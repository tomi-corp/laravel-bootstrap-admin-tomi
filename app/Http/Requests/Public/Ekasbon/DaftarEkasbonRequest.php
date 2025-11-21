<?php

namespace App\Http\Requests\Public\Ekasbon;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DaftarEkasbonRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'jumlah_karyawan' => ['required', 'in:1 - 50,51 - 100,>100'],
            'pic' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'regex:/^08[0-9]{9,12}$/'],
            'email' => ['required', 'email', 'max:255'],

            // Geetest v4 fields (wajib untuk verifikasi anti-bot)
            'geetest_challenge' => ['required', 'string'],
            'geetest_validate'  => ['required', 'string'],
            'geetest_seccode'   => ['required', 'string'],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi.',
            'jumlah_karyawan.required' => 'Jumlah karyawan wajib dipilih.',
            'jumlah_karyawan.in' => 'Pilihan jumlah karyawan tidak valid.',
            'pic.required' => 'Nama PIC wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Format nomor WhatsApp tidak valid. Contoh: 081234567890',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',

            'geetest_challenge.required' => 'Verifikasi CAPTCHA diperlukan.',
            'geetest_validate.required' => 'Verifikasi CAPTCHA tidak lengkap.',
            'geetest_seccode.required' => 'Kode keamanan CAPTCHA hilang.',
        ];
    }

    /**
     * Handle validation failure with JSON response (for API).
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors(),
        ], 422));
    }
}
