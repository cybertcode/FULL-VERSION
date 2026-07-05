<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Menu extends BaseModel
{
    protected array $searchable = ['name', 'slug'];

    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::creating(function (Menu $menu) {
            if (empty($menu->slug)) {
                $menu->slug = static::generateUniqueSlug($menu->name);
            }
        });
    }

    private static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->whereNull('parent_id')->defaultOrder();
    }

    public function allItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->defaultOrder();
    }

    public function tree(): Collection
    {
        return $this->allItems()->get()->toTree();
    }

    /**
     * Crea un ítem de menú directamente por código (seeders, tinker, comandos
     * artisan) sin pasar por el panel administrativo ni por el formato de
     * arrays planos que usa MenuService::saveStructure() (ese es para el
     * guardado batch del editor visual).
     *
     * Uso:
     *   $inicio = $menu->addItem(['label' => 'Inicio', 'type' => 'url', 'url' => '/']);
     *   $menu->addItem(['label' => 'Sub-ítem', 'type' => 'url', 'url' => '/x'], parent: $inicio);
     */
    public function addItem(array $attributes, ?MenuItem $parent = null): MenuItem
    {
        $item = new MenuItem(array_merge($attributes, ['menu_id' => $this->id]));

        if ($parent) {
            $item->appendToNode($parent)->save();
        } else {
            $item->saveAsRoot();
        }

        return $item;
    }
}
