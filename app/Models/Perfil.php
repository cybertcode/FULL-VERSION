<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string|null $dni
 * @property string|null $apellido_paterno
 * @property string|null $apellido_materno
 * @property string|null $nombres
 * @property string|null $cargo
 * @property string|null $area
 * @property string|null $regimen_laboral
 * @property string|null $codigo_empleado
 * @property string|null $telefono_celular
 * @property string|null $email_institucional
 * @property string|null $departamento
 * @property string|null $provincia
 * @property string|null $distrito
 * @property string|null $bio
 * @property bool        $datos_completos
 */
class Perfil extends Model
{
    protected $table = 'perfiles';

    protected $fillable = [
        'user_id',
        'dni',
        'apellido_paterno',
        'apellido_materno',
        'nombres',
        'fecha_nacimiento',
        'sexo',
        'nacionalidad',
        'cargo',
        'area',
        'regimen_laboral',
        'fecha_ingreso',
        'codigo_empleado',
        'telefono_celular',
        'telefono_fijo',
        'anexo',
        'email_institucional',
        'direccion',
        'ubigeo',
        'distrito',
        'provincia',
        'departamento',
        'foto_perfil',
        'bio',
        'linkedin',
        'datos_completos',
        'perfil_completado_at',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento'     => 'date',
            'fecha_ingreso'        => 'date',
            'datos_completos'      => 'boolean',
            'perfil_completado_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        $partes = array_filter([
            $this->apellido_paterno,
            $this->apellido_materno,
            $this->nombres,
        ]);
        return implode(' ', $partes);
    }

    public static function buildName(?string $apellidoPaterno, ?string $apellidoMaterno, ?string $nombres): string
    {
        $partes = array_filter([
            trim($apellidoPaterno ?? ''),
            trim($apellidoMaterno ?? ''),
            trim($nombres ?? ''),
        ]);
        return implode(' ', $partes);
    }
}
