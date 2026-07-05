<?php

namespace App\Http\Requests\Admin\Menu;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'alpha_dash', 'unique:menus,slug'],
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
