<?php

namespace App\Http\Requests\User\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileSelfieKTPKKRequest extends FormRequest
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
            'selfie' => 'sometimes|required|base64mimes:jpeg,png,jpg|base64max:2048',
            'identity' => 'sometimes|required|base64mimes:jpeg,png,jpg|base64max:2048',
            'kartu_keluarga' => 'sometimes|required|base64mimes:jpeg,png,jpg|base64max:2048',
        ];
    }
}
