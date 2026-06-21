<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PermissionController extends BaseAdminController
{
    public function index(): View
    {
        $this->authorize('viewAny', Permission::class);

        return view('admin.permissions.index');
    }

    /**
     * Endpoint AJAX para DataTable — devuelve JSON con todos los permisos.
     */
    public function data(): JsonResponse
    {
        $this->authorize('viewAny', Permission::class);

        $permissions = Permission::with('roles')
            ->orderBy('name')
            ->get()
            ->map(fn (Permission $p) => [
                'id'         => $p->id,
                'name'       => $p->name,
                'label'      => $p->label ?? $p->name,
                'module'     => explode('.', $p->name)[0],
                'action'     => explode('.', $p->name)[1] ?? '',
                'roles'      => $p->roles->pluck('name'),
                'created_at' => $p->created_at?->format('d/m/Y'),
            ]);

        return response()->json(['data' => $permissions]);
    }
}
