<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Enforce2FAMiddleware
{
    // Rutas que siempre pasan aunque no tenga 2FA (para no crear bucle infinito)
    private const ALLOWED_ROUTES = [
        'profile.show',
        'profile.two-factor.*',
        'two-factor.*',
        'logout',
        'password.confirm',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Solo actúa si la feature está activa
        if (! function_exists('setting') || ! setting('force_2fa', false)) {
            return $next($request);
        }

        $user = auth()->user();

        // Solo aplica a usuarios autenticados
        if (! $user) {
            return $next($request);
        }

        // Super-Admin nunca bloqueado (para que pueda desactivar el setting)
        if ($user->hasRole('Super-Admin')) {
            return $next($request);
        }

        // Rutas permitidas sin 2FA (evitar bucle infinito)
        if ($request->routeIs(...self::ALLOWED_ROUTES)) {
            return $next($request);
        }

        // Si no tiene 2FA confirmado → redirigir al perfil
        if (! $user->two_factor_confirmed_at) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Se requiere autenticación de dos factores para continuar.',
                ], 403);
            }

            return redirect()->route('profile.show')
                ->with('flash', [
                    'type'    => 'warning',
                    'message' => 'El sistema requiere autenticación de dos factores. Actívalo en tu perfil para continuar.',
                ]);
        }

        return $next($request);
    }
}
