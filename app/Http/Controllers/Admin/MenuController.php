<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Menu\StoreMenuItemRequest;
use App\Http\Requests\Admin\Menu\StoreMenuRequest;
use App\Http\Requests\Admin\Menu\UpdateMenuRequest;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\Admin\MenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return view('admin.menus.edit', compact('menu'));
    }

    public function tree(Menu $menu): JsonResponse
    {
        $this->authorize('update', $menu);

        return response()->json($this->menuService->jsTreeData($menu->tree()));
    }

    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        $this->authorize('update', $menu);

        $this->menuService->update($menu, $request->validated());

        $this->flashSuccess('Menú actualizado correctamente.');

        return redirect()->route('admin.menus.edit', $menu);
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $this->authorize('delete', $menu);

        $this->menuService->delete($menu);

        $this->flashSuccess('Menú eliminado correctamente.');

        return redirect()->route('admin.menus.index');
    }

    public function storeItem(StoreMenuItemRequest $request, Menu $menu): JsonResponse
    {
        $this->authorize('update', $menu);

        $item = $this->menuService->createItem($menu, $request->validated());

        return response()->json(['message' => 'Ítem agregado correctamente.', 'item' => $item]);
    }

    public function updateItem(StoreMenuItemRequest $request, Menu $menu, MenuItem $item): JsonResponse
    {
        $this->authorize('update', $menu);

        $this->menuService->validateItemBelongsToMenu($menu, $item);
        $this->menuService->updateItem($item, $request->validated());

        return response()->json(['message' => 'Ítem actualizado correctamente.']);
    }

    public function destroyItem(Menu $menu, MenuItem $item): JsonResponse
    {
        $this->authorize('update', $menu);

        $this->menuService->validateItemBelongsToMenu($menu, $item);
        $this->menuService->deleteItem($item);

        return response()->json(['message' => 'Ítem eliminado correctamente.']);
    }

    public function move(Request $request, Menu $menu): JsonResponse
    {
        $this->authorize('update', $menu);

        $data = $request->validate([
            'id' => 'required|integer',
            'parent' => 'nullable|integer',
            'position' => 'required|integer|min:0',
        ]);

        $this->menuService->moveNode($menu, $data['id'], $data['parent'] ?? null, $data['position']);

        return response()->json(['message' => 'Orden actualizado correctamente.']);
    }
}
