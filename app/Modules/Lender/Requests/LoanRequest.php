<?php

namespace App\Modules\Lender\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('lender')->check();
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach ([
            'nominal',
            'biaya_admin',
            'iuran_anggota',
            'biaya_layanan',
            'total_angsuran_perbulan',
            'jumlah_yang_diterima',
        ] as $field) {
            if ($this->has($field)) {
                $data[$field] = preg_replace('/\D/', '', (string) $this->input($field));
            }
        }

        if ($data !== []) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $loan = $this->route('loan');

        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'id_perusahaan' => ['required', 'integer', 'exists:list_perusahaans,id'],
            'id_karyawan' => ['required', 'integer', 'exists:list_karyawans,id'],
            'id_lender' => ['required', 'integer', 'exists:lenders,id'],
            'id_kyc' => ['required', 'integer', 'exists:kyc_users,id'],
            'id_pinjaman' => ['required', 'string', 'max:255', Rule::unique('pinjamen', 'id_pinjaman')->ignore($loan?->id)],
            'nominal' => ['required', 'numeric', 'min:0'],
            'tenor' => ['required', 'integer', 'min:1'],
            'rate' => ['required', 'numeric', 'min:0'],
            'biaya_admin' => ['nullable', 'numeric', 'min:0'],
            'iuran_anggota' => ['nullable', 'numeric', 'min:0'],
            'biaya_layanan' => ['nullable', 'numeric', 'min:0'],
            'total_angsuran_perbulan' => ['nullable', 'numeric', 'min:0'],
            'jumlah_yang_diterima' => ['nullable', 'numeric', 'min:0'],
            'cost_tomi' => ['nullable', 'numeric', 'min:0'],
            'cost_ksp' => ['nullable', 'numeric', 'min:0'],
            'description_cost_tomi' => ['nullable', 'string'],
            'description_cost_ksp' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['on_proses', 'pending', 'approve', 'done'])],
            'ttd' => ['required', Rule::in(['y', 'n'])],
            'status_otp' => ['required', Rule::in(['pending', 'success'])],
            'document_privy' => ['required', Rule::in(['y', 'n'])],
            'note' => ['nullable', 'string'],
        ];
    }
}
