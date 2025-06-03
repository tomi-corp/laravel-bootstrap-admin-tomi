<?php

namespace App\Http\Requests\Admin\DailyCheckin;

use Illuminate\Foundation\Http\FormRequest;

class DailyCheckinUpdateRequest extends FormRequest
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
            'hari1' => 'required|numeric',
            'hari2' => 'required|numeric',
            'hari3' => 'required|numeric',
            'hari4' => 'required|numeric',
            'hari5' => 'required|numeric',
            'hari6' => 'required|numeric',
            'hari7' => 'required|numeric',
        ];
    }
}
