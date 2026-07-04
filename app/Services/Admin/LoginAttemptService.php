<?php

namespace App\Services\Admin;

use App\Models\LoginAttempt;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LoginAttemptService
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
            'total' => LoginAttempt::count(),
            'fallidos_hoy' => LoginAttempt::where('successful', false)->whereDate('created_at', today())->count(),
            'exitosos_hoy' => LoginAttempt::where('successful', true)->whereDate('created_at', today())->count(),
            'ips_bloqueadas' => LoginAttempt::where('successful', false)
                ->where('created_at', '>=', now()->subMinutes(1))
                ->distinct('ip_address')
                ->count('ip_address'),
        ];
    }

    protected function buildQuery(Request $request): Builder
    {
        return LoginAttempt::query()
            ->when($request->filled('email'), fn ($q) => $q->where('email', 'like', '%'.$request->input('email').'%'))
            ->when($request->filled('ip'), fn ($q) => $q->where('ip_address', 'like', '%'.$request->input('ip').'%'))
            ->when($request->filled('estado'), fn ($q) => $q->where('successful', $request->input('estado') === 'exitoso'))
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('desde')))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('hasta')))
            ->latest('created_at');
    }
}
