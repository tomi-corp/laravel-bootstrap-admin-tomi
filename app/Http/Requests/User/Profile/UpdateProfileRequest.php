<?php

namespace App\Http\Requests\User\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


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
            'nik' =>
            [
                'required',
                'digits:16',
                // Tambahkan aturan ini untuk memastikan NIK unik di tabel 'users',
                // tetapi abaikan NIK milik pengguna saat ini.
                Rule::unique('users')->ignore(auth('api')->user()->id),
            ],
            'selfie' => 'sometimes|required|base64mimes:jpeg,png,jpg|base64max:2048',
            'identity' => 'sometimes|required|base64mimes:jpeg,png,jpg|base64max:2048',
            'kartu_keluarga' => 'sometimes|required|base64mimes:jpeg,png,jpg|base64max:2048',
        ];
    }
}
