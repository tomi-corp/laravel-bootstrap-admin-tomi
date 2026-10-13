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
            'tanggal_pembayaran' => ['nullable', 'date'],
            'payment_proof_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
