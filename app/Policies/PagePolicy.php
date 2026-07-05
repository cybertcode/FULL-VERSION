<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('pages.viewAny');
    }

    public function view(User $user, Page $_page): bool
    {
        return $user->can('pages.view');
    }

    public function create(User $user): bool
    {
        return $user->can('pages.create');
    }

    public function update(User $user, Page $_page): bool
    {
        return $user->can('pages.edit');
    }

    public function delete(User $user, Page $_page): bool
    {
        return $user->can('pages.delete');
    }

    public function restore(User $user, Page $_page): bool
    {
        return $user->can('pages.restore');
    }

    public function forceDelete(User $user, Page $_page): bool
    {
        return $user->can('pages.forceDelete');
    }
}
