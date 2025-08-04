<?php

namespace App\Http\Resources\User\History;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JatuhTempoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'pembayaran' => $this->pembayaran,
            'jatuh_tempo' => $this->jatuh_tempo,
            'status' => $this->status
        ];
    }
}
