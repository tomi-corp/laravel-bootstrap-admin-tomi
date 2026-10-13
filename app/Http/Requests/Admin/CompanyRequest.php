<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    private const REFERRAL_PREFIXES = [
        'tomi_dana',
        'tomi_dana_plus',
        'tomi_gawai',
        'tomi_gaji',
        'tomi_invoice',
    ];

    private const REFERRAL_FORM_RULES = [
        'birth_place' => ['nullable', 'string', 'max:100'],
        'birth_date' => ['nullable', 'date'],
        'gender' => ['nullable'],
        'blood_type' => ['nullable'],
        'address' => ['nullable', 'string'],
        'rt' => ['nullable', 'string', 'max:3'],
        'rw' => ['nullable', 'string', 'max:3'],
        'province_id' => ['nullable', 'integer', 'exists:wilayah_provinsi_net,id'],
        'city_id' => ['nullable', 'integer', 'exists:wilayah_kabupaten_net,id'],
        'district_id' => ['nullable', 'integer', 'exists:wilayah_kecamatan_net,id'],
        'village_id' => ['nullable', 'integer', 'exists:wilayah_desa_net,id'],
        'postal_code' => ['nullable', 'string', 'max:10'],
        'religion' => ['nullable'],
        'marital_status' => ['nullable'],
        'occupation' => ['nullable', 'string', 'max:100'],
        'nationality' => ['nullable', 'string', 'max:50'],
        'bank_id' => ['nullable', 'exists:banks,id'],
        'bank_account_number' => ['nullable', 'string', 'max:100'],
        'bank_account_name' => ['nullable', 'string', 'max:255'],
        'is_active' => ['nullable', 'boolean'],
        'notes' => ['nullable', 'string'],
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->route('company')?->id;

        $rules = [
            'id_lender' => ['nullable', 'exists:lenders,id'],
            'pic_perusahaan' => ['required', 'string', 'max:255'],
            'pic_internal' => ['required', 'string', 'max:255'],
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
            'payment_date' => ['required', 'integer', 'min:1', 'max:31'],
            'h_min_jatuh_tempo' => ['required', 'integer', 'min:0', 'max:31'],
            'status' => ['required', Rule::in(['active', 'nonactive'])],
            'active_profesional_fee' => ['required', Rule::in(['active', 'nonactive'])],
            'tomi_dana_referral_1_id' => ['nullable', 'integer', Rule::exists('referrals', 'id')->where('is_active', true)],
            'tomi_dana_referral_2_id' => ['nullable', 'integer', Rule::exists('referrals', 'id')->where('is_active', true)],
            'tomi_dana_plus_referral_1_id' => ['nullable', 'integer', Rule::exists('referrals', 'id')->where('is_active', true)],
            'tomi_dana_plus_referral_2_id' => ['nullable', 'integer', Rule::exists('referrals', 'id')->where('is_active', true)],
            'tomi_gawai_referral_1_id' => ['nullable', 'integer', Rule::exists('referrals', 'id')->where('is_active', true)],
            'tomi_gawai_referral_2_id' => ['nullable', 'integer', Rule::exists('referrals', 'id')->where('is_active', true)],
            'tomi_gaji_referral_1_id' => ['nullable', 'integer', Rule::exists('referrals', 'id')->where('is_active', true)],
            'tomi_gaji_referral_2_id' => ['nullable', 'integer', Rule::exists('referrals', 'id')->where('is_active', true)],
            'tomi_invoice_referral_1_id' => ['nullable', 'integer', Rule::exists('referrals', 'id')->where('is_active', true)],
            'tomi_invoice_referral_2_id' => ['nullable', 'integer', Rule::exists('referrals', 'id')->where('is_active', true)],
            'active_set_pinjaman' => ['required', Rule::in(['active', 'nonactive'])],
            'active_tanggal' => ['required', 'integer', 'min:0', 'max:31'],
            'max_plafon' => ['nullable', 'numeric', 'min:0'],
        ];

        foreach (self::REFERRAL_PREFIXES as $prefix) {
            foreach ([1, 2] as $index) {
                $rules["{$prefix}_referral_{$index}_name"] = ['nullable', 'string', 'max:255'];
                $rules["{$prefix}_referral_{$index}_national_id_number"] = [
                    "required_with:{$prefix}_referral_{$index}_name",
                    'nullable',
                    'digits:16',
                ];
                $rules["{$prefix}_referral_{$index}_phone_number"] = ['nullable', 'string', 'max:30'];
                $rules["{$prefix}_referral_{$index}_email"] = ['nullable', 'email', 'max:255'];

                foreach (self::REFERRAL_FORM_RULES as $field => $fieldRules) {
                    $rules["{$prefix}_referral_{$index}_{$field}"] = $fieldRules;
                }
            }
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach (self::REFERRAL_PREFIXES as $prefix) {
                $first = $this->input("{$prefix}_referral_1_id");
                $second = $this->input("{$prefix}_referral_2_id");

                if ($first && $second && (string) $first === (string) $second) {
                    $validator->errors()->add(
                        "{$prefix}_referral_2_id",
                        'Refferal 2 harus berbeda dari Refferal 1.'
                    );
                }
            }
        });
    }
}
