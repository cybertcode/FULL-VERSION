<?php

namespace App\Enums;

enum RoleType: string
{
    case SuperAdmin = 'Super-Admin';
    case Admin      = 'admin';
    case User       = 'user';

    public function label(): string
    {
        return match($this) {
            self::SuperAdmin => 'Super Administrador',
            self::Admin      => 'Administrador',
            self::User       => 'Usuario',
        };
    }
}
