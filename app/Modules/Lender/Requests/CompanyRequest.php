<?php

namespace App\Modules\Lender\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $uppercaseFields = [
            'pic_perusahaan',
            'pic_internal',
            'nama',
            'alamat_lengkap',
            'bank',
            'no_rekening',
            'atas_nama_rekening',
            'nama_direktur_utama',
            'telp',
            'nomor_pks',
        ];

        $data = [];

        foreach ($uppercaseFields as $field) {
            if ($this->filled($field)) {
                $data[$field] = mb_strtoupper((string) $this->input($field), 'UTF-8');
            }
        }

        if ($this->has('max_plafon')) {
            $data['max_plafon'] = preg_replace('/\D/', '', (string) $this->input('max_plafon'));
        }

        if ($data !== []) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $companyId = $this->route('company')?->id;

        return [
            'id_lender' => ['nullable', 'exists:lenders,id'],
            'pic_perusahaan' => ['required', 'string', 'max:255'],
            'pic_internal' => ['nullable', 'string', 'max:255'],
            'nama' => ['required', 'string', 'max:255'],
            'email_perusahaan' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('list_perusahaans', 'email_perusahaan')->ignore($companyId)->whereNull('deleted_at'),
            ],
            'id_provinsi' => ['required', 'integer', 'exists:wilayah_provinsi_net,id'],
            'id_kabupaten' => ['required', 'integer', 'exists:wilayah_kabupaten_net,id'],
            'id_kecamatan' => ['required', 'integer', 'exists:wilayah_kecamatan_net,id'],
            'id_desa' => ['required', 'integer', 'exists:wilayah_desa_net,id'],
            'alamat_lengkap' => ['required', 'string', 'max:255'],
            'bank' => ['nullable', 'string', 'max:255'],
            'no_rekening' => ['nullable', 'string', 'max:255'],
            'atas_nama_rekening' => ['nullable', 'string', 'max:255'],
            'nama_direktur_utama' => ['nullable', 'string', 'max:255'],
            'telp' => ['required', 'string', 'max:255'],
            'nomor_pks' => ['required', 'string', 'max:255'],
            'payment_date' => ['nullable', 'integer', 'min:1', 'max:31'],
            'h_min_jatuh_tempo' => ['nullable', 'integer', 'min:0', 'max:31'],
            'status' => ['required', Rule::in(['active', 'nonactive'])],
            'active_profesional_fee' => ['nullable', Rule::in(['active', 'nonactive'])],
            'active_set_pinjaman' => ['nullable', Rule::in(['active', 'nonactive'])],
            'active_tanggal' => ['nullable', 'integer', 'min:0', 'max:31'],
            'max_plafon' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
