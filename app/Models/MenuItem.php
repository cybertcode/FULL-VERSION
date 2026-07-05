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
            default => $this->url ?? '#',
        };
    }
}
