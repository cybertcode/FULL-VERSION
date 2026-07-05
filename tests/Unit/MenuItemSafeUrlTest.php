<?php

namespace Tests\Unit;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuItemSafeUrlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regresión: MenuItem::resolvedUrl() se imprime sin escapar el scheme en
     * href={{ ... }} de las plantillas del frontend público — un ítem con
     * url=javascript:... insertado por cualquier vía (seeder, tinker, un
     * bug futuro que se salte la validación del request) no debe poder
     * renderizar un enlace ejecutable.
     */
    public function test_resolved_url_blocks_dangerous_schemes(): void
    {
        $menu = Menu::create(['name' => 'Test']);

        $item = MenuItem::create([
            'menu_id' => $menu->id,
            'label' => 'Malicioso',
            'type' => 'url',
            'url' => 'javascript:alert(document.cookie)',
            'target' => '_self',
            'is_active' => true,
        ]);
        $item->saveAsRoot();

        $this->assertSame('#', $item->resolvedUrl());
    }

    public function test_resolved_url_allows_safe_schemes_and_relative_paths(): void
    {
        $menu = Menu::create(['name' => 'Test']);

        $cases = ['/servicios', '#footer', 'https://example.com', 'mailto:hola@example.com'];

        foreach ($cases as $url) {
            $item = MenuItem::create([
                'menu_id' => $menu->id,
                'label' => 'Item',
                'type' => 'url',
                'url' => $url,
                'target' => '_self',
                'is_active' => true,
            ]);
            $item->saveAsRoot();

            $this->assertSame($url, $item->resolvedUrl());
        }
    }
}
