<?php

namespace App\Exceptions;

use Exception;

/**
 * Excepción para errores de reglas de negocio.
 * Se muestra al usuario como mensaje de error, no como error 500.
 * Uso: throw new BusinessException('No puedes eliminar este registro.');
 */
class BusinessException extends Exception
{
    public function __construct(string $message, int $code = 422)
    {
        parent::__construct($message, $code);
    }
}
