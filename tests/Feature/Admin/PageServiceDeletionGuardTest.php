<?php

namespace Tests\Feature\Admin;

use App\Enums\PageStatus;
use App\Exceptions\BusinessException;
use App\Models\Menu;
use App\Models\Page;
use App\Services\Admin\PageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresión: eliminar una página enlazada en un menú dejaba el ítem de
 * menú apuntando a un page_id inexistente — resolvedUrl() caía al
 * placeholder "#" en silencio, sin ningún aviso ni error visible para
 * el visitante ni para el admin. PageService::delete()/forceDelete()
 * ahora bloquean la operación mientras exista al menos un MenuItem
 * enlazado, con un mensaje que indica en qué menú(s) está en uso.
 */
class PageServiceDeletionGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_soft_delete_a_page_used_in_a_menu(): void
    {
        $page = Page::create(['title' => 'Contáctanos', 'status' => PageStatus::Published]);
        $menu = Menu::create(['name' => 'Menú principal']);
        $menu->addItem(['label' => 'Contáctanos', 'type' => 'page', 'page_id' => $page->id]);

        $this->expectException(BusinessException::class);
        $this->expectExceptionMessage('Menú principal');

        app(PageService::class)->delete($page);

        $this->assertFalse($page->fresh()->trashed());
    }

    public function test_cannot_force_delete_a_page_used_in_a_menu(): void
    {
        $page = Page::create(['title' => 'Contáctanos', 'status' => PageStatus::Published]);
        $menu = Menu::create(['name' => 'Menú principal']);
        $menu->addItem(['label' => 'Contáctanos', 'type' => 'page', 'page_id' => $page->id]);

        $this->expectException(BusinessException::class);

        app(PageService::class)->forceDelete($page);

        $this->assertNotNull(Page::find($page->id));
    }

    public function test_can_delete_a_page_not_used_in_any_menu(): void
    {
        $page = Page::create(['title' => 'Página libre', 'status' => PageStatus::Published]);

        app(PageService::class)->delete($page);

        $this->assertTrue($page->fresh()->trashed());
    }
}
