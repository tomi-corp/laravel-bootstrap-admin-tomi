<?php

namespace App\Modules\Lender\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoanAmountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $lenderId = auth('lender')->user()?->id_lender;
        $loanAmountId = $this->route('loanAmount')?->id;

        return [
            'id_perusahaan' => [
                'required',
                'integer',
                Rule::exists('list_perusahaans', 'id')->where('id_lender', $lenderId),
                Rule::unique('amount_pinjamen', 'id_perusahaan')->ignore($loanAmountId),
            ],
            'iuran_anggota' => ['nullable', 'numeric', 'min:0'],
            'minimal' => ['required', 'numeric', 'min:0'],
            'maximal' => ['required', 'numeric', 'min:0', 'gte:minimal'],
            'maximal_persentase' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_perusahaan.unique' => 'Konfigurasi DSR untuk perusahaan ini sudah ada. Silakan edit konfigurasi yang sudah tersedia.',
        ];
    }
}
