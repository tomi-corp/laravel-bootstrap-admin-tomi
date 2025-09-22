<?php

namespace App\Http\Requests\Admin\Lender;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLenderRequest extends FormRequest
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
            'email_lender' => "required|unique:lenders,email_lender," . $this->id,
            'telepon' => 'required|numeric',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'alamat_lengkap' => 'required',
            'nomor_pks' => 'required',
            'bank' => 'required',
            'file_pks' => 'sometimes|nullable|mimes:pdf|max:5120',
            'an_rekening' => 'required',
            'no_rekening' => 'required|numeric',
        ];
    }
}
