<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MenuLocation;
use App\Http\Requests\Admin\Menu\SaveMenuStructureRequest;
use App\Http\Requests\Admin\Menu\StoreMenuRequest;
use App\Models\Menu;
use App\Services\Admin\MenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MenuController extends BaseAdminController
{
    public function __construct(
        private readonly MenuService $menuService,
    ) {
        parent::__construct();
    }

    public function index(): View
    {
        $this->authorize('viewAny', Menu::class);

        $menus = $this->menuService->all();

        return view('admin.menus.index', compact('menus'));
    }

    public function store(StoreMenuRequest $request): RedirectResponse
    {
        $this->authorize('create', Menu::class);

        $menu = $this->menuService->create($request->validated());

        $this->flashSuccess('Menú creado correctamente.');

        return redirect()->route('admin.menus.edit', $menu);
    }

    public function edit(Menu $menu): View
    {
        $this->authorize('update', $menu);

        return view('admin.menus.edit', [
            'menu' => $menu,
            'tree' => $menu->tree(),
            'pages' => $this->menuService->selectablePagesForMenu(),
            'locations' => MenuLocation::cases(),
            'assignedLocations' => $this->menuService->locationsAssignedTo($menu),
        ]);
    }

    public function update(SaveMenuStructureRequest $request, Menu $menu): RedirectResponse
    {
        $this->authorize('update', $menu);

        $data = $request->validated();

        $this->menuService->saveStructure(
            $menu,
            $data['name'],
            $data['items'] ?? [],
            $data['deleted_ids'] ?? [],
            $data['locations'] ?? [],
        );

        $this->flashSuccess('Menú guardado correctamente.');

        return redirect()->route('admin.menus.edit', $menu);
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $this->authorize('delete', $menu);

        $this->menuService->delete($menu);

        $this->flashSuccess('Menú eliminado correctamente.');

        return redirect()->route('admin.menus.index');
    }
}
