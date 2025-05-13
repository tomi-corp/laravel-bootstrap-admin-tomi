<?php

namespace App\Http\Requests\Admin\MobileMenu;

use Illuminate\Foundation\Http\FormRequest;

class AddMobileMenuRequest extends FormRequest
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
            'title' => 'required',
            'icon' => 'required',
            'path' => 'required',
            'status' => 'required|in:show,hidden,maintenance',
        ];
    }
}
