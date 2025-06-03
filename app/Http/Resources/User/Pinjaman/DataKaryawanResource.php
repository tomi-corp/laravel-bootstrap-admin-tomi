<?php

namespace App\Http\Resources\User\Pinjaman;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DataKaryawanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nama_perusahaan' => $this->perusahaan->nama,
            'nama' => $this->nama_karyawan,
            'nip' => $this->nip,
            'departement' => $this->departement,
            'jabatan' => $this->jabatan,
            'tanggal_bergabung' => Carbon::parse($this->tanggal_bergabung)->format('d M Y'),
            'status' => $this->status,
        ];
    }
}
