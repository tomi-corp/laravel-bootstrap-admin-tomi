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
            'nama_perusahaan' => $this->nama,
            'nama' => $this->karyawanSingle->nama_karyawan,
            'nip' => $this->karyawanSingle->nip,
            'departement' => $this->karyawanSingle->departement,
            'jabatan' => $this->karyawanSingle->jabatan,
            'tanggal_bergabung' => Carbon::parse($this->karyawanSingle->tanggal_bergabung)->format('d M Y'),
            'status' => $this->karyawanSingle->status,
        ];
    }
}
