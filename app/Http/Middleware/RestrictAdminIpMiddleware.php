<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictAdminIpMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! function_exists('setting')) {
            return $next($request);
        }

        $allowedIps = setting('allowed_ips_admin', '');

        if (empty(trim($allowedIps))) {
            return $next($request);
        }

        $list = array_filter(array_map('trim', explode(',', $allowedIps)));

        if (in_array($request->ip(), $list, true)) {
            return $next($request);
        }

        // Super-Admin siempre pasa aunque su IP no esté en la lista
        if (auth('web')->check() && auth('web')->user()->hasRole('Super-Admin')) {
            return $next($request);
        }

        abort(403, 'Acceso al panel no permitido desde esta dirección IP.');
    }
}
