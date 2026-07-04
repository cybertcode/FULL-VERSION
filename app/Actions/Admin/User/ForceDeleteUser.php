<?php

namespace App\Actions\Admin\User;

use App\Exceptions\BusinessException;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ForceDeleteUser
{
    public function handle(User $user): void
    {
        if ($user->id === auth()->id()) {
            throw new BusinessException('No puedes eliminar permanentemente tu propia cuenta.');
        }

        if ($user->hasRole('Super-Admin')) {
            throw new BusinessException('No se puede eliminar permanentemente al Super-Admin.');
        }

        // Eliminar avatar y foto de perfil si existen
        if ($user->avatar) {
            Storage::delete($user->avatar);
        }

        if ($user->perfil?->foto_perfil) {
            Storage::delete($user->perfil->foto_perfil);
        }

        $user->forceDelete();
    }
}
