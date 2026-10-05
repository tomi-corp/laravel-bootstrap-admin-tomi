<?php

namespace App\Http\Requests\Admin\RoleMenuPermission;

use App\Models\RoleMenuPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleMenuPermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleMenuPermission = $this->route('role_menu_permission');

        $rules = [
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->whereNull('deleted_at'),
            ],
            'menu_id' => [
                'required',
                'integer',
                Rule::exists('menus', 'id')->whereNull('deleted_at'),
                Rule::unique('role_menu_permissions', 'menu_id')
                    ->where('role_id', $this->input('role_id'))
                    ->whereNull('deleted_at')
                    ->ignore($roleMenuPermission?->id),
            ],
        ];

        foreach (RoleMenuPermission::PERMISSIONS as $permission) {
            $rules[$permission] = ['nullable', 'boolean'];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $permissions = [];

        foreach (RoleMenuPermission::PERMISSIONS as $permission) {
            $permissions[$permission] = $this->boolean($permission);
        }

        $this->merge($permissions);
    }
}
