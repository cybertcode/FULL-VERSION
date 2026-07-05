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

        // Permisivo por diseño: la plantilla carga GTM/GA/Meta Pixel, Google Fonts, y
        // scripts inline sin nonces; el Gestor de Archivos (UniSharp LFM, resources/
        // views/vendor/laravel-filemanager/index.blade.php) carga Bootstrap 4/jQuery/
        // FontAwesome/jQuery-UI desde cdn.jsdelivr.net dentro de su propio iframe; el
        // Log Viewer (rap2hpoutre/laravel-log-viewer, vendor/.../views/log.blade.php,
        // /admin/logs) carga Bootstrap/jQuery/FontAwesome/DataTables desde otros 4
        // CDNs propios; Jetstream (HasProfilePhoto::defaultProfilePhotoUrl) genera
        // avatares vía ui-avatars.com para cualquier usuario sin foto/avatar propio.
        // Bloquear cualquiera de estos orígenes rompe el panel, el landing, el gestor
        // de archivos o el visor de logs. Este CSP documenta la superficie real usada
        // hoy por el código propio y los paquetes de terceros integrados.
        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com https://www.google-analytics.com https://connect.facebook.net https://cdn.jsdelivr.net https://maxcdn.bootstrapcdn.com https://code.jquery.com https://cdn.datatables.net https://use.fontawesome.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://maxcdn.bootstrapcdn.com https://cdn.datatables.net",
            "img-src 'self' data: blob: https://www.google-analytics.com https://www.facebook.com https://ui-avatars.com",
            "font-src 'self' data: https://cdn.jsdelivr.net https://fonts.gstatic.com https://maxcdn.bootstrapcdn.com https://use.fontawesome.com",
            "connect-src 'self' https://www.google-analytics.com https://www.facebook.com https://cdn.jsdelivr.net https://maxcdn.bootstrapcdn.com",
            "frame-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
        ]));

        return $response;
    }
}
