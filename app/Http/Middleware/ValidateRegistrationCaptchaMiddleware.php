<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ValidateRegistrationCaptchaMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->routeIs('register') || ! $request->isMethod('post')) {
            return $next($request);
        }

        if (! function_exists('setting') || ! setting('captcha_enabled', false)) {
            return $next($request);
        }

        $token = $request->input('g-recaptcha-response');
        $secretKey = config('services.recaptcha.secret_key', setting('recaptcha_secret_key'));

        if (empty($token) || empty($secretKey)) {
            throw ValidationException::withMessages([
                'email' => ['Por favor completa el captcha para continuar.'],
            ]);
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $token,
            'remoteip' => $request->ip(),
        ]);

        if (! ($response->json('success') ?? false)) {
            throw ValidationException::withMessages([
                'email' => ['La verificación captcha falló. Intenta de nuevo.'],
            ]);
        }

        return $next($request);
    }
}
