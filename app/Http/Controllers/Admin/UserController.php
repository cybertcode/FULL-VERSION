<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends BaseAdminController
{
    public function __construct(private readonly UserService $userService)
    {
        parent::__construct();
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $stats    = $this->userService->stats();
        $roles    = Role::orderBy('name')->pluck('name', 'name');
        $statuses = UserStatus::cases();

        return view('admin.users.index', compact('stats', 'roles', 'statuses'));
    }

    /**
     * Endpoint AJAX para DataTable — devuelve JSON con todos los usuarios.
     */
    public function data(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $users = User::with('roles')
            ->withTrashed()
            ->filter($request)
            ->latest()
            ->get()
            ->map(fn (User $u) => [
                'id'          => $u->id,
                'name'        => $u->name,
                'username'    => $u->username,
                'email'       => $u->email,
                'avatar_url'  => $u->avatar_url,
                'role'        => $u->roles->first()?->name ?? '—',
                'status'      => $u->status?->value,
                'status_label'=> $u->status?->label(),
                'status_class'=> $u->status?->badgeClass(),
                'deleted_at'  => $u->deleted_at?->toDateTimeString(),
                'show_url'           => route('admin.users.show', $u),
                'edit_url'           => $u->deleted_at ? null : route('admin.users.edit', $u),
                'delete_url'         => $u->deleted_at ? null : route('admin.users.destroy', $u),
                'restore_url'        => $u->deleted_at ? route('admin.users.restore', $u->id) : null,
                'reset_password_url' => $u->deleted_at ? null : route('admin.users.reset-password', $u),
            ]);

        return response()->json(['data' => $users]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $roles    = Role::where('name', '!=', 'Super-Admin')->orderBy('name')->pluck('name', 'name');
        $statuses = UserStatus::cases();

        return view('admin.users.create', compact('roles', 'statuses'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $this->userService->create(
            $request->validated(),
            $request->hasFile('avatar') ? $request->file('avatar') : null
        );

        $this->flashSuccess('Usuario creado correctamente.');

        return redirect()->route('admin.users.index');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);
        $user->load('perfil', 'roles');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $user->load('perfil', 'roles');
        // Incluir todos los roles; Super-Admin solo aparece si el usuario ya lo tiene
        $roles = Role::orderBy('name')->pluck('name', 'name');
        $statuses = UserStatus::cases();

        return view('admin.users.edit', compact('user', 'roles', 'statuses'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $this->userService->update(
            $user,
            $request->validated(),
            $request->hasFile('avatar') ? $request->file('avatar') : null
        );

        $this->flashSuccess('Usuario actualizado correctamente.');

        return redirect()->route('admin.users.index');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->userService->delete($user);

        $this->flashSuccess('Usuario eliminado correctamente.');

        return redirect()->route('admin.users.index');
    }

    public function restore(int $id): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        $this->authorize('restore', $user);

        $this->userService->restore($user);

        $this->flashSuccess('Usuario restaurado correctamente.');

        return redirect()->route('admin.users.index');
    }

    public function resetPassword(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $this->userService->resetPassword($user);

        return response()->json(['message' => "Contraseña de {$user->name} restablecida correctamente."]);
    }
}
