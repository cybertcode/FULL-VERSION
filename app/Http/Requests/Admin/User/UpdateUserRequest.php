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
        $userId   = $this->route('user')?->id;
        $perfilId = $this->route('user')?->perfil?->id;

        return [
            // Cuenta
            'name'     => ['nullable', 'string', 'max:150'],
            'username' => ['nullable', 'string', 'max:60', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique('users', 'username')->ignore($userId)],
            'email'    => ['required', 'email:rfc', 'max:254', Rule::unique('users', 'email')->ignore($userId)],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
            'status'   => ['required', Rule::enum(UserStatus::class)],
            'role'     => ['nullable', 'string', Rule::exists(Role::class, 'name')],
            'avatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // Perfil — identidad
            'perfil.dni'              => ['nullable', 'digits:8', Rule::unique('perfiles', 'dni')->ignore($perfilId)],
            'perfil.apellido_paterno' => ['nullable', 'string', 'max:100'],
            'perfil.apellido_materno' => ['nullable', 'string', 'max:100'],
            'perfil.nombres'          => ['nullable', 'string', 'max:150'],
            'perfil.fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'perfil.sexo'             => ['nullable', 'in:M,F'],
            'perfil.nacionalidad'     => ['nullable', 'string', 'max:80'],

            // Perfil — laboral
            'perfil.cargo'            => ['nullable', 'string', 'max:150'],
            'perfil.area'             => ['nullable', 'string', 'max:150'],
            'perfil.fecha_ingreso'    => ['nullable', 'date'],
            'perfil.codigo_empleado'  => ['nullable', 'string', 'max:30', Rule::unique('perfiles', 'codigo_empleado')->ignore($perfilId)],

            // Perfil — contacto
            'perfil.telefono_celular'    => ['nullable', 'string', 'max:15'],
            'perfil.telefono_fijo'       => ['nullable', 'string', 'max:15'],
            'perfil.anexo'               => ['nullable', 'string', 'max:10'],
            'perfil.email_institucional' => ['nullable', 'email', 'max:150', Rule::unique('perfiles', 'email_institucional')->ignore($perfilId)],

            // Perfil — ubicación
            'perfil.direccion'    => ['nullable', 'string', 'max:300'],
            'perfil.ubigeo'       => ['nullable', 'digits:6'],
            'perfil.distrito'     => ['nullable', 'string', 'max:100'],
            'perfil.provincia'    => ['nullable', 'string', 'max:100'],
            'perfil.departamento' => ['nullable', 'string', 'max:100'],

            // Perfil — público
            'perfil.bio'      => ['nullable', 'string', 'max:1000'],
            'perfil.linkedin' => ['nullable', 'url', 'max:200'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                       => 'nombre',
            'username'                   => 'usuario',
            'email'                      => 'correo electrónico',
            'phone'                      => 'teléfono',
            'password'                   => 'contraseña',
            'status'                     => 'estado',
            'role'                       => 'rol',
            'avatar'                     => 'foto',
            'perfil.dni'                 => 'DNI',
            'perfil.apellido_paterno'    => 'apellido paterno',
            'perfil.apellido_materno'    => 'apellido materno',
            'perfil.nombres'             => 'nombres',
            'perfil.fecha_nacimiento'    => 'fecha de nacimiento',
            'perfil.sexo'                => 'sexo',
            'perfil.cargo'               => 'cargo',
            'perfil.area'                => 'área',
            'perfil.fecha_ingreso'       => 'fecha de ingreso',
            'perfil.codigo_empleado'     => 'código de empleado',
            'perfil.telefono_celular'    => 'teléfono celular',
            'perfil.telefono_fijo'       => 'teléfono fijo',
            'perfil.email_institucional' => 'correo institucional',
            'perfil.direccion'           => 'dirección',
            'perfil.ubigeo'              => 'ubigeo',
            'perfil.distrito'            => 'distrito',
            'perfil.provincia'           => 'provincia',
            'perfil.departamento'        => 'departamento',
            'perfil.bio'                 => 'biografía',
            'perfil.linkedin'            => 'LinkedIn',
        ];
    }
}
