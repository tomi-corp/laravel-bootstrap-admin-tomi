<?php

namespace App\Http\Requests\Admin\Ksp\Cost;

use Illuminate\Foundation\Http\FormRequest;

class CostAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'penerbitan_sertifikat_elektronik' => 'required',
            'pk_automation' => 'required',
            'wa_otp' => 'required',
            'disbursement' => 'required',
        ];
    }
}
