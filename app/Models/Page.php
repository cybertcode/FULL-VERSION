<?php

namespace App\Models;

use App\Enums\PageStatus;
use App\Enums\PageTemplate;
use App\Traits\HasAudit;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class Page extends BaseModel
{
    use HasAudit, NodeTrait, SoftDeletes;

    protected array $searchable = ['title', 'slug'];

    protected $fillable = [
        'title', 'slug', 'template', 'status', 'content',
        'seo_title', 'seo_description', 'seo_og_image',
        'published_at', 'parent_id',
    ];

    protected function casts(): array
    {
        return [
            'template' => PageTemplate::class,
            'status' => PageStatus::class,
            'content' => 'array',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Page $page) {
            if (empty($page->slug)) {
                $page->slug = static::generateUniqueSlug($page->title);
            }
        });
    }

    private static function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function publish(): bool
    {
        return $this->update(['status' => PageStatus::Published, 'published_at' => now()]);
    }

    public function unpublish(): bool
    {
        return $this->update(['status' => PageStatus::Draft, 'published_at' => null]);
    }
}
