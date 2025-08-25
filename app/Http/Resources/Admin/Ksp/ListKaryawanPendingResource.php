<?php

namespace App\Http\Resources\Admin\Ksp;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListKaryawanPendingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nama' =>  $this->nama_karyawan,
            'nik' =>  $this->nik,
            'nip' =>  $this->nip,
            'whatsapp' =>  $this->whatsapp,
            'alamat' =>  $this->alamat,
            'jabatan' =>  $this->jabatan,
            'max_plafon' => "Rp" . number_format($this->max_plafon),
        ];
    }
}
