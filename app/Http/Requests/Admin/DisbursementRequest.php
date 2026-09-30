<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DisbursementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'pinjaman_id' => ['required', 'integer', Rule::exists('pinjamen', 'id')],
            'bank_id' => ['required', 'integer', Rule::exists('banks', 'id')],
            'dana_yang_dicairkan' => ['required', 'numeric', 'min:0'],
            'atas_nama' => ['required', 'string', 'max:255'],
            'nomor_rekening' => ['required', 'string', 'max:255'],
            'transfer_date' => ['nullable', 'date'],
            'transfer_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('dana_yang_dicairkan')) {
            $this->merge([
                'dana_yang_dicairkan' => preg_replace('/[^\d.]/', '', (string) $this->input('dana_yang_dicairkan')),
            ]);
        }
    }
}
