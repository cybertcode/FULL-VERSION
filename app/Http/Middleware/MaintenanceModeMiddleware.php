<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! function_exists('setting')) {
            return $next($request);
        }

        if (! setting('maintenance_mode', false)) {
            return $next($request);
        }

        // Rutas siempre permitidas: login, logout, assets
        if ($request->routeIs('login', 'logout', 'password.*', 'admin.settings.*')) {
            return $next($request);
        }

        // Admins autenticados pasan siempre
        if (auth()->check() && auth()->user()->hasRole('Super-Admin')) {
            return $next($request);
        }

        // IPs en whitelist pasan
        $allowedIps = array_filter(
            array_map('trim', explode(',', setting('maintenance_ips', '')))
        );

        if (in_array($request->ip(), $allowedIps)) {
            return $next($request);
        }

        $message = setting('maintenance_message', 'El sistema se encuentra en mantenimiento. Vuelve pronto.');

        return response()->view('errors.maintenance', compact('message'), 503);
    }
}
