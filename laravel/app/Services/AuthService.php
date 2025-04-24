<?php

namespace App\Services;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Registra um novo usuário e inicializa sua carteira.
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->wallet()->create(['balance' => 0]);

        return $user;
    }

    /**
     * Valida credenciais e gera token Sanctum.
     *
     * @param string $email
     * @param string $password
     * @return string
     * @throws ValidationException
     */
    public function login(string $email, string $password): string
    {
        $user = User::where('email', $email)->first();
        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }
        return $user->createToken('api-token')->plainTextToken;
    }

    /**
     * Retorna o usuário a partir de um token Bearer.
     *
     * @param string $token
     * @return User
     * @throws ValidationException
     */
    public function userFromToken(string $token): User
    {
        $pat = PersonalAccessToken::findToken($token);
        if (! $pat || ! $pat->tokenable instanceof User) {
            throw ValidationException::withMessages([
                'token' => ['Token inválido.'],
            ]);
        }
        return $pat->tokenable;
    }

    /**
     * Revoga todos os tokens do usuário.
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
