<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $feature = $this->route('feature');
        $featureId = is_object($feature) ? $feature->id : $feature;

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('features', 'code')->ignore($featureId)->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive', 'develop', 'maintenance'])],
        ];
    }
}
