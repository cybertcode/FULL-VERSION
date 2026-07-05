<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Rechaza URLs de ítems de menú con un scheme peligroso (javascript:, data:,
 * vbscript:, etc.) — MenuItem::resolvedUrl() imprime el valor crudo en un
 * href de las plantillas públicas del frontend, así que aceptar cualquier
 * string permitiría un enlace ejecutable con solo hacer click. Se permiten
 * rutas relativas ("/servicios"), anchors ("#footer") y los schemes usuales
 * de un enlace de navegación (http, https, mailto, tel).
 */
class SafeMenuUrl implements ValidationRule
{
    private const ALLOWED_SCHEMES = ['http', 'https', 'mailto', 'tel'];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value)) {
            return;
        }

        if (! preg_match('/^([a-zA-Z][a-zA-Z0-9+.-]*):/', $value, $matches)) {
            return;
        }

        $scheme = strtolower($matches[1]);

        if (! in_array($scheme, self::ALLOWED_SCHEMES, true)) {
            $fail('La URL contiene un esquema no permitido.');
        }
    }
}
