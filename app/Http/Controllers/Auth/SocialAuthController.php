<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\RememberTwoFactorDevice;
use App\Models\SocialAccount;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\Jetstream;
use Laravel\Socialite\AbstractUser;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

/**
 * Autenticación social (Google / GitHub / Facebook) vía Laravel Socialite.
 *
 * Nota: la creación de team personal duplica la lógica de
 * App\Actions\Fortify\CreateNewUser::createTeam() porque ese método es
 * protected y pertenece al flujo de registro por formulario de Fortify.
 */
class SocialAuthController extends Controller
{
    private const PROVIDERS = ['google', 'github', 'facebook'];

    public function redirect(string $provider): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);
        abort_unless((bool) setting("social_{$provider}_enabled", false), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);
        abort_unless((bool) setting("social_{$provider}_enabled", false), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable) {
            return redirect()->route('login')->withErrors([
                'email' => 'No se pudo completar la autenticación con '.ucfirst($provider).'. Intenta de nuevo.',
            ]);
        }

        $account = SocialAccount::where('provider', $provider)
            ->where('provider_user_id', $socialUser->getId())
            ->first();

        if ($account) {
            return $this->loginOrChallenge($account->user()->firstOrFail());
        }

        if (! $this->providerVerifiedEmail($socialUser)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta de '.ucfirst($provider).' no tiene el correo verificado. Verifícalo con '.ucfirst($provider).' o inicia sesión con tu contraseña para vincular tu cuenta.',
            ]);
        }

        if (! setting('registration_enabled', true) && ! User::where('email', $socialUser->getEmail())->exists()) {
            return redirect()->route('login')->withErrors([
                'email' => 'El registro público está deshabilitado. Contacta a un administrador.',
            ]);
        }

        $user = $this->findOrCreateUser($socialUser, $provider);

        return $this->loginOrChallenge($user);
    }

    /**
     * Determina si el proveedor confirma que el email de la cuenta social
     * está verificado. Google y Facebook siempre lo garantizan; GitHub puede
     * exponer un email no verificado, así que se exige el flag explícito.
     */
    private function providerVerifiedEmail(SocialiteUser $socialUser): bool
    {
        if (! $socialUser instanceof AbstractUser) {
            return false;
        }

        $raw = $socialUser->getRaw();

        return (bool) ($raw['email_verified'] ?? $raw['verified_email'] ?? false);
    }

    /**
     * Autentica al usuario respetando el 2FA: si tiene un segundo factor
     * confirmado, no se hace login directo — se replica el mismo mecanismo
     * que Fortify usa en el login por formulario (guardar el login pendiente
     * en sesión y redirigir al challenge) para que no se pueda saltar el 2FA
     * entrando por la vía social.
     */
    private function loginOrChallenge(User $user): RedirectResponse
    {
        if ($user->two_factor_secret &&
            ! is_null($user->two_factor_confirmed_at) &&
            in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user), true) &&
            ! RememberTwoFactorDevice::isRemembered(request(), $user)) {
            request()->session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => true,
            ]);

            TwoFactorAuthenticationChallenged::dispatch($user);

            return redirect()->route('two-factor.login');
        }

        Auth::login($user, remember: true);

        return redirect()->intended(config('fortify.home'));
    }

    private function findOrCreateUser(SocialiteUser $socialUser, string $provider): User
    {
        return DB::transaction(function () use ($socialUser, $provider) {
            $user = User::where('email', $socialUser->getEmail())->first();

            if (! $user) {
                $user = User::create([
                    'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'Usuario',
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(40)),
                    'email_verified_at' => now(),
                ]);

                if (Jetstream::hasTeamFeatures()) {
                    $user->ownedTeams()->save(Team::forceCreate([
                        'user_id' => $user->id,
                        'name' => 'Equipo de '.explode(' ', $user->name, 2)[0],
                        'personal_team' => true,
                    ]));

                    $user->switchTeam($user->ownedTeams()->first());
                }
            }

            SocialAccount::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_user_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]);

            return $user;
        });
    }
}
