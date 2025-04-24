<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa o fluxo completo: registro, login, depósito, transferência, listagem e reversão.
     */
    public function test_full_wallet_flow()
    {
        $registerA = $this->postJson('/api/register', [
            'name' => 'UserA',
            'email' => 'a@example.com',
            'password' => 'password123',
        ]);
        $registerA->assertStatus(201)
                  ->assertJsonStructure(['message', 'user' => ['id']]);
        $userAId = $registerA->json('user.id');

        $loginA = $this->postJson('/api/login', [
            'email' => 'a@example.com',
            'password' => 'password123',
        ]);
        $loginA->assertStatus(200)
               ->assertJsonStructure(['token']);
        $tokenA = $loginA->json('token');

        $registerB = $this->postJson('/api/register', [
            'name' => 'UserB',
            'email' => 'b@example.com',
            'password' => 'password456',
        ]);
        $registerB->assertStatus(201);
        $userBId = $registerB->json('user.id');

        $deposit = $this->withHeader('Authorization', "Bearer {$tokenA}")
                        ->postJson('/api/deposit', ['amount' => 500.00]);
        $deposit->assertStatus(201)
                ->assertJsonStructure(['message', 'transactionId', 'transaction']);
        $depositId = $deposit->json('transactionId');

        $transfer = $this->withHeader('Authorization', "Bearer {$tokenA}")
                         ->postJson('/api/transfer', [
                             'payee_id' => $userBId,
                             'amount'   => 200.00,
                         ]);
        $transfer->assertStatus(201)
                 ->assertJsonStructure(['message', 'transactionId', 'transaction']);
        $transferId = $transfer->json('transactionId');

        $this->assertDatabaseHas('wallets', [
            'user_id' => $userBId,
            'balance' => 200.00,
        ]);

        $listA = $this->withHeader('Authorization', "Bearer {$tokenA}")
                      ->getJson('/api/transactions');
        $listA->assertStatus(200)
              ->assertJsonCount(2, 'data');

        $userA = $this->withHeader('Authorization', "Bearer {$tokenA}")
                      ->getJson('/api/user')
                      ->json('data.wallet.balance');
        $this->assertEquals('300,00', $userA);

        $loginB = $this->postJson('/api/login', [
            'email' => 'b@example.com',
            'password' => 'password456',
        ]);
        $tokenB = $loginB->json('token');

        $responseB = $this->withHeader('Authorization', "Bearer {$tokenB}")
                          ->getJson('/api/user');

        Log::info('API /user B response', $responseB->json());

        $userB = $responseB->json('data.wallet.balance');
        $this->assertEquals('200,00', $userB);

        $reverse = $this->withHeader('Authorization', "Bearer {$tokenA}")
                        ->postJson("/api/reverse/{$transferId}");
        $reverse->assertStatus(201)
                ->assertJsonStructure(['message', 'transactionId', 'reversal']);

        $afterA = $this->withHeader('Authorization', "Bearer {$tokenA}")
                        ->getJson('/api/user')
                        ->json('data.wallet.balance');
        $afterB = $this->withHeader('Authorization', "Bearer {$tokenB}")
                        ->getJson('/api/user')
                        ->json('data.wallet.balance');

        $this->assertEquals('500,00', $afterA);
        $this->assertEquals('0,00',   $afterB);
    }
}
