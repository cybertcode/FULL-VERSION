<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Menu extends BaseModel
{
    protected array $searchable = ['name', 'slug'];

    protected $fillable = ['name', 'slug', 'location'];

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
}
