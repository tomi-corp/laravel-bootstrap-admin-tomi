<?php

namespace App\Http\Requests\Internal\TomiDana;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRepaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'tanggal' => 'required|date_format:Y-m-d\TH:i',
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required' => 'Waktu pembayaran wajib diisi.',
            'tanggal.date_format' => 'Format waktu pembayaran tidak valid.',
            'bukti_transfer.required' => 'Bukti transfer wajib diupload.',
            'bukti_transfer.file' => 'Bukti transfer tidak valid.',
            'bukti_transfer.mimes' => 'Bukti transfer harus berformat JPG, JPEG, PNG, atau PDF.',
            'bukti_transfer.max' => 'Ukuran bukti transfer maksimal 5 MB.',
        ];
    }
}
