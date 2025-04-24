<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa que um usuário pode se registrar com dados válidos.
     */
    public function test_user_can_register_with_valid_data()
    {
        $response = $this->postJson('/api/register', [
            'name'     => 'Teste Usuário',
            'email'    => 'teste@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'user' => ['id','name','email'],
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'teste@example.com'
        ]);
    }

    /**
     * Testa que não é possível registrar com dados inválidos.
     */
    public function test_user_cannot_register_with_invalid_data()
    {
        $response = $this->postJson('/api/register', [
            'name'     => '',
            'email'    => 'invalido',
            'password' => '123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors' => ['name','email','password'],
                 ]);
    }

    /**
     * Testa que um usuário pode fazer login com credenciais válidas.
     */
    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    /**
     * Testa que o login falha com credenciais inválidas.
     */
    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Credenciais inválidas.']);
    }

    /**
     * Testa que logout requer autenticação e funciona corretamente.
     */
    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logout efetuado com sucesso']);
    }

    /**
     * Testa que rotas protegidas exigem autenticação.
     */
    public function test_protected_route_requires_authentication()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Não autenticado']);
    }
}
