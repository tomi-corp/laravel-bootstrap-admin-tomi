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
        // Mengambil data dari relasi PrivyDigitalIdSession (misal: relasinya bernama privySession atau privy)
        // Sesuaikan nama relasi dengan yang ada di Model User Anda.
        $privySession = $this->privySession; // atau $this->privy

        $isPrivyVerified = $privySession && $privySession->status === 'verified'; // Atau sesuaikan kondisi status terverifikasi

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
            'status_verifikasi_wa' => $this->whatsapp_verified_at === null ? 'belum verifikasi' : 'terverifikasi',
            'status_verifikasi_email' => $this->email_verified_at === null ? 'belum verifikasi' : 'terverifikasi',

            // Data Privy diambil dari tabel/model PrivyDigitalIdSession
            'status_privy' => $privySession ? $privySession->status : null,
            'status_privy_terbaru' => $isPrivyVerified ? 'terverifikasi' : 'belum terverifikasi',
            'privy_verified' => $isPrivyVerified,
            'privy_id' => $privySession ? $privySession->privy_id : null,
            'reference_number' => $privySession ? $privySession->reference_number : null,
            'session_url' => $privySession ? $privySession->session_url : null,
            'reason_privy' => $this->reason_privy, // Tetap ambil dari user jika kolomnya masih ada di user
            'resend_privy' => $this->resend_privy,
            'update_data_privy' => $this->update_data_privy,
        ];
    }
}
