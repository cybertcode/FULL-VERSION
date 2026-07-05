<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kalnoy\Nestedset\NodeTrait;

class MenuItem extends BaseModel
{
    use NodeTrait;

    protected array $searchable = ['label'];

    protected $fillable = [
        'menu_id', 'label', 'type', 'url', 'page_id',
        'icon', 'target', 'is_active', 'parent_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function resolvedUrl(): string
    {
        return match ($this->type) {
            'page' => $this->page ? url($this->page->slug) : '#',
            default => $this->safeUrl(),
        };
    }

    /**
     * Defensa en profundidad: aunque SafeMenuUrl ya valida esto al guardar
     * desde el panel, cualquier ítem insertado por otra vía (seeder, tinker)
     * no debe poder renderizar un scheme ejecutable como javascript:/data:.
     */
    private function safeUrl(): string
    {
        if (blank($this->url)) {
            return '#';
        }

        if (preg_match('/^([a-zA-Z][a-zA-Z0-9+.-]*):/', $this->url, $matches) &&
            ! in_array(strtolower($matches[1]), ['http', 'https', 'mailto', 'tel'], true)) {
            return '#';
        }

        return $this->url;
    }
}
