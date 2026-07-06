<?php

namespace App\Models;

use App\Enums\PageStatus;
use App\Traits\HasAudit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class Page extends BaseModel
{
    use HasAudit, NodeTrait, SoftDeletes;

    protected array $searchable = ['title', 'slug'];

    protected $fillable = [
        'title', 'slug', 'status',
        'seo_title', 'seo_description', 'seo_og_image',
        'published_at', 'parent_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => PageStatus::class,
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

        static::created(function (Page $page) {
            $page->createViewFile();
        });

        static::updating(function (Page $page) {
            if ($page->isDirty('slug')) {
                $page->renameViewFile($page->getOriginal('slug'), $page->slug);
            }
        });

        static::forceDeleted(function (Page $page) {
            $page->deleteViewFile();
        });
    }

    /**
     * Vista Blade dedicada a esta página — una por página, editada a mano
     * en resources/views/frontend/paginas/{slug}.blade.php.
     */
    public function view(): string
    {
        return 'frontend.paginas.'.$this->slug;
    }

    public function viewPath(): string
    {
        return resource_path('views/frontend/paginas/'.$this->slug.'.blade.php');
    }

    public function createViewFile(): void
    {
        $path = $this->viewPath();

        if (File::exists($path)) {
            return;
        }

        File::ensureDirectoryExists(dirname($path));

        $stub = File::get(resource_path('stubs/frontend-page.blade.stub'));
        $stub = str_replace('{{ title }}', $this->title, $stub);

        File::put($path, $stub);
    }

    public function renameViewFile(string $oldSlug, string $newSlug): void
    {
        $oldPath = resource_path("views/frontend/paginas/{$oldSlug}.blade.php");
        $newPath = resource_path("views/frontend/paginas/{$newSlug}.blade.php");

        if (File::exists($oldPath) && ! File::exists($newPath)) {
            File::move($oldPath, $newPath);
        }
    }

    public function deleteViewFile(): void
    {
        $path = $this->viewPath();

        if (File::exists($path)) {
            File::delete($path);
        }
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

    /**
     * @return HasMany<MenuItem, $this>
     */
    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    /**
     * Cadena de ancestros (de raíz a padre inmediato) para el breadcrumb
     * público — cada elemento trae la etiqueta y la URL real de la página.
     *
     * @return array<int, array{label: string, url: string}>
     */
    public function breadcrumbTrail(): array
    {
        return $this->ancestors()
            ->getQuery()
            ->orderBy($this->getLftName())
            ->get(['title', 'slug'])
            ->map(fn (Model $ancestor) => [
                'label' => (string) $ancestor->getAttribute('title'),
                'url' => url((string) $ancestor->getAttribute('slug')),
            ])
            ->all();
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
