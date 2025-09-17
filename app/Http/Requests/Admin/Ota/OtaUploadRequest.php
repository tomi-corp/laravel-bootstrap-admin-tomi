<?php

namespace App\Http\Requests\Admin\Ota;

use Illuminate\Foundation\Http\FormRequest;

class OtaUploadRequest extends FormRequest
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
            'platform' => 'required|in:android,ios',
            'version'  => ['required', 'regex:/^\d+(\.\d+){2,3}$/'],
            'changelog' => 'required',
            'file' => 'required|mimes:zip,bundle|max:50000',
        ];
    }
}
