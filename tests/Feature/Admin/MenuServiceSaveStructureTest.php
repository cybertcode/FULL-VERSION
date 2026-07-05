<?php

namespace Tests\Feature\Admin;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\Admin\MenuService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuServiceSaveStructureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regresión: guardar un árbol de 3 niveles donde (1) una raíz se
     * reordena a una posición anterior (desplazando en BD el subárbol
     * completo de otra raíz vía makeGap) y luego (2) un nieto se
     * reparenta a un tío usando ese tío como $parent cacheado en memoria,
     * lanzaba "LogicException: Node must not be a descendant." porque
     * appendToNode()/afterNode() comparaban _lft/_rgt en memoria (obsoletos
     * tras el shift SQL del paso 1) contra el nodo movido, en vez de
     * releer el modelo fresco de BD antes de cada operación de la jerarquía.
     */
    public function test_saves_three_level_tree_with_reordered_root_and_reparented_grandchild_without_corrupting_it(): void
    {
        $service = app(MenuService::class);
        $menu = Menu::create(['name' => 'Regression Menu']);

        $items = [
            ['client_id' => 'a', 'id' => null, 'parent_client_id' => null, 'label' => 'A', 'type' => 'url', 'url' => '/a', 'icon' => null, 'target' => '_self', 'is_active' => true, 'order' => 0],
            ['client_id' => 'b', 'id' => null, 'parent_client_id' => null, 'label' => 'B', 'type' => 'url', 'url' => '/b', 'icon' => null, 'target' => '_self', 'is_active' => true, 'order' => 1],
            ['client_id' => 'f', 'id' => null, 'parent_client_id' => null, 'label' => 'F', 'type' => 'url', 'url' => '/f', 'icon' => null, 'target' => '_self', 'is_active' => true, 'order' => 2],
            ['client_id' => 'c', 'id' => null, 'parent_client_id' => 'b', 'label' => 'C', 'type' => 'url', 'url' => '/b/c', 'icon' => null, 'target' => '_self', 'is_active' => true, 'order' => 0],
            ['client_id' => 'g', 'id' => null, 'parent_client_id' => 'b', 'label' => 'G', 'type' => 'url', 'url' => '/b/g', 'icon' => null, 'target' => '_self', 'is_active' => true, 'order' => 1],
            ['client_id' => 'd', 'id' => null, 'parent_client_id' => 'c', 'label' => 'D', 'type' => 'url', 'url' => '/b/c/d', 'icon' => null, 'target' => '_self', 'is_active' => true, 'order' => 0],
            ['client_id' => 'h', 'id' => null, 'parent_client_id' => 'g', 'label' => 'H', 'type' => 'url', 'url' => '/b/g/h', 'icon' => null, 'target' => '_self', 'is_active' => true, 'order' => 0],
        ];

        $service->saveStructure($menu, $menu->name, $items, [], []);

        $saved = MenuItem::where('menu_id', $menu->id)->get()->keyBy('label');

        // Segundo guardado: F pasa de 3ra a 1ra raíz (desplaza el subárbol de
        // B en BD), se invierten G/C dentro de B, y D se reparenta de C a G.
        $items2 = [
            ['client_id' => 'f', 'id' => $saved['F']->id, 'parent_client_id' => null, 'label' => 'F', 'type' => 'url', 'url' => '/f', 'icon' => null, 'target' => '_self', 'is_active' => true, 'order' => 0],
            ['client_id' => 'a', 'id' => $saved['A']->id, 'parent_client_id' => null, 'label' => 'A', 'type' => 'url', 'url' => '/a', 'icon' => null, 'target' => '_self', 'is_active' => true, 'order' => 1],
            ['client_id' => 'b', 'id' => $saved['B']->id, 'parent_client_id' => null, 'label' => 'B', 'type' => 'url', 'url' => '/b', 'icon' => null, 'target' => '_self', 'is_active' => true, 'order' => 2],
            ['client_id' => 'g', 'id' => $saved['G']->id, 'parent_client_id' => 'b', 'label' => 'G', 'type' => 'url', 'url' => '/b/g', 'icon' => null, 'target' => '_self', 'is_active' => true, 'order' => 0],
            ['client_id' => 'c', 'id' => $saved['C']->id, 'parent_client_id' => 'b', 'label' => 'C', 'type' => 'url', 'url' => '/b/c', 'icon' => null, 'target' => '_self', 'is_active' => true, 'order' => 1],
            ['client_id' => 'h', 'id' => $saved['H']->id, 'parent_client_id' => 'g', 'label' => 'H', 'type' => 'url', 'url' => '/b/g/h', 'icon' => null, 'target' => '_self', 'is_active' => true, 'order' => 0],
            ['client_id' => 'd', 'id' => $saved['D']->id, 'parent_client_id' => 'g', 'label' => 'D', 'type' => 'url', 'url' => '/b/c/d', 'icon' => null, 'target' => '_self', 'is_active' => true, 'order' => 1],
        ];

        $service->saveStructure($menu, $menu->name, $items2, [], []);

        $tree = $menu->fresh()->tree();

        $this->assertCount(3, $tree, 'Debe haber 3 raíces (F, A, B)');
        $this->assertSame('F', $tree->first()->label, 'F debe ser ahora la primera raíz');

        $b = $tree->firstWhere('label', 'B');
        $this->assertNotNull($b);
        $this->assertCount(2, $b->children, 'B debe tener 2 hijos (G, C)');

        $g = $b->children->firstWhere('label', 'G');
        $this->assertNotNull($g, 'G debe seguir siendo hijo de B');
        $this->assertCount(2, $g->children, 'G debe tener 2 hijos (H, D)');
        $this->assertSame(['H', 'D'], $g->children->pluck('label')->all());

        $c = $b->children->firstWhere('label', 'C');
        $this->assertNotNull($c, 'C debe seguir siendo hijo de B');
        $this->assertCount(0, $c->children, 'C ya no debe tener hijos (D se reparentó a G)');
    }
}
