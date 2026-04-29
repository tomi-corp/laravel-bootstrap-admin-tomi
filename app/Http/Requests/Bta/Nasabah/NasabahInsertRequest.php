<?php

namespace App\Http\Requests\Bta\Nasabah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;


class NasabahInsertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Sesuaikan dengan kebijakan Anda (misalnya, hanya admin yang bisa membuat)
        // Untuk sementara, biarkan true
        return true;
    }

    /**
     * Prepare the data for validation.
     * Membersihkan format rupiah menjadi angka sebelum validasi.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $fieldsToClean = [
            'plafond',
            'nilai_angsuran',
            'biaya_bank',
            'biaya_asuransi',
            'biaya_koperasi',
            'simpanan_koperasi',
            'gaji_bersih',
        ];

        foreach ($fieldsToClean as $field) {
            $value = $this->input($field);
            if ($value !== null && $value !== '') {
                // Misalnya: "Rp 10.000.000" -> "10000000"
                $cleanedValue = $this->cleanRupiahFormat($value);
                // Set ulang nilai di request
                $this->merge([$field => $cleanedValue]);
            }
        }
    }

    /**
     * Clean rupiah format string to integer/float.
     *
     * @param string $value
     * @return float|null
     */
    private function cleanRupiahFormat($value)
    {
        if (!is_string($value)) {
            return $value; // Biarkan jika bukan string
        }

        // Hapus "Rp " di awal
        $cleaned = Str::remove('Rp ', $value, false);
        // Hapus titik pemisah ribuan
        $cleaned = str_replace('.', '', $cleaned);
        // Hapus spasi ekstra
        $cleaned = trim($cleaned);

        // Konversi ke float
        return (float) $cleaned;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            '_token' => 'required|string',

            // Identitas
            'nama_lengkap' => 'required|string|max:255',
            'nama_singkat' => 'nullable|string|max:100',
            'no_ktp' => 'required|string|size:16|unique:nasabahs,no_ktp',
            'no_npwp' => 'required|string|size:16|unique:nasabahs,no_npwp',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'nama_ibu' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
            'agama' => 'required|in:Islam,Katolik,Kristen,Buddha,Konghucu',
            'pendidikan' => 'required|in:SD,SMP,SMA,D1,D3,D4,S1,S2,S3',
            'status_perkawinan' => 'required|in:Kawin,Tidak Kawin,Cerai Mati,Cerai Hidup',

            // Pasangan (wajib jika kawin)
            'nama_pasangan' => 'required_if:status_perkawinan,Kawin|nullable|string|max:255',
            'tgl_lahir_pasangan' => 'required_if:status_perkawinan,Kawin|nullable|date',

            // Alamat
            'alamat_rumah' => 'required|string',
            'provinsi' => 'required|exists:wilayah_provinsi_net,id',
            'kota' => 'required|exists:wilayah_kabupaten_net,id',
            'kecamatan' => 'required|exists:wilayah_kecamatan_net,id',
            'kelurahan' => 'required|exists:wilayah_desa_net,id',
            'kodePos' => 'required|string|size:5',
            'kode_dati' => 'required',

            // Pekerjaan
            'nama_perusahaan' => 'required|string|max:255',
            'bidang_usaha' => 'required|string|max:255',
            'alamat_kerja' => 'required|string',
            'nip' => 'nullable|string|max:50',
            'tgl_akhir_kontrak' => 'required_if:status_pegawai,Karyawan Tidak Tetap|nullable|date',

            'status_pegawai' => 'required|in:Karyawan Tetap,Karyawan Tidak Tetap,Pensiun',
            'nama_perusahaan' => 'required_unless:status_pegawai,Pensiun|nullable|string|max:255',
            'bidang_usaha' => 'required_unless:status_pegawai,Pensiun|nullable|string|max:255',
            'alamat_kerja' => 'required_unless:status_pegawai,Pensiun|nullable|string',
            'nip' => 'required|nullable|string|max:50',
            'tgl_akhir_kontrak' => 'required_if:status_pegawai,Karyawan Tidak Tetap|nullable|date',



            // Bank
            'bankSelect' => 'required|exists:banks,id',
            'nama_pemilik_rek' => 'required|string|max:255',
            'no_rek' => 'required|string|max:50|unique:nasabahs,no_rek',

            // KTA Multiguna - Sekarang input sudah berupa angka karena prepareForValidation
            'plafond' => 'required|numeric|min:0',
            'tipe_bunga' => 'required|in:flat,efektif',
            'jangka_waktu' => 'required|integer|min:1',
            'suku_bunga' => 'required|numeric|min:0',
            'nilai_angsuran' => 'required|numeric|min:0',
            'biaya_bank' => 'required|numeric|min:0',
            'biaya_asuransi' => 'required|numeric|min:0',
            'biaya_koperasi' => 'required|numeric|min:0',
            'simpanan_koperasi' => 'required|numeric|min:0',
            'tujuan_penggunaan' => 'required|in:Konsumsi,Modal Kerja,Investasi',
            'gaji_bersih' => 'required|numeric|min:0',
            'mulai_pinjaman' => 'required|date',
            'akhir_pinjaman' => 'required|date|after_or_equal:mulai_pinjaman',
            'dbr' => 'required|numeric|min:0', // Harusnya input angka, bukan persen?

            // Upload File
            'upload_ktp' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            // 'upload_selfie' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'upload_npwp' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'upload_kk' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'upload_sk' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'upload_slip' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'upload_buku_tabungan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'upload_mutasi' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'upload_slik' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'upload_kredit' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'upload_bta' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'upload_checklist' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',

            'upload_video_interview' => 'nullable',
            'upload_video_asuransi'  => 'nullable',


            // 🎬 VIDEO: WAJIB HANYA JIKA STATUS_PEGAWAI = 'Pensiun'
            // 'upload_video_interview' => $this->status_pegawai === 'Pensiun'
            //     ? 'required|file|mimes:mp4,mov,avi,webm|max:51200'
            //     : 'nullable|file|mimes:mp4,mov,avi,webm|max:51200',

            // 'upload_video_asuransi' => $this->status_pegawai === 'Pensiun'
            //     ? 'required|file|mimes:mp4,mov,avi,webm|max:51200'
            //     : 'nullable|file|mimes:mp4,mov,avi,webm|max:51200',


        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'no_ktp.size' => 'NIK harus 16 digit.',
            'no_ktp.unique' => 'NIK ini sudah terdaftar.',
            'no_npwp.size' => 'NPWP harus 16 digit.',
            'no_npwp.unique' => 'NPWP ini sudah terdaftar.',
            'required_if' => ':attribute wajib diisi karena status perkawinan adalah Kawin.',
            'max' => ':attribute maksimal :max karakter.',
            'min' => ':attribute minimal :min.',
            'mimes' => 'Format file :attribute hanya boleh JPG, JPEG, PNG, atau PDF.',
            'max.file' => 'Ukuran file :attribute maksimal 5MB.',
            'unique' => ':attribute ini sudah terdaftar.',
            'after_or_equal' => 'Tanggal akhir pinjaman harus sama atau setelah tanggal mulai pinjaman.',
            'numeric' => ':attribute harus berupa angka.',

            'upload_video_interview.required' => 'Video Interview wajib diupload untuk nasabah Pensiun',
            'upload_video_asuransi.required'  => 'Video Asuransi wajib diupload untuk nasabah Pensiun',
            'upload_video_interview.mimes'    => 'Format Video Interview harus MP4, MOV, AVI, atau WEBM',
            'upload_video_asuransi.mimes'     => 'Format Video Asuransi harus MP4, MOV, AVI, atau WEBM',
            'upload_video_interview.max'      => 'Ukuran Video Interview maksimal 50 MB',
            'upload_video_asuransi.max'       => 'Ukuran Video Asuransi maksimal 50 MB',
        ];
    }
}
