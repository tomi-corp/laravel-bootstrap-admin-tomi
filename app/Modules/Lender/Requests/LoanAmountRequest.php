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

        return [
            'id_perusahaan' => [
                'required',
                'integer',
                Rule::exists('list_perusahaans', 'id')->where('id_lender', $lenderId),
            ],
            'iuran_anggota' => ['nullable', 'numeric', 'min:0'],
            'minimal' => ['required', 'numeric', 'min:0'],
            'maximal' => ['required', 'numeric', 'min:0', 'gte:minimal'],
            'maximal_persentase' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
