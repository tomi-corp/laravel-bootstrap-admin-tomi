<?php

namespace App\Http\Requests\User\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    protected $id;

    public function __construct()
    {
        $this->id = auth('api')->user()->id;
    }

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
            'name' => 'required',
            'dob' => 'required|date',
            'nama_lengkap' => 'required',
            'nik' => 'required|digits:16',
            'selfie' => 'required|base64mimes:jpeg,png,jpg|base64max:2048',
            'identity' => 'required|base64mimes:jpeg,png,jpg|base64max:2048',
            'kartu_keluarga' => 'sometimes|required|base64mimes:jpeg,png,jpg|base64max:2048',
        ];
    }
}
