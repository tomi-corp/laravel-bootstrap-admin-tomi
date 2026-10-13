<?php

namespace App\Http\Requests\Admin\Ksp\Lender;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddLenderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pic_lender' => 'required',
            'pic_lender_national_id_number' => 'nullable|digits:16',
            'pic_lender_birth_place' => 'nullable|string|max:100',
            'pic_lender_birth_date' => 'nullable|date',
            'pic_lender_gender' => ['nullable', Rule::in(['male', 'female'])],
            'pic_lender_blood_type' => ['nullable', Rule::in(['A', 'B', 'AB', 'O', '-'])],
            'pic_lender_address' => 'nullable|string',
            'pic_lender_rt' => 'nullable|string|max:3',
            'pic_lender_rw' => 'nullable|string|max:3',
            'pic_lender_province_id' => 'nullable|integer|exists:wilayah_provinsi_net,id',
            'pic_lender_city_id' => 'nullable|integer|exists:wilayah_kabupaten_net,id',
            'pic_lender_district_id' => 'nullable|integer|exists:wilayah_kecamatan_net,id',
            'pic_lender_village_id' => 'nullable|integer|exists:wilayah_desa_net,id',
            'pic_lender_postal_code' => 'nullable|string|max:10',
            'pic_lender_religion' => ['nullable', Rule::in(['islam', 'christian', 'catholic', 'hindu', 'buddhist', 'confucian'])],
            'pic_lender_marital_status' => ['nullable', Rule::in(['single', 'married', 'divorced', 'widowed'])],
            'pic_lender_occupation' => 'nullable|string|max:100',
            'pic_lender_nationality' => 'nullable|string|max:50',
            'pic_lender_phone_number' => 'nullable|string|max:20',
            'pic_lender_email' => 'nullable|email|max:255',
            'pic_lender_password' => 'nullable|string|min:8',
            'pic_internal' => 'required',
            'pic_internal_national_id_number' => 'nullable|digits:16',
            'pic_internal_birth_place' => 'nullable|string|max:100',
            'pic_internal_birth_date' => 'nullable|date',
            'pic_internal_gender' => ['nullable', Rule::in(['male', 'female'])],
            'pic_internal_blood_type' => ['nullable', Rule::in(['A', 'B', 'AB', 'O', '-'])],
            'pic_internal_address' => 'nullable|string',
            'pic_internal_rt' => 'nullable|string|max:3',
            'pic_internal_rw' => 'nullable|string|max:3',
            'pic_internal_province_id' => 'nullable|integer|exists:wilayah_provinsi_net,id',
            'pic_internal_city_id' => 'nullable|integer|exists:wilayah_kabupaten_net,id',
            'pic_internal_district_id' => 'nullable|integer|exists:wilayah_kecamatan_net,id',
            'pic_internal_village_id' => 'nullable|integer|exists:wilayah_desa_net,id',
            'pic_internal_postal_code' => 'nullable|string|max:10',
            'pic_internal_religion' => ['nullable', Rule::in(['islam', 'christian', 'catholic', 'hindu', 'buddhist', 'confucian'])],
            'pic_internal_marital_status' => ['nullable', Rule::in(['single', 'married', 'divorced', 'widowed'])],
            'pic_internal_occupation' => 'nullable|string|max:100',
            'pic_internal_nationality' => 'nullable|string|max:50',
            'pic_internal_phone_number' => 'nullable|string|max:20',
            'pic_internal_email' => 'nullable|email|max:255',
            'pic_internal_password' => 'nullable|string|min:8',
            'nama_lender' => 'required',
            'email_lender' => 'required|unique:lenders,email_lender',
            'telepon' => 'required|numeric',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'alamat_lengkap' => 'required',
            'nomor_pks' => 'required',
            'bank' => 'required',
            'file_pks' => 'required|mimes:pdf|max:5120',
            'logoLender' => 'required|mimes:jpeg,jpg,png|max:2048',
            'colorLender' => 'required',
            'an_rekening' => 'required',
            'no_rekening' => 'required|numeric',
        ];
    }
}
