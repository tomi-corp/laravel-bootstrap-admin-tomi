<?php

namespace App\Modules\Lender\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisbursementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('lender')->check();
    }

    public function rules(): array
    {
        return [
            'pinjaman_id' => ['required', 'integer', 'exists:pinjamen,id'],
            'bank_id' => ['required', 'integer', 'exists:banks,id'],
            'dana_yang_dicairkan' => ['required', 'numeric', 'min:0'],
            'atas_nama' => ['required', 'string', 'max:255'],
            'nomor_rekening' => ['required', 'string', 'max:255'],
            'transfer_date' => ['nullable', 'date'],
            'transfer_proof' => ['nullable', 'string', 'max:255'],
        ];
    }
}
