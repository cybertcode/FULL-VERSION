<?php

namespace App\Exceptions;

use Exception;

/**
 * Excepción para acciones sin autorización de dominio.
 * Diferente a 403 HTTP — es para lógica de negocio que restringe acceso.
 * Uso: throw new UnauthorizedException('No puedes editar registros de otro usuario.');
 */
class UnauthorizedException extends Exception
{
    public function __construct(string $message = 'No autorizado.', int $code = 403)
    {
        parent::__construct($message, $code);
    }
}
