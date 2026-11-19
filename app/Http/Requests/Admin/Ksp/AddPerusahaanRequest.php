<?php

namespace App\Http\Requests\Admin\Ksp;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pic_perusahaan' => 'required',
            'pic_internal' => 'nullable',
            'admin_id' => 'required|exists:admins,id',
            'nama_perusahaan' => 'required',
            'email_perusahaan' => 'required|unique:list_perusahaans,email_perusahaan',
            'telepon' => 'required|numeric',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'alamat_lengkap' => 'required',
            'payment_date' => 'required|numeric',
            'nomor_pks' => 'required|alpha_num',
        ];
    }
}
