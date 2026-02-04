<?php

namespace App\Http\Requests\Bta\Nasabah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NasabahInsertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // === IDENTITAS UTAMA ===
            'nama_lengkap' => 'required|string|max:255',
            'nama_singkat' => 'nullable|string|max:100',
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'nama_ibu' => 'required|string|max:255',
            'no_telp' => 'required|regex:/^08[0-9]{9,11}$/',
            'agama' => ['required', Rule::in(['Islam', 'Katolik', 'Kristen', 'Buddha', 'Konghucu'])],
            'pendidikan_terakhir' => ['required', Rule::in(['SD', 'SMP', 'SMA', 'D1', 'D3', 'D4', 'S1', 'S2', 'S3'])],
            'status_perkawinan' => ['required', Rule::in(['Kawin', 'Tidak Kawin'])],

            // === DATA PASANGAN (kondisional) ===
            'nama_pasangan' => 'required_if:status_perkawinan,Kawin|string|max:255',
            'tgl_lahir_pasangan' => 'required_if:status_perkawinan,Kawin|date|before_or_equal:today',

            // === ALAMAT ===
            'alamat_rumah' => 'required|string',
            'provinsi' => 'required|string',
            'kota' => 'required|string',
            'kecamatan' => 'required|string',
            'kelurahan' => 'required|string',
            'kode_pos' => 'required|digits:5',
            'kode_dati' => 'required',
            'no_ktp' => 'required',
            'no_npwp' => 'required',

            // === PEKERJAAN ===
            'nama_perusahaan' => 'required|string|max:255',
            'bidang_usaha' => 'required|string|max:255',
            'alamat_kerja' => 'required|string',
            'nip' => 'nullable|string|max:50',
            'status_pegawai' => ['required', Rule::in(['Karyawan Tetap', 'Karyawan Tidak Tetap'])],
            'tgl_akhir_kontrak' => 'required_if:status_pegawai,Karyawan Tidak Tetap|date|after:today',

            // === BANK ===
            'bank_id' => 'required',
            'no_rek' => 'required|numeric|digits_between:10,20',
            'nama_pemilik_rek' => 'required|string|max:255|same:nama_lengkap',

            // === FILE UPLOADS (WAJIB) ===
            'ktp' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120', // 5120 KB = 5 MB
            'selfie' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'kk' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'slip_gaji' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'buku_tabungan' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',

            // === FILE UPLOADS (OPSIONAL) ===
            'npwp' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'id_card' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'sk_karyawan' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'mutasi_rekening' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'form_slik' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'form_kredit' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'form_bta' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'form_checklist' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'nama_pemilik_rek.same' => 'Nama pemilik rekening harus sama dengan nama lengkap.',
            'tgl_akhir_kontrak.after' => 'Tanggal akhir kontrak harus setelah hari ini.',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh di masa depan.',
            'tgl_lahir_pasangan.before_or_equal' => 'Tanggal lahir pasangan tidak boleh di masa depan.',
        ];
    }
}
