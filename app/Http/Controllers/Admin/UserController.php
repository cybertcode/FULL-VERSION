<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends BaseAdminController
{
    public function __construct(private readonly UserService $userService)
    {
        parent::__construct();
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $users  = $this->userService->paginate($request);
        $stats  = $this->userService->stats();
        $roles  = Role::orderBy('name')->pluck('name', 'name');
        $statuses = UserStatus::cases();

        return view('admin.users.index', compact('users', 'stats', 'roles', 'statuses'));
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

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $roles    = Role::where('name', '!=', 'Super-Admin')->orderBy('name')->pluck('name', 'name');
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
}
