<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\DashboardService;
use Illuminate\View\View;

class DashboardController extends BaseAdminController
{
    public function __invoke(DashboardService $service): View
    {
        $this->authorize('dashboard.view');

        $user = auth()->user();

        return view('admin.dashboard.index', [
            'stats' => $user->can('users.viewAny') ? $service->stats() : null,
            'weeklyTrend' => $user->can('users.viewAny') ? $service->newUsersWeeklyTrend() : null,
            'chart' => $user->can('dashboard.viewStats') ? $service->registrationsPerMonth() : null,
            'recentActivity' => $user->can('activitylog.viewAny') ? $service->recentActivity() : null,
            'recentUsers' => $user->can('users.viewAny') ? $service->recentUsers() : null,
        ]);
    }
}
