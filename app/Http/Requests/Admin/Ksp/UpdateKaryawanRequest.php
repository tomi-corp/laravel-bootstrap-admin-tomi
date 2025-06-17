<?php

namespace App\Http\Requests\Admin\Ksp;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKaryawanRequest extends FormRequest
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
            'id' => 'required|exists:list_data_ksps,id',
            'nama_karyawan' => 'required',
            'tanggal_bergabung' => 'required|date',
            'nik' => 'required|numeric|unique:list_data_ksps,nik,' . $this->id,
            'nip' => 'required|numeric',
            'whatsapp' => 'required|regex:/(62)[8]/|unique:list_data_ksps,whatsapp,' . $this->id,
            'alamat_lengkap' => 'required',
            'divisi' => 'required',
            'departement' => 'required',
            'jabatan' => 'required',
            'status' => 'required|in:tetap,percobaan,phk,resign',
            'statusSp' => 'required|in:0,1,2,3',
            'bank' => 'required|exists:banks,id',
            'nomor_bank' => 'required|numeric',
            'penghasilan' => 'required|numeric',
        ];
    }
}
