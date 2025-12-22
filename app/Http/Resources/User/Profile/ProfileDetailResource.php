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
            'nama_lengkap' => $this->nama_lengkap,
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
            'ktp' => $this->kyc ? secureFile($this->kyc->ktp) : null,
            'selfie' => $this->kyc ? secureFile($this->kyc->selfie) : null,
            'kartu_keluarga' => $this->kartu_keluarga ? secureFile($this->kartu_keluarga) : null,
            'selfie_ktp' => $this->selfie_ktp ? secureFile($this->selfie_ktp) : null,
            'status_verifikasi_wa' => $this->whatsapp_verified_at === null ? "belum verifikasi" : "terverifikasi",
            'status_verifikasi_email' => $this->email_verified_at === null ? "belum verifikasi" : "terverifikasi",
            'status_privy' => $this->verified_privy,
            'reason_privy' => $this->reason_privy,
            'resend_privy' => $this->resend_privy,
            'update_data_privy' => $this->update_data_privy,
        ];
    }
}
