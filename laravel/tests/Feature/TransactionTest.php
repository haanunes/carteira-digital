<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Wallet;
use App\Services\TransactionService;
use App\Models\Transaction;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    private TransactionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(TransactionService::class);
    }

    /** @test */
    public function it_can_perform_a_deposit()
    {
        $user = User::factory()->create();
        $user->wallet()->create(['balance' => 0]);

        $tx = $this->service->deposit($user, 150.75);

        $this->assertInstanceOf(Transaction::class, $tx);
        $this->assertEquals('deposit', $tx->type);
        $this->assertEquals(150.75, $tx->amount);
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'balance' => 150.75,
        ]);
    }

    /** @test */
    public function it_can_perform_a_transfer()
    {
        $payer = User::factory()->create();
        $payer->wallet()->create(['balance' => 500]);
        $payee = User::factory()->create();
        $payee->wallet()->create(['balance' => 0]);

        $tx = $this->service->transfer($payer, $payee, 200);

        $this->assertInstanceOf(Transaction::class, $tx);
        $this->assertEquals('transfer', $tx->type);
        $this->assertEquals(200, $tx->amount);
        $this->assertDatabaseHas('wallets', [
            'user_id' => $payer->id,
            'balance' => 300,
        ]);
        $this->assertDatabaseHas('wallets', [
            'user_id' => $payee->id,
            'balance' => 200,
        ]);
    }

    /** @test */
    public function it_can_reverse_a_deposit_transaction()
    {
        $user = User::factory()->create();
        $user->wallet()->create(['balance' => 100]);
        $tx = Transaction::create([
            'payer_id' => null,
            'payee_id' => $user->id,
            'amount'   => 100,
            'type'     => 'deposit',
            'status'   => 'completed',
        ]);

        $reverseTx = $this->service->reverse($tx);

        $this->assertInstanceOf(Transaction::class, $reverseTx);
        $this->assertEquals('reversal', $reverseTx->type);
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'balance' => 0,
        ]);
    }

    /** @test */
    public function it_can_reverse_a_transfer_transaction()
    {
        $payer = User::factory()->create();
        $payer->wallet()->create(['balance' => 300]);
        $payee = User::factory()->create();
        $payee->wallet()->create(['balance' => 200]);
        $tx = Transaction::create([
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount'   => 200,
            'type'     => 'transfer',
            'status'   => 'completed',
        ]);

        $reverseTx = $this->service->reverse($tx);

        $this->assertEquals('reversal', $reverseTx->type);
        $this->assertDatabaseHas('wallets', [
            'user_id' => $payer->id,
            'balance' => 500,
        ]);
        $this->assertDatabaseHas('wallets', [
            'user_id' => $payee->id,
            'balance' => 0,
        ]);
    }

    /** @test */
    public function it_can_get_transaction_history()
    {
        $user = User::factory()->create();
        $user->wallet()->create(['balance' => 0]);
        
        Transaction::factory()->count(3)->create(['payee_id' => $user->id]);

        $history = $this->service->history($user);

        $this->assertCount(3, $history);
        $this->assertEquals($history->first()->payee_id, $user->id);
    }
}
