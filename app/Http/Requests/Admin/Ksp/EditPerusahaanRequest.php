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
            'pic_perusahaan' => 'required',
            'pic_internal' => 'required',
            'nama_perusahaan' => 'required',
            'id_lender' => 'nullable|exists:lenders,id',
            'email_perusahaan' => 'required|email|unique:list_perusahaans,email_perusahaan,' . $this->id,
            'telepon' => 'required|numeric',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'alamat_lengkap' => 'required',
            'payment_date' => 'required|numeric',
            'h_min_jatuh_tempo' => 'required|integer|min:0',
            'active_profesional_fee' => 'required|in:active,nonactive',
            'active_set_pinjaman' => 'required|in:active,nonactive',
            'active_tanggal' => 'required|integer|min:0',
            'max_plafon' => 'nullable|numeric|min:0',
            'nomor_pks' => 'nullable|string|max:255',
            'feature_id' => 'nullable|exists:features,id',
            'pks_number' => 'nullable|required_with:feature_id|string|max:150',
            'pks_date' => 'nullable|required_with:feature_id|date',
            'pks_expired_at' => 'nullable|date|after_or_equal:pks_date',
            'is_active' => 'nullable|boolean',
            'status' => 'required|in:active,nonactive',
        ];
    }
}
