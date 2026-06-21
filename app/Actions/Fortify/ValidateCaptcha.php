<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ValidateCaptcha
{
    public function handle(Request $request, \Closure $next): mixed
    {
        if (! function_exists('setting') || ! setting('captcha_enabled', false)) {
            return $next($request);
        }

        $token     = $request->input('g-recaptcha-response');
        $secretKey = config('services.recaptcha.secret_key', setting('captcha_secret_key'));

        if (empty($token) || empty($secretKey)) {
            throw ValidationException::withMessages([
                'email' => ['Por favor completa el captcha para continuar.'],
            ]);
        }

        $response = \Illuminate\Support\Facades\Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $secretKey,
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
