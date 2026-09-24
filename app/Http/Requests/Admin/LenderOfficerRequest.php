<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LenderOfficerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $officer = $this->route('lender_officer');
        $officerId = is_object($officer) ? $officer->id : $officer;

        return [
            'lender_id' => ['required', 'integer', 'exists:lenders,id'],
            'position' => ['required', Rule::in(['chairperson', 'vice_chairperson', 'secretary', 'treasurer', 'supervisor', 'other'])],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'nik' => ['required', 'digits:16', Rule::unique('lender_officers', 'nik')->ignore($officerId)->whereNull('deleted_at')],
            'place_of_birth' => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'ktp_address' => ['nullable', 'string'],
            'rt' => ['nullable', 'string', 'max:3'],
            'rw' => ['nullable', 'string', 'max:3'],
            'province_id' => ['nullable', 'integer', 'exists:wilayah_provinsi_net,id'],
            'city_id' => ['nullable', 'integer', 'exists:wilayah_kabupaten_net,id'],
            'district_id' => ['nullable', 'integer', 'exists:wilayah_kecamatan_net,id'],
            'village_id' => ['nullable', 'integer', 'exists:wilayah_desa_net,id'],
            'postal_code' => ['nullable', 'string', 'max:5'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'ktp_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'appointment_reference_number' => ['nullable', 'string', 'max:100'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
