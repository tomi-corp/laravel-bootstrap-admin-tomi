<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RepaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'id_invoicing' => ['required', 'integer', Rule::exists('invoicing_pinjamen', 'id')],
            'amount_total' => ['required', 'numeric', 'min:0'],
            'tanggal_pembayaran' => ['nullable', 'date'],
            'payment_proof_date' => ['nullable', 'date'],
            'payment_proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('amount_total')) {
            $this->merge([
                'amount_total' => preg_replace('/[^\d.]/', '', (string) $this->input('amount_total')),
            ]);
        }
    }
}
