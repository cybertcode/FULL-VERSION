<?php

namespace App\Enums;

enum PageStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Borrador',
            self::Published => 'Publicado',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-label-secondary',
            self::Published => 'bg-label-success',
        };
    }
}
