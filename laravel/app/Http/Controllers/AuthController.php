<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UserResource;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @OA\Info(
 *     title="Carteira Digital API",
 *     version="1.0.0",
 *     description="API para gerenciamento de carteira financeira (registro, login, usuário, logout).",
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     in="header",
 *     name="Authorization",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class AuthController extends Controller
{
    /**
     * Registra um novo usuário e cria sua carteira zerada.
     *
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Registra um usuário",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário registrado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário registrado com sucesso"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="UserA"),
     *                 @OA\Property(property="email", type="string", example="a@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->wallet()->create(['balance' => 0]);

        return response()->json([
            'message' => 'Usuário registrado com sucesso',
            'user'    => $user->only('id', 'name', 'email'),
        ], 201);
    }

    /**
     * Autentica o usuário e retorna um token Bearer.
     *
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="Login do usuário",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token gerado",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="1|abcdef123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Credenciais inválidas.")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas.'
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
        ], 200);
    }

    /**
     * Retorna os dados do usuário autenticado (incluindo wallet), identificando-o pelo token.
     *
     * @OA\Get(
     *     path="/api/user",
     *     tags={"Auth"},
     *     summary="Retorna o usuário autenticado",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuário autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Não autenticado")
     *         )
     *     )
     * )
     */
    public function user(Request $request)
    {
        
        $authHeader = $request->header('Authorization');
        if (! $authHeader || ! preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
            return response()->json(['message' => 'Não autenticado'], 401);
        }

        $token = $matches[1];
        $pat = PersonalAccessToken::findToken($token);
        if (! $pat || ! $pat->tokenable instanceof User) {
            return response()->json(['message' => 'Não autenticado'], 401);
        }

        $user = $pat->tokenable;
        $user->load('wallet');

        return new UserResource($user);
    }

    /**
     * Revoga todos os tokens do usuário (logout).
     *
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Auth"},
     *     summary="Logout do usuário",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout efetuado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout efetuado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Não autenticado")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        Log::info('Logout chamado', [
            'user'      => optional($request->user())->id,
            'hasHeader' => $request->header('Authorization'),
        ]);

        if (! $request->user()) {
            return response()->json(['message' => 'Não autenticado'], 401);
        }

        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout efetuado com sucesso',
        ], 200);
    }
}
