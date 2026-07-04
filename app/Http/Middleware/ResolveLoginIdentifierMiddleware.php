<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ResolveLoginIdentifierMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->routeIs('password.email')) {
            return $next($request);
        }

        $throttleKey = Str::transliterate(Str::lower((string) $request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts('reset-password:'.$throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => ['Demasiados intentos. Por favor espera antes de volver a intentarlo.'],
            ]);
        }

        RateLimiter::hit('reset-password:'.$throttleKey, 60);

        $login = (string) $request->input('email');

        if ($login !== '' && ! filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $user = User::query()
                ->where('username', $login)
                ->orWhereHas('perfil', fn ($query) => $query->where('dni', $login))
                ->first();

            if ($user) {
                $request->merge(['email' => $user->email]);
            }
        }

        return $next($request);
    }
}
