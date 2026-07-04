<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // El filtrado por permisos requiere el usuario autenticado, que no existe
        // aún en boot() — por eso se resuelve en un composer (render time) y se
        // memoiza para no repetir el trabajo en cada partial de la misma request.
        View::composer('*', function ($view) {
            $view->with('menuData', $this->menuData());
        });
    }

    /**
     * Menús vertical y horizontal con los items filtrados por permiso.
     */
    protected function menuData(): array
    {
        static $menuData = null;

        if ($menuData === null) {
            $verticalMenuData = json_decode(file_get_contents(base_path('resources/menu/verticalMenu.json')));
            $horizontalMenuData = json_decode(file_get_contents(base_path('resources/menu/horizontalMenu.json')));

            $verticalMenuData->menu = $this->filterByPermission($verticalMenuData->menu ?? []);
            $horizontalMenuData->menu = $this->filterByPermission($horizontalMenuData->menu ?? []);

            $menuData = [$verticalMenuData, $horizontalMenuData];
        }

        return $menuData;
    }

    /**
     * Filtra recursivamente los items del menú según la clave "permission".
     * - Item sin "permission" → visible para cualquier usuario autenticado.
     * - Item con "permission" → visible solo si el usuario tiene el permiso
     *   (Super-Admin pasa siempre vía Gate::before).
     * - Un item padre sin URL propia se oculta si todos sus hijos quedan ocultos.
     * - Los menuHeader huérfanos (sin items visibles debajo) se eliminan.
     */
    protected function filterByPermission(array $items): array
    {
        $user = auth()->user();
        $visible = [];

        foreach ($items as $item) {
            if (isset($item->permission) && ! ($user?->can($item->permission))) {
                continue;
            }

            if (isset($item->submenu)) {
                $item->submenu = $this->filterByPermission($item->submenu);
                if (empty($item->submenu) && ! isset($item->url)) {
                    continue;
                }
            }

            $visible[] = $item;
        }

        // Eliminar headers sin items visibles hasta el siguiente header
        $result = [];
        $count = count($visible);
        foreach ($visible as $i => $item) {
            if (isset($item->menuHeader)) {
                $next = $visible[$i + 1] ?? null;
                if ($next === null || isset($next->menuHeader)) {
                    continue;
                }
            }
            $result[] = $item;
        }

        return $result;
    }
}
