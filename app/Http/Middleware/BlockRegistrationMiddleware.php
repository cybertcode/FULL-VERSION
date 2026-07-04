<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockRegistrationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->routeIs('register')) {
            return $next($request);
        }

        $enabled = function_exists('setting') ? setting('registration_enabled', true) : true;

        if (! $enabled) {
            abort(404);
        }

        return $next($request);
    }
}
