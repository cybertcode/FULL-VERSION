<?php

namespace App\Http\Requests\Admin\User;

use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\Role;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email:rfc', 'max:254', Rule::unique('users', 'email')->ignore($userId)],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
            'status'   => ['required', Rule::enum(UserStatus::class)],
            'role'     => ['required', 'string', Rule::exists(Role::class, 'name')],
            'avatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'nombre',
            'email'    => 'correo electrónico',
            'phone'    => 'teléfono',
            'password' => 'contraseña',
            'status'   => 'estado',
            'role'     => 'rol',
            'avatar'   => 'foto',
        ];
    }
}

