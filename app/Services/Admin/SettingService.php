<?php

namespace App\Services\Admin;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    const CACHE_KEY = 'app_settings';

    const CACHE_TTL = 86400; // 24 horas

    // Claves con archivos (rutas de Storage) — excluidas de export/import, se gestionan por upload
    const FILE_KEYS = ['site_logo', 'site_logo_dark', 'site_favicon', 'seo_og_image'];

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
    public function all(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::all()->pluck('value', 'key');
        });
    }

    /**
     * Retorna settings agrupadas por group.
     */
    public function grouped(): Collection
    {
        return Setting::all()->groupBy('group');
    }

    /**
     * Guarda múltiples settings de un grupo y retorna solo los pares que
     * realmente cambiaron de valor (clave => ['before' => ..., 'after' => ...]),
     * para poder registrar un diff legible en el log de auditoría.
     */
    public function save(array $data, ?string $group = null): array
    {
        $before = Setting::whereIn('key', array_keys($data))->pluck('value', 'key');
        $changes = [];

        foreach ($data as $key => $value) {
            $oldValue = $before->get($key);
            if ((string) $oldValue !== (string) $value) {
                $changes[$key] = ['before' => $oldValue, 'after' => $value];
            }

            Setting::updateOrCreate(
                ['key' => $key],
                array_filter(['value' => $value, 'group' => $group], fn ($v) => $v !== null)
            );
        }

        $this->clearCache();

        return $changes;
    }

    /**
     * Limpia el cache de settings.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Exporta todos los settings (excepto archivos y claves encriptadas) como array plano.
     */
    public function export(): array
    {
        return Setting::query()
            ->whereNotIn('key', self::FILE_KEYS)
            ->whereNotIn('key', Setting::ENCRYPTED_KEYS)
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->mapWithKeys(fn (Setting $s) => [$s->key => $s->value])
            ->all();
    }

    /**
     * Importa un array key => value, ignorando claves de archivos y encriptadas
     * (deben configurarse manualmente por seguridad). Retorna cuántas claves se aplicaron.
     */
    public function import(array $values): int
    {
        $applied = 0;

        foreach ($values as $key => $value) {
            if (\in_array($key, self::FILE_KEYS, true) || \in_array($key, Setting::ENCRYPTED_KEYS, true)) {
                continue;
            }

            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            $applied++;
        }

        $this->clearCache();

        return $applied;
    }
}
