<?php

namespace App\Services\Admin;

use App\Actions\Admin\User\CreateUser;
use App\Actions\Admin\User\DeleteUser;
use App\Actions\Admin\User\UpdateUser;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

class UserService
{
    public function __construct(
        private readonly CreateUser $createUser,
        private readonly UpdateUser $updateUser,
        private readonly DeleteUser $deleteUser,
    ) {}

    public function paginate(\Illuminate\Http\Request $request): LengthAwarePaginator
    {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = User::query();

        return $query
            ->filter($request)
            ->with('roles')
            ->withTrashed()
            ->latest()
            ->paginate(config('app-settings.pagination.default', 15))
            ->withQueryString();
    }

    public function stats(): array
    {
        return [
            'total'    => User::count(),
            'active'   => User::where('status', UserStatus::Active->value)->count(),
            'inactive' => User::where('status', UserStatus::Inactive->value)->count(),
            'banned'   => User::where('status', UserStatus::Banned->value)->count(),
        ];
    }

    public function create(array $data, ?UploadedFile $avatar = null): User
    {
        $user = $this->createUser->handle($data, $avatar);

        activity('usuarios')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties(['role' => $data['role'] ?? null])
            ->event('created')
            ->log("Usuario '{$user->name}' creado.");

        return $user;
    }

    public function update(User $user, array $data, ?UploadedFile $avatar = null): User
    {
        $updated = $this->updateUser->handle($user, $data, $avatar);

        activity('usuarios')
            ->causedBy(auth()->user())
            ->performedOn($updated)
            ->event('updated')
            ->log("Usuario '{$updated->name}' actualizado.");

        return $updated;
    }

    public function delete(User $user): void
    {
        $name = $user->name;
        $this->deleteUser->handle($user);

        activity('usuarios')
            ->causedBy(auth()->user())
            ->event('deleted')
            ->log("Usuario '{$name}' eliminado.");
    }

    public function restore(User $user): void
    {
        $user->restore();

        activity('usuarios')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->event('restored')
            ->log("Usuario '{$user->name}' restaurado.");
    }
}
