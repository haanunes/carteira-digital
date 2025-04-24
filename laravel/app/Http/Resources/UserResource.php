<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="UserResource",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="João Silva"),
 *   @OA\Property(property="email", type="string", format="email", example="joao@example.com"),
 *   @OA\Property(property="wallet", type="object",
 *       @OA\Property(property="id", type="integer", example=1),
 *       @OA\Property(property="balance", type="string", example="100,00"),
 *       @OA\Property(property="updated", type="string", format="date-time", example="2025-04-23 15:00:00")
 *   ),
 *   @OA\Property(property="joined", type="string", format="date-time", example="2025-04-23 15:00:00")
 * )
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'email'   => $this->email,
            'wallet'  => new WalletResource($this->whenLoaded('wallet')),
            'joined'  => $this->created_at->toDateTimeString(),
        ];
    }
}
