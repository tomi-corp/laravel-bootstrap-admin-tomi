<?php

namespace App\Http\Resources\User\History;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_pinjaman' => $this->id_pinjaman,
            'nominal' => "Rp  " . number_format($this->nominal),
            'biaya_admin' => $this->biaya_admin . "%",
            'total_biaya_admin' => "Rp  " . number_format($this->total_biaya_admin),
            'rate' => $this->rate . "%",
            'tenor' => $this->tenor . " bulan",
            'iuran_anggota' => "Rp  " . number_format($this->iuran_anggota),
            'angsuran_perbulan' => "Rp  " . number_format($this->total_angsuran_perbulan),
            'jumlah_yang_diterima' => "Rp  " . number_format($this->jumlah_yang_diterima),
            'status' => $this->status,
            'ttd' => $this->ttd,
            'jatuh_tempo' =>  Carbon::parse($this->jatuh_tempo->jatuh_tempo)->format('Y-m-d'),
            'datetime' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
