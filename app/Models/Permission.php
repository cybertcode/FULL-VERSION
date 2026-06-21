<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = ['name', 'guard_name', 'label'];

    /**
     * Retorna el label legible, o el nombre como fallback.
     */
    public function getDisplayLabel(): string
    {
        return $this->label ?? $this->name;
    }
}
