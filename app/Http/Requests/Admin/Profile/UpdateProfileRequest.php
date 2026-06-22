<?php

namespace App\Http\Requests\Admin\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId   = auth()->id();
        $perfilId = auth()->user()->perfil?->id;

        return [
            'username' => ['nullable', 'string', 'max:60', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique('users', 'username')->ignore($userId)],
            'phone'    => ['nullable', 'string', 'max:20'],
            'avatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'banner'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'perfil.dni'              => ['nullable', 'digits:8', Rule::unique('perfiles', 'dni')->ignore($perfilId)],
            'perfil.apellido_paterno' => ['nullable', 'string', 'max:100'],
            'perfil.apellido_materno' => ['nullable', 'string', 'max:100'],
            'perfil.nombres'          => ['nullable', 'string', 'max:150'],
            'perfil.fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'perfil.sexo'             => ['nullable', 'in:M,F'],
            'perfil.nacionalidad'     => ['nullable', 'string', 'max:80'],

            'perfil.cargo'            => ['nullable', 'string', 'max:150'],
            'perfil.area'             => ['nullable', 'string', 'max:150'],
            'perfil.fecha_ingreso'    => ['nullable', 'date'],
            'perfil.codigo_empleado'  => ['nullable', 'string', 'max:30', Rule::unique('perfiles', 'codigo_empleado')->ignore($perfilId)],

            'perfil.telefono_celular'    => ['nullable', 'string', 'max:15'],
            'perfil.telefono_fijo'       => ['nullable', 'string', 'max:15'],
            'perfil.anexo'               => ['nullable', 'string', 'max:10'],
            'perfil.email_institucional' => ['nullable', 'email', 'max:150', Rule::unique('perfiles', 'email_institucional')->ignore($perfilId)],

            'perfil.bio'      => ['nullable', 'string', 'max:1000'],
            'perfil.linkedin' => ['nullable', 'url', 'max:200'],
        ];
    }

    public function attributes(): array
    {
        return [
            'username'                   => 'usuario',
            'phone'                      => 'teléfono',
            'avatar'                     => 'foto de perfil',
            'banner'                     => 'foto de portada',
            'perfil.dni'                 => 'DNI',
            'perfil.apellido_paterno'    => 'apellido paterno',
            'perfil.apellido_materno'    => 'apellido materno',
            'perfil.nombres'             => 'nombres',
            'perfil.fecha_nacimiento'    => 'fecha de nacimiento',
            'perfil.sexo'                => 'sexo',
            'perfil.nacionalidad'        => 'nacionalidad',
            'perfil.cargo'               => 'cargo',
            'perfil.area'                => 'área',
            'perfil.fecha_ingreso'       => 'fecha de ingreso',
            'perfil.codigo_empleado'     => 'código de empleado',
            'perfil.telefono_celular'    => 'teléfono celular',
            'perfil.telefono_fijo'       => 'teléfono fijo',
            'perfil.email_institucional' => 'correo institucional',
            'perfil.bio'                 => 'biografía',
            'perfil.linkedin'            => 'LinkedIn',
        ];
    }
}
