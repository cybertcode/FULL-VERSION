<?php

namespace App\Services\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    const CACHE_KEY = 'app_settings';
    const CACHE_TTL = 86400; // 24 horas

    /**
     * Obtiene un valor de configuración.
     * Usa cache para no golpear la BD en cada request.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()->get($key, $default);
    }

    /**
     * Retorna todas las settings como colección key→value.
     */
    public function all(): \Illuminate\Support\Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::all()->pluck('value', 'key');
        });
    }

    /**
     * Retorna settings agrupadas por group.
     */
    public function grouped(): \Illuminate\Support\Collection
    {
        return Setting::all()->groupBy('group');
    }

    /**
     * Guarda múltiples settings de una vez (un formulario por grupo).
     * Limpia el cache después de guardar.
     */
    public function save(array $data): void
    {
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->clearCache();
    }

    /**
     * Limpia el cache de settings.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
