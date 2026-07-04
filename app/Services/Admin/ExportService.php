<?php

namespace App\Services\Admin;

use App\Exports\PermissionsExport;
use App\Exports\RolesExport;
use App\Exports\UsersExport;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    private function branding(): array
    {
        $logoBase64 = null;
        $logoMime = 'image/png';
        $logoPath = setting('site_logo');

        if ($logoPath) {
            $fullPath = storage_path('app/public/'.$logoPath);
            if (file_exists($fullPath)) {
                $mime = mime_content_type($fullPath);
                // dompdf no soporta webp — convertir a PNG con GD
                if ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
                    $img = imagecreatefromwebp($fullPath);
                    ob_start();
                    imagepng($img);
                    $logoBase64 = base64_encode(ob_get_clean());
                    imagedestroy($img);
                    $logoMime = 'image/png';
                } elseif (in_array($mime, ['image/png', 'image/jpeg', 'image/gif'])) {
                    $logoBase64 = base64_encode(file_get_contents($fullPath));
                    $logoMime = $mime;
                }
            }
        }

        return [
            'primaryColor' => setting('primary_color', '#7367F0'),
            'siteName' => setting('site_name', 'Mi Sistema'),
            'companyName' => setting('company_name', 'Mi Empresa S.A.C.'),
            'companyAddress' => setting('company_address', ''),
            'companyPhone' => setting('company_phone', ''),
            'companyEmail' => setting('company_email', ''),
            'companyRuc' => setting('company_ruc', ''),
            'companyWebsite' => setting('company_website', ''),
            'logoBase64' => $logoBase64,
            'logoMime' => $logoMime,
            'dateFormat' => setting('date_format', 'd/m/Y'),
        ];
    }

    private function resolveUsersQuery(Request $request): Collection
    {
        $query = User::with(['roles', 'perfil'])
            ->withoutTrashed();

        if ($role = $request->input('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $role));
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($area = $request->input('area')) {
            $query->whereHas('perfil', fn ($q) => $q->where('area', $area));
        }
        if ($verificado = $request->input('verificado')) {
            $query->when($verificado === '1', fn ($q) => $q->whereNotNull('email_verified_at'));
            $query->when($verificado === '0', fn ($q) => $q->whereNull('email_verified_at'));
        }
        if ($search = $request->input('search')) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            );
        }

        return $query->orderByDesc('created_at')->get();
    }

    private function activeFiltersLabel(Request $request): string
    {
        $parts = [];
        if ($v = $request->input('role')) {
            $parts[] = "Rol: {$v}";
        }
        if ($v = $request->input('status')) {
            $parts[] = "Estado: {$v}";
        }
        if ($v = $request->input('area')) {
            $parts[] = "Área: {$v}";
        }
        if ($request->input('verificado') === '1') {
            $parts[] = 'Email verificado';
        }
        if ($request->input('verificado') === '0') {
            $parts[] = 'Sin verificar';
        }
        if ($v = $request->input('search')) {
            $parts[] = "Busqueda: \"{$v}\"";
        }

        return implode('  ·  ', $parts);
    }

    public function exportUsersPdf(Request $request): Response
    {
        $users = $this->resolveUsersQuery($request);
        $branding = $this->branding();
        $filters = $this->activeFiltersLabel($request);

        // Orientación automática: landscape si hay muchos registros o se solicita
        $forceOrient = $request->input('orientation');
        $orientation = $forceOrient ?: ($users->count() > 30 ? 'landscape' : 'portrait');

        $pdf = Pdf::loadView('admin.exports.users-pdf', array_merge($branding, [
            'users' => $users,
            'filters' => $filters,
            'orientation' => $orientation,
        ]));

        $pdf->setPaper('A4', $orientation);
        $pdf->set_option('dpi', 150);
        $pdf->set_option('enable_php', false);

        $filename = 'usuarios_'.now()->format('Ymd_His').'.pdf';

        return $pdf->download($filename);
    }

    public function exportUsersExcel(Request $request): BinaryFileResponse
    {
        $users = $this->resolveUsersQuery($request);
        $branding = $this->branding();
        $color = ltrim($branding['primaryColor'], '#');

        $export = new UsersExport(
            users: $users,
            primaryColor: $color,
            siteName: $branding['siteName'],
            companyName: $branding['companyName'],
        );

        $filename = 'usuarios_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download($export, $filename);
    }

    public function exportUsersCsv(Request $request): StreamedResponse
    {
        $users = $this->resolveUsersQuery($request);
        $filename = 'usuarios_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            // BOM para Excel en Windows
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Nombre', 'Email', 'Usuario', 'Rol', 'Cargo', 'Área',
                'DNI', 'Teléfono', 'Estado', 'Email verificado', 'Último acceso', 'Registrado',
            ]);

            foreach ($users as $u) {
                fputcsv($handle, [
                    $u->name,
                    $u->email,
                    $u->username ?? '',
                    $u->roles->first()?->name ?? '',
                    $u->perfil?->cargo ?? '',
                    $u->perfil?->area ?? '',
                    $u->perfil?->dni ?? '',
                    $u->perfil?->celular ?? $u->phone ?? '',
                    $u->status?->label() ?? '',
                    $u->email_verified_at ? 'Sí' : 'No',
                    $u->last_login_at?->format('d/m/Y H:i') ?? 'Nunca',
                    $u->created_at->format('d/m/Y'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── ROLES ────────────────────────────────────────────────────────

    private function resolveRolesQuery(Request $request): Collection
    {
        $query = Role::with(['permissions', 'users.perfil']);

        if ($name = $request->input('role')) {
            $query->where('name', $name);
        }

        return $query->orderByDesc('created_at')->get();
    }

    private function activeRoleFiltersLabel(Request $request): string
    {
        $parts = [];
        if ($v = $request->input('role')) {
            $parts[] = "Rol: {$v}";
        }
        if ($v = $request->input('status')) {
            $parts[] = "Estado: {$v}";
        }
        if ($v = $request->input('search')) {
            $parts[] = "Búsqueda: \"{$v}\"";
        }

        return implode('  ·  ', $parts);
    }

    public function exportRolesPdf(Request $request): Response
    {
        $roles = $this->resolveRolesQuery($request);
        $branding = $this->branding();
        $filters = $this->activeRoleFiltersLabel($request);

        // Con usuarios detallados si son pocos roles (portrait), si no landscape
        $orientation = $roles->count() > 5 ? 'landscape' : 'portrait';
        $showUsers = $request->boolean('with_users', true);

        $pdf = Pdf::loadView('admin.exports.roles-pdf', array_merge($branding, [
            'roles' => $roles,
            'filters' => $filters,
            'orientation' => $orientation,
            'showUsers' => $showUsers,
        ]));

        $pdf->setPaper('A4', $orientation);
        $pdf->set_option('dpi', 150);
        $pdf->set_option('enable_php', false);

        return $pdf->download('roles_'.now()->format('Ymd_His').'.pdf');
    }

    public function exportRolesExcel(Request $request): BinaryFileResponse
    {
        $roles = $this->resolveRolesQuery($request);
        $branding = $this->branding();
        $color = ltrim($branding['primaryColor'], '#');

        return Excel::download(
            new RolesExport($roles, $color, $branding['siteName'], $branding['companyName']),
            'roles_'.now()->format('Ymd_His').'.xlsx'
        );
    }

    public function exportRolesCsv(Request $request): StreamedResponse
    {
        $roles = $this->resolveRolesQuery($request);
        $filename = 'roles_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($roles) {
            $handle = fopen('php://output', 'w');
            \fprintf($handle, \chr(0xEF).\chr(0xBB).\chr(0xBF));

            fputcsv($handle, ['Rol', 'Usuarios asignados', 'Permisos', 'Permisos detalle', 'Creado']);

            foreach ($roles as $role) {
                fputcsv($handle, [
                    $role->name,
                    $role->users->count(),
                    $role->permissions->count(),
                    $role->permissions->pluck('name')->join(', ') ?: '—',
                    $role->created_at?->format('d/m/Y') ?? '—',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── PERMISOS ─────────────────────────────────────────────────────────

    private function resolvePermissionsQuery(Request $request): Collection
    {
        $query = Permission::with('roles')->orderBy('name');

        if ($module = $request->input('module')) {
            $query->where('name', 'like', $module.'.%');
        }
        if ($search = $request->input('search')) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('label', 'like', "%{$search}%")
            );
        }

        return $query->get();
    }

    private function activePermissionsFiltersLabel(Request $request): string
    {
        $parts = [];
        if ($v = $request->input('module')) {
            $parts[] = "Módulo: {$v}";
        }
        if ($v = $request->input('search')) {
            $parts[] = "Búsqueda: \"{$v}\"";
        }

        return implode('  ·  ', $parts);
    }

    public function exportPermissionsPdf(Request $request): Response
    {
        $permissions = $this->resolvePermissionsQuery($request);
        $branding = $this->branding();
        $filters = $this->activePermissionsFiltersLabel($request);

        $pdf = Pdf::loadView('admin.exports.permissions-pdf', array_merge($branding, [
            'permissions' => $permissions,
            'filters' => $filters,
            'orientation' => 'portrait',
        ]));

        $pdf->setPaper('A4', 'portrait');
        $pdf->set_option('dpi', 150);
        $pdf->set_option('enable_php', false);

        return $pdf->download('permisos_'.now()->format('Ymd_His').'.pdf');
    }

    public function exportPermissionsExcel(Request $request): BinaryFileResponse
    {
        $permissions = $this->resolvePermissionsQuery($request);
        $branding = $this->branding();
        $color = ltrim($branding['primaryColor'], '#');

        return Excel::download(
            new PermissionsExport($permissions, $color, $branding['siteName'], $branding['companyName']),
            'permisos_'.now()->format('Ymd_His').'.xlsx'
        );
    }

    public function exportPermissionsCsv(Request $request): StreamedResponse
    {
        $permissions = $this->resolvePermissionsQuery($request);
        $filename = 'permisos_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($permissions) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['Nombre técnico', 'Label', 'Módulo', 'Acción', 'Roles asignados', 'Fecha']);

            foreach ($permissions as $p) {
                $parts = explode('.', $p->name);
                fputcsv($handle, [
                    $p->name,
                    $p->label ?? $p->name,
                    $parts[0] ?? '',
                    $parts[1] ?? '',
                    $p->roles->pluck('name')->join(', ') ?: '—',
                    $p->created_at?->format('d/m/Y') ?? '—',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
