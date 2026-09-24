<?php

namespace App\Modules\Lender\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $lenderId = auth('lender')->user()?->id_lender;
        $employeeId = $this->route('employee')?->id;

        return [
            'id_list_perusahaan' => [
                'required',
                'integer',
                Rule::exists('list_perusahaans', 'id')->where('id_lender', $lenderId),
            ],
            'nik' => ['required', 'string', 'max:255', Rule::unique('list_karyawans', 'nik')->ignore($employeeId)],
            'nip' => ['required', 'string', 'max:255', Rule::unique('list_karyawans', 'nip')->ignore($employeeId)],
            'whatsapp' => ['required', 'string', 'max:255', Rule::unique('list_karyawans', 'whatsapp')->ignore($employeeId)],
            'nama_karyawan' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:255'],
            'divisi' => ['nullable', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:255'],
            'max_plafon' => ['required', 'numeric', 'min:0'],
            'departement' => ['required', 'string', 'max:255'],
            'tanggal_bergabung' => ['required', 'date'],
            'status' => ['required', Rule::in(['tetap', 'percobaan', 'phk', 'resign'])],
            'status_sp' => ['required', Rule::in(['0', '1', '2', '3'])],
            'id_nama_bank' => ['nullable', 'integer', 'exists:banks,id'],
            'nama_rekening' => ['nullable', 'string', 'max:255'],
            'nomor_bank' => ['nullable', 'string'],
            'approve_satu' => ['required', Rule::in(['pending', 'approve', 'reject'])],
            'approve_dua' => ['required', Rule::in(['pending', 'approve', 'reject'])],
        ];
    }
}
