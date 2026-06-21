<?php

namespace App\Models;

use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model
{
    use HasFilters;

    protected array $searchable = [];

    protected string $defaultSort = 'created_at';

    /*
    |--------------------------------------------------------------------------
    | Para activar SoftDeletes en un modelo hijo: use SoftDeletes;
    | Para activar auditoría: use HasAudit;
    | Para activar status active/inactive: use HasActive;
    |--------------------------------------------------------------------------
    */
}
