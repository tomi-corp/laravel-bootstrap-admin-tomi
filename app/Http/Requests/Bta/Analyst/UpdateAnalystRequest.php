<?php

namespace App\Http\Requests\Bta\Analyst;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnalystRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        // Pastikan user sudah login sebagai analyst
        return auth('bta')->check();
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */


    protected function prepareForValidation()
    {
        $currencyFields = [
            'plafond',
            'nilai_angsuran',
            'biaya_bank',
            'biaya_asuransi',
            'biaya_koperasi',
            'simpanan_koperasi',
            'gaji_bersih'
        ];

        $cleanValues = [];
        foreach ($currencyFields as $field) {
            if ($this->has($field)) {
                // Menghapus 'Rp', titik, spasi, atau karakter lain selain angka
                $cleanValues[$field] = preg_replace('/[^0-9]/', '', $this->$field);
            }
        }

        $this->merge($cleanValues);
    }

    public function rules(): array
    {
        return [
            // Identitas & Utama
            'id'        => 'required|exists:nasabahs,id',
            'rekomendasi'       => 'required|in:disetujui,ditolak,dikembalikan',
            'tujuan_penggunaan' => 'required|string|max:255',
            'catatan_analyst'   => 'nullable|string',

            // Data Pinjaman (Sekarang berupa angka murni karena prepareForValidation)
            'plafond'           => 'required|numeric',
            'jangka_waktu'      => 'required|integer|min:1',
            'suku_bunga'        => 'required|numeric',
            'nilai_angsuran'    => 'required|numeric',
            'gaji_bersih'       => 'required|numeric',
            'dbr'               => 'required|numeric',

            // Biaya-biaya
            'biaya_bank'        => 'required|numeric',
            'biaya_asuransi'    => 'required|numeric',
            'biaya_koperasi'    => 'required|numeric',
            'simpanan_koperasi' => 'required|numeric',

            // Tanggal
            'mulai_pinjaman'    => 'required|date',
            'akhir_pinjaman'    => 'required|date|after:mulai_pinjaman',

            // Status Dokumen (Validasi status harus accept/reject/revisi sesuai kebutuhan Anda)
            'status_npwp'              => 'required|in:approve,reject',
            'note_npwp'                => 'nullable|string',
            'status_ktp'              => 'required|in:approve,reject',
            'note_ktp'                => 'nullable|string',
            'status_id_card'          => 'required|in:approve,reject',
            'note_id_card'            => 'nullable|string',
            'status_kk'               => 'required|in:approve,reject',
            'note_kk'                 => 'nullable|string',
            'status_slip_gaji'        => 'required|in:approve,reject',
            'note_slip_gaji'          => 'nullable|string',
            'status_sk_karyawan'      => 'required|in:approve,reject',
            'note_sk_karyawan'        => 'nullable|string',
            'status_form_bta'         => 'required|in:approve,reject',
            'note_form_bta'           => 'nullable|string',
            'status_mutasi_rekening'  => 'required|in:approve,reject',
            'note_mutasi_rekening'    => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute wajib diisi.',
            'numeric'  => ':attribute harus berupa angka tanpa format titik/Rp.',
            'exists'   => 'Nasabah tidak terdaftar.',
            'in'       => 'Pilihan :attribute tidak valid.',
        ];
    }
}
