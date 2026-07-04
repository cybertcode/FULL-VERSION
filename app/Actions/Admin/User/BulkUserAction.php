<?php

namespace App\Actions\Admin\User;

use App\Enums\UserStatus;
use App\Exceptions\BusinessException;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class BulkUserAction
{
    public function handle(array $ids, string $action): array
    {
        $currentId = auth()->id();

        $users = User::withTrashed()->whereIn('id', $ids)->get();

        if ($users->isEmpty()) {
            throw new BusinessException('No se encontraron usuarios seleccionados.');
        }

        $processed = 0;
        $skipped = 0;

        foreach ($users as $user) {
            // Nunca tocar al Super-Admin ni al usuario autenticado en acciones destructivas
            if ($user->hasRole('Super-Admin') || ($user->id === $currentId && in_array($action, ['delete', 'force_delete', 'ban']))) {
                $skipped++;

                continue;
            }

            match ($action) {
                'activate' => $user->update(['status' => UserStatus::Active]),
                'deactivate' => $user->update(['status' => UserStatus::Inactive]),
                'ban' => $user->update(['status' => UserStatus::Banned]),
                'delete' => $user->trashed() ? null : $user->delete(),
                'restore' => $user->trashed() ? $user->restore() : null,
                'force_delete' => $this->forceDeleteSafe($user),
                'verify_email' => $user->hasVerifiedEmail() ? null : $user->forceFill(['email_verified_at' => now()])->save(),
                default => throw new BusinessException("Acción desconocida: {$action}"),
            };

            $processed++;
        }

        return ['processed' => $processed, 'skipped' => $skipped];
    }

    private function forceDeleteSafe(User $user): void
    {
        if ($user->avatar) {
            Storage::delete($user->avatar);
        }
        $user->forceDelete();
    }
}
