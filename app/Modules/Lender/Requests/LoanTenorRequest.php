<?php

namespace App\Modules\Lender\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoanTenorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $lenderId = auth('lender')->user()?->id_lender;

        return [
            'id_perusahaan' => [
                'required',
                'integer',
                Rule::exists('list_perusahaans', 'id')->where('id_lender', $lenderId),
            ],
            'tenor' => ['required', 'integer', 'min:1', 'max:360'],
            'rate' => ['required', 'numeric', 'min:0'],
            'asuransi' => ['required', 'numeric', 'min:0'],
            'provisi' => ['required', 'numeric', 'min:0'],
            'biaya_admin' => ['required', 'numeric', 'min:0'],
            'fee_tomi' => ['required', 'numeric', 'min:0'],
            'biaya_layanan' => ['nullable', 'numeric', 'min:0'],
            'minimal_nominal' => ['nullable', 'numeric', 'min:0'],
            'maximal_nominal' => ['nullable', 'numeric', 'min:0', 'gte:minimal_nominal'],
            'profesional_fee_1' => ['required', 'numeric', 'min:0'],
            'profesional_fee_2' => ['required', 'numeric', 'min:0'],
            'ksp' => ['required', 'numeric', 'min:0'],
        ];
    }
}
