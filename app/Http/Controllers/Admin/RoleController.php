<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Role\StoreRoleRequest;
use App\Http\Requests\Admin\Role\UpdateRoleRequest;
use App\Services\Admin\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController extends BaseAdminController
{
    public function __construct(private readonly RoleService $roleService)
    {
        parent::__construct();
    }

    public function index(): View
    {
        $this->authorize('viewAny', Role::class);

        $roles              = $this->roleService->all();
        $permissionsGrouped = $this->roleService->allPermissionsGrouped();

        return view('admin.roles.index', compact('roles', 'permissionsGrouped'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->authorize('create', Role::class);

        $this->roleService->create($request->validated());

        $this->flashSuccess('Rol creado correctamente.');

        return redirect()->route('admin.roles.index');
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->authorize('update', $role);

        $this->roleService->update($role, $request->validated());

        $this->flashSuccess('Rol actualizado correctamente.');

        return redirect()->route('admin.roles.index');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        $this->roleService->delete($role);

        $this->flashSuccess('Rol eliminado correctamente.');

        return redirect()->route('admin.roles.index');
    }
}
