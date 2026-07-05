<?php

namespace App\Http\Requests\Admin\Permission;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'module' => ['required', 'string', 'max:50', 'regex:/^[a-z][a-z0-9_-]*$/'],
            'action' => ['required', 'string', 'max:50', 'regex:/^[a-z][a-zA-Z0-9]*$/'],
            'label' => ['nullable', 'string', 'max:150'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'module' => strtolower(trim((string) $this->input('module'))),
            'action' => trim((string) $this->input('action')),
        ]);
    }

    public function attributes(): array
    {
        return [
            'module' => 'módulo',
            'action' => 'acción',
            'label' => 'label',
        ];
    }
}
