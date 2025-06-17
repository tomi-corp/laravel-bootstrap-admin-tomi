<?php

namespace App\Http\Requests\User\Profile;

use Illuminate\Validation\Rules\Password;


use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordActionRequest extends FormRequest
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
            'email' => 'required|exists:password_reset_users,email',
            'token' => 'required|exists:password_reset_users,otp',
            'password' => ['required', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()],
            'password_confirmation' => 'required'
        ];
    }
}
