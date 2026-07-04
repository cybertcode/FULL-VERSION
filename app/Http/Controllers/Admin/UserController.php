<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\Perfil;
use App\Models\Role;
use App\Models\User;
use App\Services\Admin\ExportService;
use App\Services\Admin\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends BaseAdminController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly ExportService $exportService,
    ) {
        parent::__construct();
    }

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $stats = $this->userService->stats();
        $roles = Role::orderBy('name')->pluck('name', 'name');
        $statuses = UserStatus::cases();
        $areas = Perfil::distinct()->orderBy('area')->pluck('area')->filter()->values();
        $departamentos = Perfil::distinct()->orderBy('departamento')->pluck('departamento')->filter()->values();

        return view('admin.users.index', compact('stats', 'roles', 'statuses', 'areas', 'departamentos'));
    }

    /**
     * Endpoint AJAX para DataTable — server-side processing.
     */
    public function data(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $columns = [
            2 => 'name',
            3 => 'email',
        ];

        $soloDeleted = $request->input('solo_deleted') === '1';
        $query = $soloDeleted
            ? User::with('roles', 'perfil')->onlyTrashed()
            : User::with('roles', 'perfil')->withTrashed();

        // ── Filtros externos ─────────────────────────────────────────────────
        if ($request->filled('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $request->role));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('area')) {
            $query->whereHas('perfil', fn ($q) => $q->where('area', $request->area));
        }
        if ($request->filled('departamento')) {
            $query->whereHas('perfil', fn ($q) => $q->where('departamento', $request->departamento));
        }
        if ($request->filled('sexo')) {
            $query->whereHas('perfil', fn ($q) => $q->where('sexo', $request->sexo));
        }
        if ($request->filled('con_perfil')) {
            if ($request->con_perfil === '1') {
                $query->whereHas('perfil', fn ($q) => $q->whereNotNull('cargo'));
            } else {
                $query->whereDoesntHave('perfil')->orWhereHas('perfil', fn ($q) => $q->whereNull('cargo'));
            }
        }
        if ($request->filled('ingreso_desde')) {
            $query->whereHas('perfil', fn ($q) => $q->where('fecha_ingreso', '>=', $request->ingreso_desde));
        }
        if ($request->filled('ingreso_hasta')) {
            $query->whereHas('perfil', fn ($q) => $q->where('fecha_ingreso', '<=', $request->ingreso_hasta));
        }
        if ($request->filled('verificado')) {
            if ($request->verificado === '1') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }
        if ($request->filled('registro_desde')) {
            $query->where('created_at', '>=', $request->registro_desde.' 00:00:00');
        }
        if ($request->filled('registro_hasta')) {
            $query->where('created_at', '<=', $request->registro_hasta.' 23:59:59');
        }
        if ($request->filled('ultimo_acceso')) {
            match ($request->ultimo_acceso) {
                'hoy' => $query->whereDate('last_login_at', today()),
                'semana' => $query->where('last_login_at', '>=', now()->subDays(7)),
                'mes' => $query->where('last_login_at', '>=', now()->subDays(30)),
                'nunca' => $query->whereNull('last_login_at'),
                'inactivo' => $query->where(fn ($q) => $q->whereNull('last_login_at')->orWhere('last_login_at', '<', now()->subDays(90))),
                default => null,
            };
        }

        $totalData = (clone $query)->count();
        $totalFiltered = $totalData;

        // ── Búsqueda global ──────────────────────────────────────────────────
        if (! empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(fn ($q) => $q
                ->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('username', 'LIKE', "%{$search}%")
                ->orWhereHas('perfil', fn ($p) => $p
                    ->where('cargo', 'LIKE', "%{$search}%")
                    ->orWhere('area', 'LIKE', "%{$search}%")
                    ->orWhere('dni', 'LIKE', "%{$search}%")
                )
            );
            $totalFiltered = (clone $query)->count();
        }

        $order = $columns[$request->input('order.0.column')] ?? 'name';
        $dir = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $users = $query->orderBy($order, $dir)->offset($start)->limit($length)->get()
            ->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username,
                'email' => $u->email,
                'avatar_url' => $u->avatar_url,
                'cargo' => $u->perfil?->cargo,
                'area' => $u->perfil?->area,
                'departamento' => $u->perfil?->departamento,
                'sexo' => $u->perfil?->sexo,
                'telefono' => $u->perfil?->telefono_celular ?? $u->phone,
                'fecha_ingreso' => $u->perfil?->fecha_ingreso?->format('d/m/Y'),
                'email_verified' => (bool) $u->email_verified_at,
                'last_login_at' => $u->last_login_at?->diffForHumans(),
                'last_login_at_full' => $u->last_login_at?->format('d/m/Y H:i'),
                'last_login_ip' => $u->last_login_ip,
                'created_at' => $u->created_at?->format('d/m/Y'),
                'role' => $u->roles->first()?->name ?? '—',
                'status' => $u->status?->value,
                'status_label' => $u->status?->label(),
                'status_class' => $u->status?->badgeClass(),
                'deleted_at' => $u->deleted_at?->toDateTimeString(),
                'show_url' => $u->deleted_at ? null : route('admin.users.show', $u),
                'edit_url' => $u->deleted_at ? null : route('admin.users.edit', $u),
                'delete_url' => $u->deleted_at ? null : route('admin.users.destroy', $u),
                'restore_url' => $u->deleted_at ? route('admin.users.restore', $u->id) : null,
                'force_delete_url' => $u->deleted_at ? route('admin.users.force-delete', $u->id) : null,
                'verify_email_url' => (! $u->deleted_at && ! $u->email_verified_at) ? route('admin.users.verify-email', $u) : null,
                'resend_verify_url' => (! $u->deleted_at && ! $u->email_verified_at) ? route('admin.users.resend-verification', $u) : null,
                'reset_password_url' => $u->deleted_at ? null : route('admin.users.reset-password', $u),
                'impersonate_url' => (! $u->deleted_at && auth()->user()->can('impersonate', $u))
                    ? route('admin.users.impersonate', $u) : null,
            ]);

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $users,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $roles = Role::where('name', '!=', 'Super-Admin')->orderBy('name')->pluck('name', 'name');
        $statuses = UserStatus::cases();

        return view('admin.users.create', compact('roles', 'statuses'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $this->userService->create(
            $request->validated(),
            $request->hasFile('avatar') ? $request->file('avatar') : null
        );

        $this->flashSuccess('Usuario creado correctamente.');

        return redirect()->route('admin.users.index');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);
        $user->load('perfil', 'roles');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $user->load('perfil', 'roles');
        // Incluir todos los roles; Super-Admin solo aparece si el usuario ya lo tiene
        $roles = Role::orderBy('name')->pluck('name', 'name');
        $statuses = UserStatus::cases();

        return view('admin.users.edit', compact('user', 'roles', 'statuses'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $this->userService->update(
            $user,
            $request->validated(),
            $request->hasFile('avatar') ? $request->file('avatar') : null
        );

        $this->flashSuccess('Usuario actualizado correctamente.');

        return redirect()->route('admin.users.index');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $this->userService->delete($user);

        $this->flashSuccess('Usuario eliminado correctamente.');

        return redirect()->route('admin.users.index');
    }

    public function restore(int $id): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        $this->authorize('restore', $user);

        $this->userService->restore($user);

        $this->flashSuccess('Usuario restaurado correctamente.');

        return redirect()->route('admin.users.index');
    }

    public function forceDelete(int $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $user);

        $this->userService->forceDelete($user);

        return response()->json(['message' => "Usuario '{$user->name}' eliminado permanentemente."]);
    }

    public function verifyEmail(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => "El email de {$user->name} ya está verificado."]);
        }

        $this->userService->verifyEmail($user);

        return response()->json(['message' => "Email de {$user->name} verificado correctamente."]);
    }

    public function resendVerification(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => "El email de {$user->name} ya está verificado."], 422);
        }

        $this->userService->resendVerification($user);

        return response()->json(['message' => "Correo de verificación enviado a {$user->email}."]);
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
            'action' => ['required', 'string', 'in:activate,deactivate,ban,delete,restore,force_delete,verify_email'],
        ]);

        // Acciones destructivas requieren permiso adicional
        if ($request->action === 'force_delete') {
            $this->authorize('forceDelete', new User);
        } elseif (in_array($request->action, ['delete', 'restore'])) {
            $this->authorize($request->action === 'delete' ? 'delete' : 'restore', new User);
        } else {
            $this->authorize('update', new User);
        }

        $result = $this->userService->bulkAction($request->ids, $request->action);

        $msg = "Acción aplicada a {$result['processed']} usuario(s)";
        if ($result['skipped'] > 0) {
            $msg .= " ({$result['skipped']} omitido(s) por seguridad)";
        }

        return response()->json(['message' => $msg, 'processed' => $result['processed']]);
    }

    public function resetPassword(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $this->userService->resetPassword($user);

        return response()->json(['message' => "Contraseña de {$user->name} restablecida correctamente."]);
    }

    public function exportPdf(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        return $this->exportService->exportUsersPdf($request);
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $this->authorize('viewAny', User::class);

        return $this->exportService->exportUsersExcel($request);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', User::class);

        return $this->exportService->exportUsersCsv($request);
    }
}
