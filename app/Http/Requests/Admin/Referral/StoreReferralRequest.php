<?php

namespace App\Http\Requests\Admin\Referral;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReferralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'national_id_number' => ['required', 'digits:16', Rule::unique('referrals', 'national_id_number')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'blood_type' => ['nullable', Rule::in(['A', 'B', 'AB', 'O', '-'])],
            'address' => ['nullable', 'string'],
            'rt' => ['nullable', 'string', 'max:3'],
            'rw' => ['nullable', 'string', 'max:3'],
            'province_id' => ['nullable', 'integer', 'exists:wilayah_provinsi_net,id'],
            'city_id' => ['nullable', 'integer', 'exists:wilayah_kabupaten_net,id'],
            'district_id' => ['nullable', 'integer', 'exists:wilayah_kecamatan_net,id'],
            'village_id' => ['nullable', 'integer', 'exists:wilayah_desa_net,id'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'religion' => ['nullable', Rule::in(['islam', 'christian', 'catholic', 'hindu', 'buddhist', 'confucian'])],
            'marital_status' => ['nullable', Rule::in(['single', 'married', 'divorced', 'widowed'])],
            'occupation' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:50'],
            'id_card_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'bank_id' => ['nullable', 'exists:banks,id'],
            'bank_account_number' => ['nullable', 'string', 'max:100'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
            'bank_book_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
