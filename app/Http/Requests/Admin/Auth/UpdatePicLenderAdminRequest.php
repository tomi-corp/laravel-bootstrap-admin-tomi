<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdatePicLenderAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:ksp_admins,id',
            'id_lender' => 'nullable|exists:lenders,id',
            'nama' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ksp_admins', 'username')->ignore($this->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('ksp_admins', 'email')->ignore($this->id),
            ],
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ];
    }
}
