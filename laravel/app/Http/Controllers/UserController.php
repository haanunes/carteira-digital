<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;

/**
 * @OA\Tag(
 *   name="Usuários",
 *   description="Operações relacionadas aos usuários"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/users/{id}",
     *   tags={"Usuários"},
     *   summary="Retorna os dados de um usuário pelo ID",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID do usuário",
     *     required=true,
     *     @OA\Schema(type="integer", example=5)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Usuário encontrado",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *     )
     *   ),
     *   @OA\Response(response=401, description="Não autenticado"),
     *   @OA\Response(response=404, description="Usuário não encontrado")
     * )
     */
    public function show(User $user)
    {
        $user->load('wallet');
        return new UserResource($user);
    }
}
