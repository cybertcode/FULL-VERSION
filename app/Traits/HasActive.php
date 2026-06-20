<?php

namespace App\Traits;

use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Builder;

/**
 * Agrega scopes de estado activo/inactivo a cualquier modelo
 * que tenga una columna `status` del tipo UserStatus enum.
 */
trait HasActive
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Active);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Inactive);
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    public function activate(): bool
    {
        return $this->update(['status' => UserStatus::Active]);
    }

    public function deactivate(): bool
    {
        return $this->update(['status' => UserStatus::Inactive]);
    }
}
