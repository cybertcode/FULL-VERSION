<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Services\Admin\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PermissionController extends BaseAdminController
{
    public function __construct(
        private readonly ExportService $exportService,
    ) {
        parent::__construct();
    }

    public function index(): View
    {
        $this->authorize('viewAny', Permission::class);

        return view('admin.permissions.index');
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Permission::class);

        $query = Permission::with('roles')->orderBy('name');

        if ($module = $request->input('module')) {
            $query->where('name', 'like', $module.'.%');
        }

        $permissions = $query->get()->map(fn (Permission $p) => [
            'id' => $p->id,
            'name' => $p->name,
            'label' => $p->label ?? $p->name,
            'module' => explode('.', $p->name)[0],
            'action' => explode('.', $p->name)[1] ?? '',
            'roles' => $p->roles->pluck('name'),
            'created_at' => $p->created_at?->format('d/m/Y'),
        ]);

        return response()->json(['data' => $permissions]);
    }

    public function exportPdf(Request $request): Response
    {
        $this->authorize('viewAny', Permission::class);

        return $this->exportService->exportPermissionsPdf($request);
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $this->authorize('viewAny', Permission::class);

        return $this->exportService->exportPermissionsExcel($request);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Permission::class);

        return $this->exportService->exportPermissionsCsv($request);
    }
}
