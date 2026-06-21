<?php

namespace App\Actions\Admin\User;

use App\Exceptions\BusinessException;
use App\Models\User;

class DeleteUser
{
    public function handle(User $user): void
    {
        if ($user->id === auth()->id()) {
            throw new BusinessException('No puedes eliminar tu propia cuenta.');
        }

        if ($user->hasRole('Super-Admin')) {
            throw new BusinessException('No se puede eliminar al Super-Admin.');
        }

        $user->delete();
    }
}
