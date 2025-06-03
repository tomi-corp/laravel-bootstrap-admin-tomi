<?php

namespace App\Http\Resources\User\Pinjaman;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistroyPinjamanResource extends JsonResource
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
            'nominal_pinjaman' => "Rp  " . number_format($this->nominal),
            'tenor' => $this->tenor . " Bulan",
            'biaya_admin' => $this->biaya_admin . " %",
            'status' => $this->status,
            'tujuan' => $this->tujuan ? $this->tujuan : "-",
            'note' => $this->note ? $this->note : "-",
            'tanggal_pengajuan' => $this->created_at,
            'history_pembayaran' => HistroyPembayaranPinjamanResource::collection($this->pembayaran_pinjaman)
        ];
    }
}
