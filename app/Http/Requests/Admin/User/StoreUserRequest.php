<?php

namespace App\Http\Requests\Admin\User;

use App\Enums\UserStatus;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Cuenta
            'name' => ['nullable', 'string', 'max:150'],
            'username' => ['nullable', 'string', 'max:60', 'regex:/^[a-zA-Z0-9._-]+$/', 'unique:users,username'],
            'email' => ['required', 'email:rfc', 'max:254', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'invite_by_email' => ['nullable', 'boolean'],
            'password' => [
                Rule::requiredIf(! $this->boolean('invite_by_email')),
                'nullable', 'confirmed', Password::min(8)->letters()->numbers(),
            ],
            'status' => ['required', Rule::enum(UserStatus::class)],
            'role' => ['required', 'string', Rule::exists(Role::class, 'name')],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // Perfil — identidad
            'perfil.dni' => ['nullable', 'digits:8', 'unique:perfiles,dni'],
            'perfil.apellido_paterno' => ['nullable', 'string', 'max:100'],
            'perfil.apellido_materno' => ['nullable', 'string', 'max:100'],
            'perfil.nombres' => ['nullable', 'string', 'max:150'],
            'perfil.fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'perfil.sexo' => ['nullable', 'in:M,F'],
            'perfil.nacionalidad' => ['nullable', 'string', 'max:80'],

            // Perfil — laboral
            'perfil.cargo' => ['nullable', 'string', 'max:150'],
            'perfil.area' => ['nullable', 'string', 'max:150'],
            'perfil.fecha_ingreso' => ['nullable', 'date'],
            'perfil.codigo_empleado' => ['nullable', 'string', 'max:30', 'unique:perfiles,codigo_empleado'],

            // Perfil — contacto
            'perfil.telefono_celular' => ['nullable', 'string', 'max:15'],
            'perfil.telefono_fijo' => ['nullable', 'string', 'max:15'],
            'perfil.anexo' => ['nullable', 'string', 'max:10'],
            'perfil.email_institucional' => ['nullable', 'email', 'max:150', 'unique:perfiles,email_institucional'],

            // Perfil — ubicación
            'perfil.direccion' => ['nullable', 'string', 'max:300'],
            'perfil.ubigeo' => ['nullable', 'digits:6'],
            'perfil.distrito' => ['nullable', 'string', 'max:100'],
            'perfil.provincia' => ['nullable', 'string', 'max:100'],
            'perfil.departamento' => ['nullable', 'string', 'max:100'],

            // Perfil — público
            'perfil.bio' => ['nullable', 'string', 'max:1000'],
            'perfil.linkedin' => ['nullable', 'url', 'max:200'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'username' => 'nombre de usuario',
            'email' => 'correo electrónico',
            'phone' => 'teléfono',
            'password' => 'contraseña',
            'status' => 'estado',
            'role' => 'rol',
            'avatar' => 'foto',
            'perfil.dni' => 'DNI',
            'perfil.apellido_paterno' => 'apellido paterno',
            'perfil.apellido_materno' => 'apellido materno',
            'perfil.nombres' => 'nombres',
            'perfil.fecha_nacimiento' => 'fecha de nacimiento',
            'perfil.sexo' => 'sexo',
            'perfil.cargo' => 'cargo',
            'perfil.area' => 'área',
            'perfil.fecha_ingreso' => 'fecha de ingreso',
            'perfil.codigo_empleado' => 'código de empleado',
            'perfil.telefono_celular' => 'teléfono celular',
            'perfil.telefono_fijo' => 'teléfono fijo',
            'perfil.email_institucional' => 'correo institucional',
            'perfil.direccion' => 'dirección',
            'perfil.ubigeo' => 'ubigeo',
            'perfil.distrito' => 'distrito',
            'perfil.provincia' => 'provincia',
            'perfil.departamento' => 'departamento',
            'perfil.bio' => 'biografía',
            'perfil.linkedin' => 'LinkedIn',
        ];
    }
}
