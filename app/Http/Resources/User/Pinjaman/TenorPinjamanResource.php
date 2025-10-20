<?php

namespace App\Http\Resources\User\Pinjaman;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenorPinjamanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_tenor_pinjaman' => $this->id,
            'tenor' => $this->tenor . " bulan",
            'rate' => $this->rate . " %",
            'asuransi' => $this->asuransi . " %",
            'biaya_admin' => $this->biaya_admin . " %",
            'iuran_anggota' => "Rp  " . number_format($this->amount_pinjaman->iuran_anggota * $this->tenor)
        ];
    }
}
