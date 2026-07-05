<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BusinessException;
use App\Http\Requests\Admin\Permission\StorePermissionRequest;
use App\Http\Requests\Admin\Permission\UpdatePermissionRequest;
use App\Models\Permission;
use App\Services\Admin\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PermissionController extends BaseAdminController
{
    public function __construct(
        private readonly ExportService $exportService,
    ) {
        parent::__construct();
    }

    public function index(): View
    {
        $this->authorize('viewAny', Permission::class);

        return view('admin.permissions.index', [
            'canEditPermissions' => auth()->user()->can('permissions.edit'),
            'canDeletePermissions' => auth()->user()->can('permissions.delete'),
            'permissionModules' => config('app-settings.permission_modules', []),
        ]);
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $this->authorize('create', Permission::class);

        $name = $request->validated('module').'.'.$request->validated('action');

        if (Permission::where('name', $name)->where('guard_name', 'web')->exists()) {
            throw new BusinessException("Ya existe el permiso «{$name}».");
        }

        Permission::create([
            'name' => $name,
            'guard_name' => 'web',
            'label' => $request->validated('label') ?: null,
        ]);

        activity('permisos')
            ->withProperties(['name' => $name])
            ->log("Permiso «{$name}» creado.");

        $this->flashSuccess('Permiso creado correctamente.');

        return redirect()->route('admin.permissions.index');
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $this->authorize('update', $permission);

        $permission->update(['label' => $request->validated('label') ?: null]);

        activity('permisos')
            ->withProperties(['name' => $permission->name])
            ->log("Permiso «{$permission->name}» actualizado.");

        $this->flashSuccess('Permiso actualizado correctamente.');

        return redirect()->route('admin.permissions.index');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $this->authorize('delete', $permission);

        if ($permission->roles()->exists()) {
            throw new BusinessException('No puedes eliminar un permiso asignado a roles. Quítalo de los roles primero.');
        }

        $name = $permission->name;
        $permission->delete();

        activity('permisos')
            ->withProperties(['name' => $name])
            ->log("Permiso «{$name}» eliminado.");

        $this->flashSuccess('Permiso eliminado correctamente.');

        return redirect()->route('admin.permissions.index');
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Permission::class);

        $query = Permission::with('roles')->orderBy('name');

        if ($module = $request->input('module')) {
            $query->where('name', 'like', $module.'.%');
        }

        $permissions = $query->get()->map(fn (Permission $p) => [
            'id' => $p->id,
            'name' => $p->name,
            'label' => $p->label ?? $p->name,
            'module' => explode('.', $p->name)[0],
            'action' => explode('.', $p->name)[1] ?? '',
            'roles' => $p->roles->pluck('name'),
            'created_at' => $p->created_at?->format('d/m/Y'),
        ]);

        return response()->json(['data' => $permissions]);
    }

    public function exportPdf(Request $request): Response
    {
        $this->authorize('viewAny', Permission::class);

        return $this->exportService->exportPermissionsPdf($request);
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $this->authorize('viewAny', Permission::class);

        return $this->exportService->exportPermissionsExcel($request);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Permission::class);

        return $this->exportService->exportPermissionsCsv($request);
    }
}
