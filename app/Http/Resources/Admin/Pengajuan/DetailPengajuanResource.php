<?php

namespace App\Http\Resources\Admin\Pengajuan;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailPengajuanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nama' => $this->user->name,
            'nik' => $this->user->nik,
            'tanggal_terdaftar' => Carbon::parse($this->user->created_at)->format("Y-m-d"),
            'tujuan' => $this->tujuan,
            'id_pinjaman' => $this->id_pinjaman,
            'tanggal_pengajuan' => Carbon::parse($this->user->created_at)->format("Y-m-d H:i:s"),
            'status' => $this->status,
            'plafon' => "Rp" . number_format($this->nominal),
            'tenor' => $this->tenor . " Bulan",
            'percent' => $this->biaya_admin . "%",
            'biaya_admin' =>  "Rp" . number_format($this->nominal * $this->biaya_admin / 100),
            'angsuran_perbulan' => "Rp" . number_format($this->nominal / $this->tenor),
            'jumlah_diterima' => "Rp" . number_format($this->nominal - ($this->nominal * $this->biaya_admin / 100)),
        ];
    }
}
