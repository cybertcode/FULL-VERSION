<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackLastLoginMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Solo actualiza una vez por sesión para no golpear la BD en cada request
            if (! $request->session()->has('last_login_tracked')) {
                $user->timestamps = false;
                $user->forceFill([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                ])->save();
                $user->timestamps = true;
                $request->session()->put('last_login_tracked', true);
            }
        }

        return $next($request);
    }
}
