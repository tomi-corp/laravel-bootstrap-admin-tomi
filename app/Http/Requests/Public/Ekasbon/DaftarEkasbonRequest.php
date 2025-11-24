<?php

namespace App\Http\Requests\Public\Ekasbon;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DaftarEkasbonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'namaPerusahaan' => ['required', 'string', 'max:255'],
            'jumlahKaryawan' => ['required', 'in:1 - 50,51 - 100,>100'],
            'pic' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'regex:/^08[0-9]{9,12}$/'],
            'email' => ['required', 'email', 'max:255'],
            'g-recaptcha-response' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'namaPerusahaan.required' => 'Nama perusahaan wajib diisi.',
            'jumlahKaryawan.required' => 'Jumlah karyawan wajib dipilih.',
            'jumlahKaryawan.in' => 'Pilihan jumlah karyawan tidak valid.',
            'pic.required' => 'Nama PIC wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Format nomor WhatsApp tidak valid. Contoh: 081234567890',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'g-recaptcha-response.required' => 'Verifikasi keamanan diperlukan. Pastikan JavaScript aktif.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors(),
        ], 422));
    }
}
