<?php

namespace App\Services\Admin;

use App\Actions\Admin\User\BulkUserAction;
use App\Actions\Admin\User\CreateUser;
use App\Actions\Admin\User\DeleteUser;
use App\Actions\Admin\User\ForceDeleteUser;
use App\Actions\Admin\User\UpdateUser;
use App\Actions\Admin\User\VerifyUserEmail;
use App\Enums\UserStatus;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserService
{
    public function __construct(
        private readonly CreateUser $createUser,
        private readonly UpdateUser $updateUser,
        private readonly DeleteUser $deleteUser,
        private readonly ForceDeleteUser $forceDeleteUser,
        private readonly VerifyUserEmail $verifyUserEmail,
        private readonly BulkUserAction $bulkUserAction,
    ) {}

    public function paginate(Request $request): LengthAwarePaginator
    {
        /** @var Builder $query */
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
            'total' => User::count(),
            'active' => User::where('status', UserStatus::Active->value)->count(),
            'inactive' => User::where('status', UserStatus::Inactive->value)->count(),
            'banned' => User::where('status', UserStatus::Banned->value)->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'sin_acceso' => User::where(fn ($q) => $q->whereNull('last_login_at')->orWhere('last_login_at', '<', now()->subDays(30)))->count(),
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

        $user->notify(new SystemNotification(
            title: 'Bienvenido a '.setting('site_name', config('app.name')),
            message: 'Tu cuenta fue creada correctamente. Completa tu perfil para empezar.',
            icon: 'tabler-confetti',
            color: 'success',
            url: route('admin.profile.show'),
        ));

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

    public function forceDelete(User $user): void
    {
        $name = $user->name;
        $this->forceDeleteUser->handle($user);

        activity('usuarios')
            ->causedBy(auth()->user())
            ->event('force_deleted')
            ->log("Usuario '{$name}' eliminado permanentemente.");
    }

    public function verifyEmail(User $user): void
    {
        $this->verifyUserEmail->handle($user);

        activity('usuarios')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->event('email_verified')
            ->log("Email de '{$user->name}' verificado manualmente por administrador.");
    }

    public function resendVerification(User $user): void
    {
        $user->sendEmailVerificationNotification();

        activity('usuarios')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->event('verification_resent')
            ->log("Reenvío de verificación de email a '{$user->name}'.");
    }

    public function bulkAction(array $ids, string $action): array
    {
        $result = $this->bulkUserAction->handle($ids, $action);

        activity('usuarios')
            ->causedBy(auth()->user())
            ->withProperties(['action' => $action, 'ids' => $ids, 'processed' => $result['processed']])
            ->event('bulk_action')
            ->log("Acción masiva '{$action}' aplicada a {$result['processed']} usuarios.");

        return $result;
    }

    public function resetPassword(User $user): void
    {
        // Usar DNI como contraseña si existe, sino generar temporal
        $newPassword = $user->perfil?->dni ?? Str::random(10);

        $user->update(['password' => Hash::make($newPassword)]);

        $esDni = $user->perfil?->dni !== null;
        $mensaje = $esDni
            ? "Tu nueva contraseña es tu número de DNI: <strong>{$newPassword}</strong>"
            : "Tu nueva contraseña temporal es: <strong>{$newPassword}</strong>";

        Mail::send([], [], function ($message) use ($user, $mensaje) {
            $message->to($user->email, $user->name)
                ->subject('Tu contraseña ha sido restablecida — '.config('app.name'))
                ->html(
                    "<p>Hola <strong>{$user->name}</strong>,</p>".
                    '<p>Un administrador ha restablecido tu contraseña.</p>'.
                    "<p>{$mensaje}</p>".
                    '<p>Por seguridad, te recomendamos cambiarla después de iniciar sesión.</p>'.
                    '<p>— Equipo de Sistemas</p>'
                );
        });

        activity('usuarios')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->event('password_reset')
            ->log("Contraseña de '{$user->name}' restablecida por administrador.");
    }

    public function resetTwoFactor(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_remember_token' => null,
        ])->save();

        activity('usuarios')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->event('two_factor_reset')
            ->log("2FA de '{$user->name}' restablecido por administrador.");
    }

    public function unlock(User $user): void
    {
        $user->forceFill([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ])->save();

        activity('usuarios')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->event('account_unlocked')
            ->log("Cuenta de '{$user->name}' desbloqueada por administrador.");
    }

    public function forceLogout(User $user): void
    {
        DB::table('sessions')->where('user_id', $user->id)->delete();

        activity('usuarios')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->event('force_logout')
            ->log("Sesiones de '{$user->name}' cerradas por administrador.");
    }
}
