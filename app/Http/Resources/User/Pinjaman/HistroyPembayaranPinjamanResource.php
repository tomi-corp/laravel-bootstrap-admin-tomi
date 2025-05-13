<?php

namespace App\Http\Resources\User\Pinjaman;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistroyPembayaranPinjamanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'pembayaran_ke' => $this->pembayaran_ke,
            'nominal' => "Rp" . number_format($this->nominal),
            'tanggal_pembayaran' => $this->created_at,
        ];
    }
}
