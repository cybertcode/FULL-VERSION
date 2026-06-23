<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;

class DashboardController extends BaseAdminController
{
    public function __invoke(): View
    {
        $this->authorize('dashboard.view');

        return view('admin.dashboard.index');
    }
}
