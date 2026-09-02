<?php

namespace App\Http\Requests\Admin\Ksp;

use Illuminate\Foundation\Http\FormRequest;

class AddKonfigurasiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $defaults = [
            'asuransi',
            'provisi',
            'biaya_admin',
            'fee_tomi',
            'biaya_layanan',
            'minimal_nominal',
            'maximal_nominal',
            'profesional_fee_1',
            'profesional_fee_2',
            'ksp',
        ];

        $values = [];

        foreach ($defaults as $field) {
            if ($this->input($field) === null || $this->input($field) === '') {
                $values[$field] = 0;
            }
        }

        if ($values !== []) {
            $this->merge($values);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required',
            'tenor' => 'required',
            'rate' => 'required',
            'asuransi' => 'required',
            'provisi' => 'required',
            'biaya_admin' => 'required',
            'fee_tomi' => 'required',
            'biaya_layanan' => 'required',
            'minimal_nominal' => 'required',
            'maximal_nominal' => 'required',
        ];
    }
}
