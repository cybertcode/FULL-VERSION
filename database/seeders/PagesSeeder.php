<?php

namespace Database\Seeders;

use App\Enums\PageStatus;
use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Datos de prueba para el módulo de Páginas — cubre jerarquía padre/hijo
 * (para probar el breadcrumb automático y la indentación del listado admin)
 * y ambos estados (para probar el filtro de "publicadas" del selector de Menús).
 */
class PagesSeeder extends Seeder
{
    public function run(): void
    {
        if (Page::count() > 0) {
            return;
        }

        $nosotros = Page::create([
            'title' => 'Nosotros',
            'status' => PageStatus::Published,
            'published_at' => now(),
        ]);

        Page::create([
            'title' => 'Nuestro equipo',
            'status' => PageStatus::Published,
            'published_at' => now(),
            'parent_id' => $nosotros->id,
        ]);

        Page::create([
            'title' => 'Misión y visión',
            'status' => PageStatus::Published,
            'published_at' => now(),
            'parent_id' => $nosotros->id,
        ]);

        $servicios = Page::create([
            'title' => 'Servicios',
            'status' => PageStatus::Published,
            'published_at' => now(),
        ]);

        $consultoria = Page::create([
            'title' => 'Consultoría',
            'status' => PageStatus::Published,
            'published_at' => now(),
            'parent_id' => $servicios->id,
        ]);

        Page::create([
            'title' => 'Auditoría de procesos',
            'status' => PageStatus::Published,
            'published_at' => now(),
            'parent_id' => $consultoria->id,
        ]);

        Page::create([
            'title' => 'Contáctanos',
            'status' => PageStatus::Published,
            'published_at' => now(),
        ]);

        // Borrador: no debe aparecer en el frontend ni en el selector de Menús.
        Page::create([
            'title' => 'Página en preparación',
            'status' => PageStatus::Draft,
        ]);
    }
}
