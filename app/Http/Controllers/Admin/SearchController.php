<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends BaseAdminController
{
    private const MODULES = [
        ['name' => 'Panel principal',           'icon' => 'tabler-smart-home',       'url' => '/',                      'section' => 'Módulos'],
        ['name' => 'Usuarios',                  'icon' => 'tabler-users',             'url' => '/admin/usuarios',        'section' => 'Módulos'],
        ['name' => 'Nuevo usuario',             'icon' => 'tabler-user-plus',         'url' => '/admin/usuarios/crear',  'section' => 'Módulos'],
        ['name' => 'Roles y Permisos',          'icon' => 'tabler-shield-lock',       'url' => '/admin/roles',           'section' => 'Módulos'],
        ['name' => 'Mi Perfil',                 'icon' => 'tabler-user-circle',       'url' => '/admin/mi-perfil',       'section' => 'Módulos'],
        ['name' => 'Configuración',             'icon' => 'tabler-settings',          'url' => '/admin/configuracion',   'section' => 'Módulos'],
        ['name' => 'Configuración — General',   'icon' => 'tabler-settings',          'url' => '/admin/configuracion',   'section' => 'Módulos'],
        ['name' => 'Configuración — Correo',    'icon' => 'tabler-mail-cog',          'url' => '/admin/configuracion',   'section' => 'Módulos'],
        ['name' => 'Configuración — Seguridad', 'icon' => 'tabler-lock-cog',          'url' => '/admin/configuracion',   'section' => 'Módulos'],
        ['name' => 'Configuración — SEO',       'icon' => 'tabler-seo',               'url' => '/admin/configuracion',   'section' => 'Módulos'],
        ['name' => 'Configuración — Apariencia','icon' => 'tabler-palette',           'url' => '/admin/configuracion',   'section' => 'Módulos'],
        ['name' => 'Configuración — Integraciones','icon' => 'tabler-plug-connected', 'url' => '/admin/configuracion',   'section' => 'Módulos'],
    ];

    public function __invoke(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // ── Módulos (estáticos, siempre primero) ──────────────────────
        if (auth()->user()->can('dashboard.view')) {
            $term = mb_strtolower($q);
            foreach (self::MODULES as $mod) {
                if (str_contains(mb_strtolower($mod['name']), $term)) {
                    $results[] = $mod;
                }
            }
        }

        // ── Usuarios ──────────────────────────────────────────────────
        if (auth()->user()->can('users.viewAny')) {
            User::with('perfil')
                ->where(fn ($query) => $query
                    ->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%")
                    ->orWhereHas('perfil', fn ($p) => $p
                        ->where('cargo', 'like', "%{$q}%")
                        ->orWhere('dni', 'like', "%{$q}%")
                        ->orWhere('codigo_empleado', 'like', "%{$q}%")
                    )
                )
                ->limit(6)
                ->get()
                ->each(function (User $u) use (&$results) {
                    $results[] = [
                        'name'       => $u->name,
                        'subtitle'   => $u->email,
                        'icon'       => 'tabler-user',
                        'avatar_url' => $u->avatar_url,
                        'url'        => route('admin.users.show', $u),
                        'section'    => 'Usuarios',
                    ];
                });
        }

        // ── Roles ─────────────────────────────────────────────────────
        if (auth()->user()->can('roles.viewAny')) {
            Role::where('name', 'like', "%{$q}%")
                ->limit(4)
                ->get()
                ->each(function (Role $r) use (&$results) {
                    $results[] = [
                        'name'    => $r->name,
                        'subtitle'=> 'Rol del sistema',
                        'icon'    => 'tabler-shield',
                        'url'     => route('admin.roles.index'),
                        'section' => 'Roles',
                    ];
                });
        }

        return response()->json(['results' => $results]);
    }
}
