<?php

namespace App\Http\Requests\Admin\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class MenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $menu = $this->route('menu');
        $menuId = $menu?->id;
        $menuColumns = Schema::getColumnListing('menus');

        $rules = [
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('menus', 'id')->whereNull('deleted_at'),
                Rule::notIn(array_filter([$menuId])),
            ],
            'name' => ['required', 'string', 'max:100'],
        ];

        if (in_array('project_id', $menuColumns, true)) {
            $rules['project_id'] = [
                'nullable',
                'integer',
                Rule::exists('projects', 'id')->whereNull('deleted_at'),
            ];
        }

        if (in_array('feature_id', $menuColumns, true)) {
            $rules['feature_id'] = [
                'nullable',
                'integer',
                Rule::exists('features', 'id')->whereNull('deleted_at'),
            ];
        }

        if (in_array('label', $menuColumns, true)) {
            $rules['label'] = ['required', 'string', 'max:100'];
        }

        if (in_array('icon', $menuColumns, true)) {
            $rules['icon'] = ['nullable', 'string', 'max:100'];
        }

        if (in_array('route', $menuColumns, true)) {
            $rules['route'] = ['nullable', 'string', 'max:150'];
        }

        if (in_array('type', $menuColumns, true)) {
            $rules['type'] = ['required', Rule::in(['menu', 'group'])];
        }

        if (in_array('sort_order', $menuColumns, true)) {
            $rules['sort_order'] = ['required', 'integer', 'min:0'];
        }

        if (in_array('is_active', $menuColumns, true)) {
            $rules['is_active'] = ['required', 'boolean'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'parent_id.not_in' => 'Parent menu tidak boleh sama dengan menu yang sedang diedit.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $menu = $this->route('menu');
            $parentId = $this->integer('parent_id');

            if (! $menu || ! $parentId) {
                return;
            }

            $descendantIds = $menu->children()
                ->with('children')
                ->get()
                ->flatMap(fn ($child) => $this->descendantIds($child))
                ->push($menu->id)
                ->all();

            if (in_array($parentId, $descendantIds, true)) {
                $validator->errors()->add('parent_id', 'Parent menu tidak boleh berasal dari submenu menu ini.');
            }
        });
    }

    private function descendantIds($menu)
    {
        return collect([$menu->id])->merge(
            $menu->children->flatMap(fn ($child) => $this->descendantIds($child))
        );
    }
}
