<?php

namespace App\Http\Requests\Admin\Ksp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;


class EditPerusahaanRequest extends FormRequest
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
            'id' => 'required|exists:list_perusahaans,id',
            'nama_perusahaan' => 'required',
            'telepon' => 'required|numeric',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'alamat_lengkap' => 'required',
            'payment_date' => 'required|numeric',
            'nomor_pks' => 'required|numeric',
            'status' => 'required|in:active,nonactive',
            'password' =>  ['sometimes', 'nullable', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()]
        ];
    }
}
