<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *   schema="TransactionResource",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=10),
 *   @OA\Property(property="type", type="string", example="deposit"),
 *   @OA\Property(property="amount", type="string", example="100,00"),
 *   @OA\Property(property="status", type="string", example="completed"),
 *   @OA\Property(property="payer_id", type="integer", nullable=true, example=null),
 *   @OA\Property(property="payee_id", type="integer", example=1),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-23T15:00:00Z")
 * )
 */
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'type'       => $this->type->label(),   
            'amount'     => number_format($this->amount, 2, ',', '.'),
            'status'     => $this->status->label(),   
            'payer_id'   => $this->payer_id,
            'payee_id'   => $this->payee_id,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
