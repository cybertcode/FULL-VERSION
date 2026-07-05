<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Permisivo por diseño: la plantilla carga GTM/GA/Meta Pixel y scripts inline
        // sin nonces. Bloquear script-src rompería el panel y el landing. Este CSP
        // documenta la superficie real usada hoy sin restringir nada nuevo.
        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com https://www.google-analytics.com https://connect.facebook.net",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: blob: https://www.google-analytics.com https://www.facebook.com",
            "font-src 'self' data:",
            "connect-src 'self' https://www.google-analytics.com https://www.facebook.com",
            "frame-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
        ]));

        return $response;
    }
}
