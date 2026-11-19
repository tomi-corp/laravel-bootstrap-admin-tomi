<?php

namespace App\Http\Resources\Internal\TomiDana;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListJatuhTempoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $pinjaman = $this->pinjaman;

        return [
            'id_pinjaman' => $pinjaman?->id_pinjaman ?? '-',
            'nama' => $pinjaman?->data_karyawan?->nama_karyawan ?? '-',
            'angsuran_perbulan' => $pinjaman ? 'Rp  '.number_format($pinjaman->total_angsuran_perbulan) : '-',
        ];
    }
}
