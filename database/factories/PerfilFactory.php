<?php

namespace Database\Factories;

use App\Models\Perfil;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Perfil>
 */
class PerfilFactory extends Factory
{
    protected $model = Perfil::class;

    private static array $cargos = [
        'Especialista en Gestión Pedagógica',
        'Especialista en Gestión Institucional',
        'Especialista en Educación Inicial',
        'Especialista en Educación Primaria',
        'Especialista en Educación Secundaria',
        'Especialista en Tecnologías Educativas',
        'Especialista Legal',
        'Especialista en Recursos Humanos',
        'Especialista Contable',
        'Especialista en Logística',
        'Técnico Administrativo',
        'Técnico en Informática',
        'Técnico en Archivo',
        'Asistente Administrativo',
        'Jefe de Área',
        'Director de Gestión Pedagógica',
        'Director de Gestión Institucional',
        'Director de Administración',
    ];

    private static array $areas = [
        'Área de Gestión Pedagógica',
        'Área de Gestión Institucional',
        'Área de Administración',
        'Área de Asesoría Jurídica',
        'Área de Recursos Humanos',
        'Área de Logística',
        'Área de Contabilidad',
        'Área de Tecnologías de la Información',
        'Dirección General',
    ];

    private static array $departamentos = [
        'Lima', 'Arequipa', 'Cusco', 'La Libertad', 'Piura',
        'Lambayeque', 'Junín', 'Ica', 'Ancash', 'Puno',
    ];

    public function definition(): array
    {
        $departamento = $this->faker->randomElement(self::$departamentos);
        $dni = $this->faker->unique()->numerify('########');
        $sexo = $this->faker->randomElement(['M', 'F']);

        return [
            // Identidad
            'dni' => $dni,
            'apellido_paterno' => $this->faker->lastName(),
            'apellido_materno' => $this->faker->lastName(),
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-55 years', '-22 years')->format('Y-m-d'),
            'sexo' => $sexo,
            'nacionalidad' => 'Peruana',

            // Laboral
            'cargo' => $this->faker->randomElement(self::$cargos),
            'area' => $this->faker->randomElement(self::$areas),
            'fecha_ingreso' => $this->faker->dateTimeBetween('-15 years', '-6 months')->format('Y-m-d'),
            'codigo_empleado' => $this->faker->unique()->numerify('EMP-####'),

            // Contacto
            'telefono_celular' => $this->faker->numerify('9########'),
            'telefono_fijo' => $this->faker->numerify('01-#######'),
            'anexo' => $this->faker->numerify('###'),
            'email_institucional' => $this->faker->unique()->safeEmail(),

            // Ubicación
            'direccion' => $this->faker->streetAddress(),
            'ubigeo' => $this->faker->numerify('######'),
            'distrito' => $this->faker->city(),
            'provincia' => $this->faker->city(),
            'departamento' => $departamento,

            // Público
            'bio' => $this->faker->boolean(60) ? $this->faker->sentence(12) : null,
            'linkedin' => $this->faker->boolean(30)
                ? 'https://linkedin.com/in/'.$this->faker->userName()
                : null,

            'datos_completos' => true,
            'perfil_completado_at' => now(),
        ];
    }

    /** Perfil sin datos laborales (usuario básico) */
    public function basico(): static
    {
        return $this->state([
            'cargo' => null,
            'area' => null,
            'fecha_ingreso' => null,
            'codigo_empleado' => null,
            'datos_completos' => false,
            'perfil_completado_at' => null,
        ]);
    }
}
