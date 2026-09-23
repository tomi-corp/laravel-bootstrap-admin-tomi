<?php

namespace App\Modules\Lender\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DisbursementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('lender')->check();
    }

    public function rules(): array
    {
        return [
            'pinjaman_id' => [
                'required',
                'integer',
                Rule::exists('pinjamen', 'id')->where('id_lender', auth('lender')->user()?->id_lender),
            ],
            'bank_id' => ['nullable', 'integer', 'exists:banks,id'],
            'dana_yang_dicairkan' => ['nullable', 'numeric', 'min:0'],
            'atas_nama' => ['nullable', 'string', 'max:255'],
            'nomor_rekening' => ['nullable', 'string', 'max:255'],
            'transfer_date' => ['nullable', 'date'],
            'transfer_proof' => ['nullable'],
        ];
    }
}
