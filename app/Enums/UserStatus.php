<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active   = 'active';
    case Inactive = 'inactive';
    case Banned   = 'banned';

    public function label(): string
    {
        return match($this) {
            self::Active   => 'Activo',
            self::Inactive => 'Inactivo',
            self::Banned   => 'Bloqueado',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Active   => 'bg-success',
            self::Inactive => 'bg-secondary',
            self::Banned   => 'bg-danger',
        };
    }
}
