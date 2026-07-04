<?php

namespace App\Http\Controllers\Auth;

use App\Models\SocialAccount;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;
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
            Auth::login($account->user()->firstOrFail(), remember: true);

            return redirect()->intended(config('fortify.home'));
        }

        if (! setting('registration_enabled', true) && ! User::where('email', $socialUser->getEmail())->exists()) {
            return redirect()->route('login')->withErrors([
                'email' => 'El registro público está deshabilitado. Contacta a un administrador.',
            ]);
        }

        $user = $this->findOrCreateUser($socialUser, $provider);

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
