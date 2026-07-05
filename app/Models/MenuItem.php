<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Route;
use Kalnoy\Nestedset\NodeTrait;

class MenuItem extends BaseModel
{
    use NodeTrait;

    protected array $searchable = ['label'];

    protected $fillable = [
        'menu_id', 'label', 'type', 'url', 'page_id',
        'route_name', 'icon', 'target', 'is_active', 'parent_id',
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

    public function resolvedUrl(): string
    {
        return match ($this->type) {
            'route' => $this->route_name && Route::has($this->route_name)
                ? route($this->route_name)
                : '#',
            'page' => $this->page_id ? url('/'.$this->page_id) : '#', // ajustar cuando exista el módulo Pages
            default => $this->url ?? '#',
        };
    }
}
