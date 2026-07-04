<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogService
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        return $this->buildQuery($request)
            ->paginate((int) setting('pagination_per_page', 20))
            ->withQueryString();
    }

    public function stats(): array
    {
        return [
            'total' => Activity::count(),
            'hoy' => Activity::whereDate('created_at', today())->count(),
            'semana' => Activity::where('created_at', '>=', now()->subDays(7))->count(),
            'actores' => Activity::whereNotNull('causer_id')->distinct('causer_id')->count('causer_id'),
        ];
    }

    /** Valores disponibles para los selects de filtro. */
    public function filterOptions(): array
    {
        return [
            'modulos' => Activity::whereNotNull('log_name')->distinct()->orderBy('log_name')->pluck('log_name'),
            'eventos' => Activity::whereNotNull('event')->distinct()->orderBy('event')->pluck('event'),
            'usuarios' => User::whereIn('id', Activity::whereNotNull('causer_id')->distinct()->pluck('causer_id'))
                ->orderBy('name')
                ->get(['id', 'name']),
        ];
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $activities = $this->buildQuery($request)->limit(10000)->get();
        $filename = 'auditoria_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($activities) {
            $handle = fopen('php://output', 'w');
            // BOM para Excel en Windows
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['Fecha', 'Usuario', 'Módulo', 'Evento', 'Descripción', 'Propiedades']);

            foreach ($activities as $a) {
                fputcsv($handle, [
                    $a->created_at->format('d/m/Y H:i:s'),
                    $a->causer?->name ?? 'Sistema',
                    $a->log_name ?? '',
                    $a->event ?? '',
                    $a->description,
                    $a->properties->isNotEmpty() ? $a->properties->toJson(JSON_UNESCAPED_UNICODE) : '',
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    protected function buildQuery(Request $request): Builder
    {
        return Activity::query()
            ->with('causer')
            ->when($request->filled('modulo'), fn ($q) => $q->where('log_name', $request->input('modulo')))
            ->when($request->filled('evento'), fn ($q) => $q->where('event', $request->input('evento')))
            ->when($request->filled('usuario'), fn ($q) => $q->where('causer_id', $request->input('usuario')))
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('desde')))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('hasta')))
            ->when($request->filled('q'), fn ($q) => $q->where('description', 'like', '%'.$request->input('q').'%'))
            ->latest();
    }
}
