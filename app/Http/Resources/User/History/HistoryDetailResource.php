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
            'id' => $this->id,
            'id_pinjaman' => $this->id_pinjaman,
            'nominal' => "Rp  " . number_format($this->nominal),
            'biaya_admin' => $this->biaya_admin . "%",
            'asuransi' => $this->asuransi,
            'jumlah_asuransi' => "Rp  " . number_format($this->jumlah_asuransi),
            'biaya_layanan' => "Rp  " . number_format($this->biaya_layanan),
            'total_biaya_admin' => "Rp  " . number_format($this->total_biaya_admin),
            'rate' => $this->rate . "%/bln",
            'jumlah_rate' => "Rp  " . number_format($this->pendapatan_bunga),
            'tenor' => $this->tenor . " bulan",
            'iuran_anggota' => "Rp  " . number_format($this->iuran_anggota),
            'angsuran_perbulan' => "Rp  " . number_format($this->total_angsuran_perbulan),
            'jumlah_yang_diterima' => "Rp  " . number_format($this->jumlah_yang_diterima),
            'status' => $this->status,
            'ttd' => $this->ttd,
            'otp' => $this->status_otp,
            'jatuh_tempo' =>  JatuhTempoResource::collection($this->jatuh_tempo),
            'datetime' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
