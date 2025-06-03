<?php

namespace App\Http\Resources\User\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'nohp' => $this->nohp,
            'email' => $this->email,
            'dob' => $this->dob,
            'point' => $this->point,

            'nik' => $this->nik,
            'nip' => $this->nip,
            'nama_perusahaan' => $this->nama_perusahaan,
            'jabatan' => $this->jabatan,
            'penghasilan' => $this->penghasilan,
            'slip_gaji' => $this->slip_gaji,
            'ktp' => $this->ktp,
            'selfie_ktp' => $this->selfie_ktp,

            'status_akun' => $this->email_verified_at === null ? "belum verifikasi" : "terverifikasi",
        ];
    }
}
