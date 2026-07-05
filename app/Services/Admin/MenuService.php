<?php

namespace App\Services\Admin;

use App\Exceptions\BusinessException;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Collection;

class MenuService
{
    public function all(): Collection
    {
        return Menu::withCount('allItems')->orderBy('name')->get();
    }

    public function create(array $data): Menu
    {
        return Menu::create($data);
    }

    public function update(Menu $menu, array $data): Menu
    {
        $menu->update($data);

        return $menu;
    }

    public function delete(Menu $menu): void
    {
        $menu->allItems()->get()->each->delete();
        $menu->delete();
    }

    public function createItem(Menu $menu, array $data): MenuItem
    {
        $item = new MenuItem($data);
        $item->menu_id = $menu->id;

        if (! empty($data['parent_id'])) {
            $parent = MenuItem::where('menu_id', $menu->id)->findOrFail($data['parent_id']);
            $item->appendToNode($parent)->save();
        } else {
            $item->saveAsRoot();
        }

        return $item;
    }

    public function updateItem(MenuItem $item, array $data): MenuItem
    {
        $newParentId = $data['parent_id'] ?? null;
        unset($data['parent_id']);

        $item->fill($data);

        if ($newParentId && $newParentId !== $item->parent_id) {
            $parent = MenuItem::where('menu_id', $item->menu_id)->findOrFail($newParentId);
            $item->appendToNode($parent)->save();
        } elseif (! $newParentId && $item->parent_id) {
            $item->saveAsRoot();
        } else {
            $item->save();
        }

        return $item;
    }

    public function deleteItem(MenuItem $item): void
    {
        $item->delete();
    }

    /**
     * Aplica el movimiento reportado por el evento move_node.jstree del árbol.
     * $newParentId es null cuando jsTree reporta '#' (nodo raíz).
     */
    public function moveNode(Menu $menu, int $nodeId, ?int $newParentId, int $position): void
    {
        $item = MenuItem::where('menu_id', $menu->id)->findOrFail($nodeId);

        if ($newParentId) {
            $parent = MenuItem::where('menu_id', $menu->id)->findOrFail($newParentId);
            $item->appendToNode($parent)->save();
        } else {
            $item->saveAsRoot();
        }

        $siblings = $newParentId
            ? MenuItem::where('menu_id', $menu->id)->where('parent_id', $newParentId)->defaultOrder()->get()
            : MenuItem::where('menu_id', $menu->id)->whereNull('parent_id')->defaultOrder()->get();

        $siblings = $siblings->reject(fn (MenuItem $s) => $s->id === $item->id)->values();
        $siblings->splice($position, 0, [$item]);

        $previous = null;
        foreach ($siblings as $sibling) {
            if ($previous) {
                $sibling->afterNode($previous)->save();
            }
            $previous = $sibling->fresh();
        }
    }

    /**
     * Convierte el árbol Eloquent (nestedset) a la estructura que espera jsTree.
     *
     * @param  Collection<int, MenuItem>  $nodes
     * @return array<int, array<string, mixed>>
     */
    public function jsTreeData(Collection $nodes): array
    {
        return $nodes->map(function (MenuItem $node) {
            return [
                'id' => $node->id,
                'text' => $node->label,
                'icon' => $node->icon ? 'icon-base ti tabler-'.$node->icon : 'icon-base ti tabler-link',
                'state' => ['opened' => true, 'disabled' => ! $node->is_active],
                'li_attr' => [
                    'data-label' => $node->label,
                    'data-type' => $node->type,
                    'data-url' => $node->url,
                    'data-route-name' => $node->route_name,
                    'data-page-id' => $node->page_id,
                    'data-icon' => $node->icon,
                    'data-target' => $node->target,
                    'data-is-active' => $node->is_active ? 1 : 0,
                    'data-resolved-url' => $node->resolvedUrl(),
                ],
                'children' => $this->jsTreeData($node->children),
            ];
        })->values()->all();
    }

    public function validateItemBelongsToMenu(Menu $menu, MenuItem $item): void
    {
        if ($item->menu_id !== $menu->id) {
            throw new BusinessException('El ítem no pertenece a este menú.');
        }
    }
}
