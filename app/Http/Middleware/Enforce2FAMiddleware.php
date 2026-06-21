<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Enforce2FAMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! function_exists('setting') || ! setting('force_2fa', false)) {
            return $next($request);
        }

        $user = auth()->user();

        if (! $user) {
            return $next($request);
        }

        // Super-Admin está exento de la obligación (ya tiene acceso total)
        if ($user->hasRole('Super-Admin')) {
            return $next($request);
        }

        // Si el usuario no tiene 2FA habilitado, redirigir a su perfil para que lo active
        if (! $user->two_factor_confirmed_at) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Se requiere autenticación de dos factores.'], 403);
            }

            return redirect()->route('profile.show')
                ->with('flash', [
                    'type'    => 'warning',
                    'message' => 'El administrador ha activado 2FA obligatorio. Actívalo en tu perfil para continuar.',
                ]);
        }

        return $next($request);
    }
}
