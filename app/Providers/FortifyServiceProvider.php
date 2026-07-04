<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\RedirectIfTwoFactorAuthenticatable;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Fortify\ValidateCaptcha;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\CanonicalizeUsername;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Registro explícito de todas las vistas de autenticación
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));
        Fortify::requestPasswordResetLinkView(fn () => view('auth.forgot-password'));
        Fortify::resetPasswordView(fn ($request) => view('auth.reset-password', ['request' => $request]));
        Fortify::verifyEmailView(fn () => view('auth.verify-email'));
        Fortify::confirmPasswordView(fn () => view('auth.confirm-password'));
        Fortify::twoFactorChallengeView(fn () => view('auth.two-factor-challenge'));

        Fortify::authenticateThrough(function () {
            return [
                EnsureLoginIsNotThrottled::class,
                CanonicalizeUsername::class,
                ValidateCaptcha::class,
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
            ];
        });

        // Permite iniciar sesión con email, username o DNI (perfil.dni) en el mismo campo.
        // También aplica bloqueo temporal de cuenta tras N intentos fallidos consecutivos (login_lockout_minutes).
        Fortify::authenticateUsing(function (Request $request) {
            $login = (string) $request->input(Fortify::username());

            $user = User::query()
                ->where('email', $login)
                ->orWhere('username', $login)
                ->orWhereHas('perfil', fn ($query) => $query->where('dni', $login))
                ->first();

            if (! $user) {
                return null;
            }

            if ($user->isLocked()) {
                return null;
            }

            if (Hash::check($request->input('password'), $user->password)) {
                if ($user->failed_login_attempts > 0 || $user->locked_until !== null) {
                    $user->forceFill(['failed_login_attempts' => 0, 'locked_until' => null])->save();
                }

                return $user;
            }

            $maxAttempts = (int) (function_exists('setting') ? setting('login_max_attempts', 5) : 5);
            $lockoutMinutes = (int) (function_exists('setting') ? setting('login_lockout_minutes', 15) : 15);

            $attempts = $user->failed_login_attempts + 1;
            $update = ['failed_login_attempts' => $attempts];

            if ($attempts >= $maxAttempts) {
                $update['locked_until'] = now()->addMinutes($lockoutMinutes);
                $update['failed_login_attempts'] = 0;
            }

            $user->forceFill($update)->save();

            return null;
        });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());
            $maxAttempts = (int) (function_exists('setting') ? setting('login_max_attempts', 5) : 5);

            return Limit::perMinute($maxAttempts)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip()
            );
        });
    }
}
