<?php

namespace App\Modules\Internal\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LenderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $lenderId = $this->route('lender')?->id;

        return [
            'pic_lender' => ['required', 'string', 'max:255'],
            'pic_internal' => ['required', 'string', 'max:255'],
            'nama' => ['required', 'string', 'max:255'],
            'email_lender' => [
                'required',
                'email',
                'max:255',
                Rule::unique('lenders', 'email_lender')->ignore($lenderId)->whereNull('deleted_at'),
            ],
            'id_provinsi' => ['required', 'integer', 'exists:wilayah_provinsi_net,id'],
            'id_kabupaten' => ['required', 'integer', 'exists:wilayah_kabupaten_net,id'],
            'id_kecamatan' => ['required', 'integer', 'exists:wilayah_kecamatan_net,id'],
            'id_desa' => ['required', 'integer', 'exists:wilayah_desa_net,id'],
            'alamat_lengkap' => ['required', 'string', 'max:255'],
            'telp' => ['required', 'string', 'max:255'],
            'nomor_pks' => ['required', 'string', 'max:255'],
            'id_bank' => ['required', 'integer', 'exists:banks,id'],
            'nama_rekening' => ['required', 'string', 'max:255'],
            'no_rekening' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'status' => ['required', Rule::in(['active', 'nonactive'])],
        ];
    }
}
