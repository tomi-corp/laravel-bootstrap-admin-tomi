<?php

namespace App\Modules\Lender\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RepaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('lender')->check();
    }

    public function rules(): array
    {
        return [
            'id_invoicing' => ['required', 'integer', 'exists:invoicing_pinjamen,id'],
            'amount_total' => ['required', 'numeric', 'min:0'],
            'tanggal_pembayaran' => ['nullable', 'date'],
            'payment_proof_date' => ['nullable', 'date'],
            'payment_proof_file' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('amount_total')) {
            $this->merge([
                'amount_total' => preg_replace('/\D/', '', (string) $this->input('amount_total')),
            ]);
        }
    }
}
