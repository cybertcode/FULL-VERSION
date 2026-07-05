<?php

namespace App\Services\Admin;

use App\Enums\MenuLocation;
use App\Enums\PageStatus;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuLocationAssignment;
use App\Models\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

    public function delete(Menu $menu): void
    {
        $menu->allItems()->get()->each->delete();
        $menu->delete();
    }

    /**
     * Zonas ya asignadas a ESTE menú específico — para marcar sus checkboxes
     * en el bloque "Ubicación" de la pantalla de edición (estilo WordPress
     * "Menu Settings > Display location").
     *
     * @return array<int, string>
     */
    public function locationsAssignedTo(Menu $menu): array
    {
        return MenuLocationAssignment::where('menu_id', $menu->id)
            ->pluck('location')
            ->map(fn (MenuLocation $l) => $l->value)
            ->all();
    }

    /**
     * Páginas del CMS (frontend) publicadas — un menú de navegación pública
     * solo debe poder enlazar a contenido real del sitio, nunca a pantallas
     * del panel administrativo.
     *
     * @return Collection<int, Page>
     */
    public function selectablePagesForMenu(): Collection
    {
        return Page::where('status', PageStatus::Published->value)
            ->orderBy('title')
            ->get(['id', 'title', 'slug']);
    }

    /**
     * Guarda el menú completo en un solo paso, estilo WordPress "Guardar menú":
     * datos del menú, ítems (altas/bajas/cambios), su jerarquía y las ubicaciones
     * asignadas, todo en una transacción.
     *
     * @param  array<int, array<string, mixed>>  $items  planos, con client_id/parent_client_id para reconstruir el árbol
     * @param  array<int>  $deletedIds
     * @param  array<string, bool>  $locations  ['header' => true, 'footer' => false, ...]
     */
    public function saveStructure(Menu $menu, string $name, array $items, array $deletedIds, array $locations): Menu
    {
        DB::transaction(function () use ($menu, $name, $items, $deletedIds, $locations) {
            $menu->update(['name' => $name]);

            if (! empty($deletedIds)) {
                MenuItem::where('menu_id', $menu->id)->whereIn('id', $deletedIds)->get()->each->delete();
            }

            $clientIdToModel = [];

            // Alta/actualización de datos propios (sin jerarquía todavía)
            foreach ($items as $itemData) {
                $attributes = collect($itemData)->only([
                    'label', 'type', 'url', 'page_id', 'icon', 'target', 'is_active',
                ])->all();

                $model = ! empty($itemData['id'])
                    ? MenuItem::where('menu_id', $menu->id)->findOrFail($itemData['id'])
                    : new MenuItem(['menu_id' => $menu->id]);

                $model->fill($attributes);

                if (! $model->exists) {
                    $model->saveAsRoot();
                } else {
                    $model->save();
                }

                $clientIdToModel[$itemData['client_id']] = $model;
            }

            // Jerarquía y orden — se aplica después de que todos los modelos ya existen,
            // ordenando por 'order' para reconstruir hermanos en secuencia correcta.
            $sorted = collect($items)->sortBy('order')->values();
            $roots = $sorted->whereNull('parent_client_id')->values();
            $this->reattachChildren($roots, $sorted, $clientIdToModel);

            $this->updateLocationAssignments($menu, $locations);
        });

        return $menu->fresh();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $siblings
     * @param  Collection<int, array<string, mixed>>  $all
     * @param  array<string, MenuItem>  $clientIdToModel
     */
    private function reattachChildren(Collection $siblings, Collection $all, array $clientIdToModel): void
    {
        $previous = null;

        foreach ($siblings as $itemData) {
            // Siempre releer fresco de BD antes de mover: el modelo cacheado en
            // $clientIdToModel puede tener _lft/_rgt obsoletos si ya fue movido
            // en una iteración anterior de esta misma recursión (ej. su propio
            // padre reordenado como hermano) — usar la instancia en memoria aquí
            // causa "Node must not be a descendant" en árboles de 3+ niveles.
            $model = $clientIdToModel[$itemData['client_id']]->fresh();

            if ($previous) {
                $model->afterNode($previous)->save();
            } elseif ($itemData['parent_client_id'] ?? null) {
                $parent = $clientIdToModel[$itemData['parent_client_id']]->fresh();
                $model->appendToNode($parent)->save();
            } else {
                $model->saveAsRoot();
            }

            $previous = $model->fresh();

            $children = $all->where('parent_client_id', $itemData['client_id'])->values();
            if ($children->isNotEmpty()) {
                $this->reattachChildren($children, $all, $clientIdToModel);
            }
        }
    }

    /**
     * @param  array<string, bool>  $locations
     */
    private function updateLocationAssignments(Menu $menu, array $locations): void
    {
        foreach ($locations as $location => $enabled) {
            if ($enabled) {
                MenuLocationAssignment::updateOrCreate(
                    ['location' => $location],
                    ['menu_id' => $menu->id]
                );
            } else {
                // Solo desasignar si la zona apuntaba a ESTE menú — si otro menú
                // ya la tiene asignada, no se debe tocar (el checkbox desmarcado
                // significa "este menú no va aquí", no "vacía la zona").
                MenuLocationAssignment::where('location', $location)
                    ->where('menu_id', $menu->id)
                    ->update(['menu_id' => null]);
            }
        }
    }
}
