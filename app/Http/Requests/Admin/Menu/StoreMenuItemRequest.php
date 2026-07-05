<?php

namespace App\Http\Requests\Admin\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:150'],
            'type' => ['required', Rule::in(['url', 'page', 'route'])],
            'url' => ['required_if:type,url', 'nullable', 'string', 'max:500'],
            'page_id' => ['required_if:type,page', 'nullable', 'integer'],
            'route_name' => ['required_if:type,route', 'nullable', 'string', 'max:150'],
            'icon' => ['nullable', 'string', 'max:100'],
            'target' => ['required', Rule::in(['_self', '_blank'])],
            'is_active' => ['boolean'],
            'parent_id' => ['nullable', 'integer', 'exists:menu_items,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'label' => 'etiqueta',
            'url' => 'URL',
            'page_id' => 'página',
            'route_name' => 'ruta',
            'target' => 'destino',
        ];
    }
}
