<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->route('company')?->id;

        return [
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
    }
}
