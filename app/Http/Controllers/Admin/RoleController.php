<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Role\StoreRoleRequest;
use App\Http\Requests\Admin\Role\UpdateRoleRequest;
use App\Models\User;
use App\Services\Admin\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Role;
use App\Enums\UserStatus;

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

        if ($search = $request->input('search.value')) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            );
        }

        $total    = (clone $query)->count();
        $filtered = $total;

        $rows = $query
            ->orderBy('name')
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

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        $this->roleService->delete($role);

        $this->flashSuccess('Rol eliminado correctamente.');

        return redirect()->route('admin.roles.index');
    }
}

