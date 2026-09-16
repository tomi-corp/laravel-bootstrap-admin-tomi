<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AddPicLenderAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_lender' => 'nullable|exists:lenders,id',
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:ksp_admins,username',
            'email' => 'required|email|max:255|unique:ksp_admins,email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ];
    }
}
