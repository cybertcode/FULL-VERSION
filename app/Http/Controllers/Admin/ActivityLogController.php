<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogController extends BaseAdminController
{
    public function __construct(protected ActivityLogService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('activitylog.viewAny');

        return view('admin.activity.index', [
            'activities' => $this->service->paginate($request),
            'stats' => $this->service->stats(),
            'options' => $this->service->filterOptions(),
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $this->authorize('activitylog.export');

        return $this->service->exportCsv($request);
    }
}
