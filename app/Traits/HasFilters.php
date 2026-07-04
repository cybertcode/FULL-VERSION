<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Agrega filtrado y ordenamiento dinámico desde request a cualquier modelo.
 * Uso: Model::filter($request)->paginate(15)
 */
trait HasFilters
{
    public function scopeFilter(Builder $query, Request $request): Builder
    {
        // Búsqueda general
        if ($search = $request->get('search')) {
            $searchable = $this->searchable ?? [];
            $query->where(function (Builder $q) use ($search, $searchable) {
                foreach ($searchable as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', $this->defaultSort ?? 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        if (in_array($sortDir, ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDir);
        }

        return $query;
    }
}
