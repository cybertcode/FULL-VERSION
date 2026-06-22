<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perfiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->unique();

            // ── Identidad ──────────────────────────────────────────────
            $table->char('dni', 8)->unique()->nullable();
            $table->string('apellido_paterno', 100)->nullable();
            $table->string('apellido_materno', 100)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('sexo', ['M', 'F'])->nullable();
            $table->string('nacionalidad', 80)->default('Peruana');

            // ── Datos laborales ────────────────────────────────────────
            $table->string('cargo', 150)->nullable();
            $table->string('area', 150)->nullable();
            $table->string('regimen_laboral', 60)->nullable(); // CAS, D. Leg. 276, 728, SPE
            $table->date('fecha_ingreso')->nullable();
            $table->string('codigo_empleado', 30)->unique()->nullable();

            // ── Contacto ───────────────────────────────────────────────
            $table->string('telefono_celular', 15)->nullable();
            $table->string('telefono_fijo', 15)->nullable();
            $table->string('anexo', 10)->nullable();
            $table->string('email_institucional', 150)->unique()->nullable();

            // ── Ubicación ──────────────────────────────────────────────
            $table->string('direccion', 300)->nullable();
            $table->char('ubigeo', 6)->nullable();             // Código INEI 6 dígitos
            $table->string('distrito', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('departamento', 100)->nullable();

            // ── Perfil público ─────────────────────────────────────────
            $table->string('foto_perfil', 500)->nullable();    // Foto carnet institucional
            $table->text('bio')->nullable();
            $table->string('linkedin', 200)->nullable();

            // ── Metadatos ──────────────────────────────────────────────
            $table->boolean('datos_completos')->default(false)->index();
            $table->timestamp('perfil_completado_at')->nullable();

            $table->timestamps();

            $table->index('cargo');
            $table->index('area');
            $table->index('regimen_laboral');
            $table->index('fecha_ingreso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfiles');
    }
};
