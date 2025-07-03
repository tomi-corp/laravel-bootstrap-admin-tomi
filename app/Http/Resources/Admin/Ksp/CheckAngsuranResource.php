<?php

namespace App\Http\Resources\Admin\Ksp;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckAngsuranResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'angsuran_ke' => $this->pembayaran,
            'jatuh_tempo' => $this->jatuh_tempo,
            'status' => $this->status == "lunas" ? "Lunas" : "Belum Lunas",
        ];
    }
}
