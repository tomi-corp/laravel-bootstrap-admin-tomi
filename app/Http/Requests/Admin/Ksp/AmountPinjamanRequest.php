<?php

namespace App\Http\Requests\Admin\Ksp;

use Illuminate\Foundation\Http\FormRequest;

class AmountPinjamanRequest extends FormRequest
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
            'id' => 'required|exists:list_perusahaans,id',
            'iuran_anggota' => 'required',
            'minimal' => 'required|numeric',
            'maximal' => 'required|numeric',
            'maximal_persentase' => 'required|numeric',
        ];
    }
}
