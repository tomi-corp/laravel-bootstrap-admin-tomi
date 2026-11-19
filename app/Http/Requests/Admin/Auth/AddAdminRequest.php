<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AddAdminRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|unique:admins,username',
            'email' => 'required|unique:admins,email',
            'instansi_id' => 'required|exists:instansis,id',
            'role_id' => 'required|exists:roles,id',
            'lender_id' => 'nullable|exists:lenders,id',
            'company_id' => 'nullable|exists:list_perusahaans,id',
            'referral_id' => 'nullable|exists:referrals,id',
            'type_admin' => 'nullable|in:admin,master',
            'password' => ['required', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()],
            'password_confirmation' => 'required',
        ];
    }
}
