<?php

namespace App\Services\Admin;

use App\Enums\UserStatus;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class DashboardService
{
    public function stats(): array
    {
        $total = User::count();

        return [
            'total' => $total,
            'active' => User::where('status', UserStatus::Active)->count(),
            'banned' => User::where('status', UserStatus::Banned)->count(),
            'roles' => Role::count(),
        ];
    }

    /** Registros de usuarios por mes — últimos 12 meses, para ApexCharts. */
    public function registrationsPerMonth(): array
    {
        $start = now()->subMonths(11)->startOfMonth();

        // Agrupación en PHP para ser portable entre MySQL y SQLite (tests)
        $raw = User::withTrashed()
            ->where('created_at', '>=', $start)
            ->pluck('created_at')
            ->countBy(fn ($date) => $date->format('Y-m'));

        $labels = [];
        $values = [];
        foreach (range(0, 11) as $i) {
            $month = $start->copy()->addMonths($i);
            $labels[] = ucfirst($month->isoFormat('MMM YY'));
            $values[] = (int) ($raw[$month->format('Y-m')] ?? 0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function recentActivity(int $limit = 8)
    {
        return Activity::with('causer')->latest()->take($limit)->get();
    }

    public function recentUsers(int $limit = 5)
    {
        return User::with('roles')->latest()->take($limit)->get();
    }
}
