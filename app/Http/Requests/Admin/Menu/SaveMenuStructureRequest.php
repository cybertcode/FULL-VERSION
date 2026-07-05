<?php

namespace App\Http\Requests\Admin\Menu;

use App\Rules\SafeMenuUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveMenuStructureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'locations' => ['nullable', 'array'],
            'locations.*' => ['boolean'],

            'items' => ['array'],
            'items.*.client_id' => ['required', 'string'],
            'items.*.id' => ['nullable', 'integer', 'exists:menu_items,id'],
            'items.*.parent_client_id' => ['nullable', 'string'],
            'items.*.label' => ['required', 'string', 'max:150'],
            'items.*.type' => ['required', Rule::in(['url', 'page'])],
            'items.*.url' => ['nullable', 'string', 'max:500', new SafeMenuUrl],
            'items.*.page_id' => ['nullable', 'integer', 'exists:pages,id'],
            'items.*.icon' => ['nullable', 'string', 'max:100'],
            'items.*.target' => ['required', Rule::in(['_self', '_blank'])],
            'items.*.is_active' => ['boolean'],
            'items.*.order' => ['required', 'integer', 'min:0'],

            'deleted_ids' => ['array'],
            'deleted_ids.*' => ['integer'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'items.*.label' => 'etiqueta',
        ];
    }
}
