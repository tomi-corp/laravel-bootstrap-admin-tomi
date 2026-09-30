<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $adminId = $this->integer('id');

        return [
            'id' => ['required', 'integer', 'exists:admins,id'],
            'username' => ['required', Rule::unique('admins', 'username')->ignore($adminId)],
            'email' => ['required', 'email', Rule::unique('admins', 'email')->ignore($adminId)],
            'type_admin' => ['required', Rule::in(['admin', 'master'])],
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ];
    }
}
