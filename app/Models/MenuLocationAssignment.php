<?php

namespace App\Models;

use App\Enums\MenuLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuLocationAssignment extends Model
{
    protected $fillable = ['location', 'menu_id'];

    protected function casts(): array
    {
        return [
            'location' => MenuLocation::class,
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
