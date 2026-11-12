<?php

namespace App\Http\Requests\Admin\Ksp\Lender;

use Illuminate\Foundation\Http\FormRequest;

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
            'pic_internal' => 'required',
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
            'lender_features' => 'nullable|array',
            'lender_features.*.feature_id' => 'required|exists:features,id',
            'lender_features.*.pks_number' => 'nullable|string|max:255',
            'lender_features.*.pks_date' => 'nullable|date',
            'lender_features.*.pks_expired_at' => 'nullable|date|after_or_equal:lender_features.*.pks_date',
            'lender_features.*.is_active' => 'nullable|boolean',
        ];
    }
}
