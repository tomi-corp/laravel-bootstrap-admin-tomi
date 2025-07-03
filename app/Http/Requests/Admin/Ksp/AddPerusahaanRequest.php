<?php

namespace App\Http\Requests\Admin\Ksp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;


class AddPerusahaanRequest extends FormRequest
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
            'unique_code' => 'required|digits:4|unique:list_perusahaans,unique_code',
            'pic_perusahaan' => 'required',
            'pic_internal' => 'required',
            'nama_perusahaan' => 'required',
            'email_perusahaan' => 'required|unique:list_perusahaans,email_perusahaan',
            'telepon' => 'required|numeric',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'alamat_lengkap' => 'required',
            'payment_date' => 'required|numeric',
            'nomor_pks' => 'required|numeric',
            'bank' => 'required|in:BCA,BNI,BRI,BJB,BSI,BNC,MANDIRI,CIMB,BSS,PERMATA,HANA',
        ];
    }
}
