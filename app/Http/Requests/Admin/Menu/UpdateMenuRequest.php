<?php

namespace App\Http\Requests\Admin\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $menu = $this->route('menu');

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('menus', 'slug')->ignore($menu)],
            'location' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'slug' => 'identificador',
            'location' => 'ubicación',
        ];
    }
}
