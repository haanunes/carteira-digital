<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'       => $this->id,
            'balance'  => number_format($this->balance, 2, ',', '.'),
            'updated'  => $this->updated_at->toDateTimeString(),
        ];
    }
}
