<?php

namespace Database\Seeders;

use App\Enums\MenuLocation;
use App\Models\Menu;
use App\Models\MenuLocationAssignment;
use Illuminate\Database\Seeder;

/**
 * Ejemplo canónico de cómo crear menús e ítems por código (sin pasar por el
 * panel administrativo). Para gestión visual usar /admin/menus; para datos
 * versionados en código (este seeder, tinker, comandos artisan) usar
 * Menu::addItem() como se hace aquí — encapsula el nestedset (appendToNode/
 * saveAsRoot) para que no haya que repetirlo a mano en cada lugar.
 */
class MenusSeeder extends Seeder
{
    public function run(): void
    {
        // ── Menú principal (header) ─────────────────────────────────────
        $header = Menu::firstOrCreate(
            ['slug' => 'header-principal'],
            ['name' => 'Menú principal']
        );

        if ($header->allItems()->count() === 0) {
            $header->addItem(['label' => 'Inicio', 'type' => 'url', 'url' => '/', 'icon' => 'home', 'target' => '_self']);

            $nosotros = $header->addItem(['label' => 'Nosotros', 'type' => 'url', 'url' => '/nosotros', 'icon' => 'info-circle', 'target' => '_self']);
            $header->addItem(['label' => 'Nuestro equipo', 'type' => 'url', 'url' => '/nosotros/equipo', 'target' => '_self'], parent: $nosotros);
            $header->addItem(['label' => 'Misión y visión', 'type' => 'url', 'url' => '/nosotros/mision-vision', 'target' => '_self'], parent: $nosotros);

            $servicios = $header->addItem(['label' => 'Servicios', 'type' => 'url', 'url' => '/servicios', 'icon' => 'briefcase', 'target' => '_self']);
            $consultoria = $header->addItem(['label' => 'Consultoría', 'type' => 'url', 'url' => '/servicios/consultoria', 'target' => '_self'], parent: $servicios);
            $header->addItem(['label' => 'Soporte técnico', 'type' => 'url', 'url' => '/servicios/soporte', 'target' => '_self'], parent: $servicios);

            // Ejemplo de 3er nivel de anidamiento (sub-ítem de un sub-ítem) —
            // el nestedset no tiene límite de profundidad, esto lo demuestra.
            $header->addItem(['label' => 'Auditoría de procesos', 'type' => 'url', 'url' => '/servicios/consultoria/auditoria', 'target' => '_self'], parent: $consultoria);

            $header->addItem(['label' => 'Contacto', 'type' => 'url', 'url' => '/contacto', 'icon' => 'mail', 'target' => '_self']);
            $header->addItem(['label' => 'Iniciar sesión', 'type' => 'url', 'url' => '/login', 'icon' => 'login', 'target' => '_self']);
            $header->addItem(['label' => 'Blog Vuexy (demo)', 'type' => 'url', 'url' => 'https://vuexy.com', 'target' => '_blank']);
        }

        // ── Menú de pie de página (footer) ───────────────────────────────
        $footer = Menu::firstOrCreate(
            ['slug' => 'footer-legal'],
            ['name' => 'Pie de página']
        );

        if ($footer->allItems()->count() === 0) {
            $footer->addItem(['label' => 'Términos y condiciones', 'type' => 'url', 'url' => '/terminos', 'target' => '_self']);
            $footer->addItem(['label' => 'Política de privacidad', 'type' => 'url', 'url' => '/privacidad', 'target' => '_self']);
            $footer->addItem(['label' => 'Preguntas frecuentes', 'type' => 'url', 'url' => '/faq', 'target' => '_self', 'is_active' => false]);
        }

        // ── Asignación de zonas (estilo WordPress) ───────────────────────
        MenuLocationAssignment::updateOrCreate(
            ['location' => MenuLocation::Header->value],
            ['menu_id' => $header->id]
        );

        MenuLocationAssignment::updateOrCreate(
            ['location' => MenuLocation::Footer->value],
            ['menu_id' => $footer->id]
        );
    }
}
