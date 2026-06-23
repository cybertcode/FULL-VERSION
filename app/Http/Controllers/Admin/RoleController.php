<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Role\StoreRoleRequest;
use App\Http\Requests\Admin\Role\UpdateRoleRequest;
use App\Models\Role;
use App\Models\User;
use App\Enums\UserStatus;
use App\Services\Admin\ExportService;
use App\Services\Admin\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RoleController extends BaseAdminController
{
    public function __construct(
        private readonly RoleService $roleService,
        private readonly ExportService $exportService,
    ) {
        parent::__construct();
    }

    public function index(): View
    {
        $this->authorize('viewAny', Role::class);

        $roles              = $this->roleService->all();
        $permissionsGrouped = $this->roleService->allPermissionsGrouped();
        $riskStats          = $this->roleService->riskStats();
        $assignableRoles    = $roles->where('name', '!=', 'Super-Admin')->pluck('name');
        $statuses           = UserStatus::cases();

        return view('admin.roles.index', compact('roles', 'permissionsGrouped', 'riskStats', 'assignableRoles', 'statuses'));
    }

    public function usersData(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Role::class);

        $query = User::with('roles', 'perfil')
            ->withTrashed(false)
            ->select('users.*');

        if ($role = $request->input('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $role));
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $total = (clone $query)->count();

        if ($search = $request->input('search.value')) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            );
        }

        $filtered = (clone $query)->count();

        $rows = $query
            ->orderByDesc('users.created_at')
            ->offset($request->input('start', 0))
            ->limit($request->input('length', 10))
            ->get()
            ->map(fn (User $u) => [
                'id'              => $u->id,
                'name'            => $u->name,
                'email'           => $u->email,
                'avatar_url'      => $u->avatar_url,
                'cargo'           => $u->perfil?->cargo,
                'role'            => $u->roles->first()?->name ?? '—',
                'last_login_at'   => $u->last_login_at?->diffForHumans() ?? null,
                'created_at'      => $u->created_at->format('d/m/Y'),
                'status'          => $u->status?->value,
                'status_label'    => $u->status?->label(),
                'status_class'    => $u->status?->badgeClass(),
                'show_url'        => route('admin.users.show', $u),
                'history_url'     => route('admin.roles.users.history', $u),
                'assign_url'      => route('admin.roles.users.assign', $u),
            ]);

        return response()->json([
            'draw'            => (int) $request->input('draw', 1),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $rows,
        ]);
    }

    public function assignRole(Request $request, User $user): JsonResponse
    {
        $this->authorize('edit', $user);

        $request->validate(['role' => 'required|string|exists:roles,name']);

        $this->roleService->assignRole($user, $request->input('role'));

        return response()->json(['message' => "Rol actualizado correctamente."]);
    }

    public function roleHistory(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return response()->json([
            'user'    => $user->name,
            'history' => $this->roleService->roleHistory($user),
        ]);
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

    public function exportPdf(Request $request): Response
    {
        $this->authorize('viewAny', Role::class);
        return $this->exportService->exportRolesPdf($request);
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $this->authorize('viewAny', Role::class);
        return $this->exportService->exportRolesExcel($request);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Role::class);
        return $this->exportService->exportRolesCsv($request);
    }

    public function bulkAssign(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Role::class);

        $request->validate([
            'user_ids'   => 'required|array|min:1|max:100',
            'user_ids.*' => 'integer|exists:users,id',
            'role'       => 'required|string|exists:roles,name',
        ]);

        ['assigned' => $assigned, 'skipped' => $skipped] = $this->roleService->bulkAssignRole(
            $request->input('user_ids'),
            $request->input('role')
        );

        return response()->json([
            'message' => "Rol asignado a {$assigned} usuario(s)." . ($skipped ? " {$skipped} omitido(s) (Super-Admin)." : ''),
            'assigned' => $assigned,
            'skipped'  => $skipped,
        ]);
    }

    public function roleChangeHistory(Role $role): JsonResponse
    {
        $this->authorize('viewAny', Role::class);

        return response()->json([
            'role'    => $role->name,
            'history' => $this->roleService->roleChangeHistory($role),
        ]);
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        $this->roleService->delete($role);

        $this->flashSuccess('Rol eliminado correctamente.');

        return redirect()->route('admin.roles.index');
    }
}

