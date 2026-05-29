<?php

namespace App\Http\Requests\Bta\Analyst;

use App\Models\nasabah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


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
        $nasabah = nasabah::where('id', $this->id)->first();

        $statusPegawai = $nasabah?->status_pegawai ?? '';

        // Base rules yang selalu berlaku
        $rules = [
            // Identitas & Utama
            'id'                     => 'required|exists:nasabahs,id',
            'rekomendasi'            => 'required|in:disetujui,ditolak,dikembalikan',
            'tujuan_penggunaan'      => 'required|string|max:255',
            'catatan_analyst'        => 'nullable|string',

            // Data Pinjaman
            'plafond'                => 'required|numeric',
            'tipe_bunga'             => 'required|in:flat,efektif',
            // 'jangka_waktu'           => 'required|integer|min:1',
            'jangka_waktu'           => 'nullable|integer|min:1',
            'suku_bunga'             => 'required|numeric',
            // 'nilai_angsuran'         => 'required|numeric',
            'nilai_angsuran'         => 'nullable|numeric',
            'gaji_bersih'            => 'required|numeric',
            'dbr'                    => 'required|numeric',

            // Biaya-biaya
            'biaya_bank'             => 'required|numeric',
            'biaya_asuransi'         => 'required|numeric',
            'biaya_koperasi'         => 'required|numeric',
            'simpanan_koperasi'      => 'required|numeric',

            // Tanggal
            'tanggal_pengajuan'       => 'required|date|after_or_equal:' . now()->subWeek()->toDateString() . '|before_or_equal:' . now()->addWeek()->toDateString(),
            // 'mulai_pinjaman'         => 'required|date',
            // 'akhir_pinjaman'         => 'required|date|after:mulai_pinjaman',
            'mulai_pinjaman'         => 'nullable|date',
            'akhir_pinjaman'         => 'nullable|date|after:mulai_pinjaman',

            // Status Dokumen - Wajib untuk SEMUA status
            'status_npwp'            => 'required|in:approve,reject',
            'note_npwp'              => 'nullable|string',
            'status_ktp'             => 'required|in:approve,reject',
            'note_ktp'               => 'nullable|string',
            // 'status_id_card'         => 'required|in:approve,reject',
            // 'note_id_card'           => 'nullable|string',
            'status_kk'              => 'required|in:approve,reject',
            'note_kk'                => 'nullable|string',

            // Status Dokumen - Conditional (Slip Gaji & SK Karyawan)
            'status_sk_karyawan'     => 'required|in:approve,reject',
            'note_sk_karyawan'       => 'nullable|string',
            'status_form_bta'        => 'required|in:approve,reject',
            'note_form_bta'          => 'nullable|string',
            'status_mutasi_rekening' => 'required|in:approve,reject',
            'note_mutasi_rekening'   => 'nullable|string',
        ];

        // ✅ CONDITIONAL VALIDATION: Slip Gaji
        // Wajib hanya jika status_pegawai BUKAN 'Pensiun'
        if ($statusPegawai !== 'Pensiun') {
            $rules['status_slip_gaji'] = ['required', Rule::in(['approve', 'reject'])];
            $rules['note_slip_gaji']   = 'nullable|string|max:255';
        } else {
            // Opsional untuk Pensiun
            $rules['status_slip_gaji'] = ['nullable', Rule::in(['approve', 'reject', null])];
            $rules['note_slip_gaji']   = 'nullable|string|max:255';
        }

        if (!blank($nasabah?->id_card)) {

            $rules['status_id_card'] = [
                'required',
                Rule::in(['approve', 'reject'])
            ];

            $rules['note_id_card'] = 'nullable|string|max:255';
        } else {

            // 👉 INI KUNCI FIX KAMU
            $rules['status_id_card'] = 'nullable';

            $rules['note_id_card'] = 'nullable|string|max:255';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'required' => ':attribute wajib diisi.',
            'numeric'  => ':attribute harus berupa angka tanpa format titik/Rp.',
            'exists'   => 'Nasabah tidak terdaftar.',
            'in'       => 'Pilihan :attribute tidak valid.',
            'date'     => ':attribute harus berupa tanggal yang valid.',
            'after'    => 'Tanggal akhir pinjaman harus setelah tanggal mulai.',
            'tanggal_pengajuan.after_or_equal' => 'Tanggal pengajuan tidak boleh kurang dari 1 minggu dari tanggal hari ini.',
            'tanggal_pengajuan.before_or_equal' => 'Tanggal pengajuan tidak boleh lebih dari 1 minggu dari tanggal hari ini.',
        ];
    }
}
