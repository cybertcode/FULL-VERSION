@php use Illuminate\Support\Facades\Auth; @endphp

@extends('admin/layouts/master')

@section('title', 'Panel principal')

@section('admin-content')

<div class="row g-4 mb-4">

  {{-- Bienvenida --}}
  <div class="col-12">
    <div class="card">
      <div class="card-body d-flex align-items-center gap-4 py-4">
        <div class="avatar avatar-xl flex-shrink-0">
          <img src="{{ Auth::user()?->avatar_url ?? asset('assets/img/avatars/1.png') }}"
               alt="{{ Auth::user()?->name }}" class="rounded-circle" />
        </div>
        <div>
          <h5 class="mb-1">Bienvenido, {{ Auth::user()?->name ?? 'Usuario' }} 👋</h5>
          <p class="mb-0 text-body-secondary">
            {{ Auth::user()?->perfil?->cargo ?? Auth::user()?->roles->first()?->name ?? 'Panel de administración' }}
            @if(Auth::user()?->perfil?->area)
              &mdash; {{ Auth::user()?->perfil->area }}
            @endif
          </p>
        </div>
        <div class="ms-auto text-end d-none d-md-block">
          <p class="mb-0 text-body-secondary small">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
          <p class="mb-0 text-body-secondary small">Último acceso: {{ Auth::user()?->last_login_at?->diffForHumans() ?? 'Primera sesión' }}</p>
        </div>
      </div>
    </div>
  </div>

  @can('users.viewAny')
  {{-- Stats rápidas --}}
  @php
    $totalUsers   = \App\Models\User::count();
    $activeUsers  = \App\Models\User::where('status', \App\Enums\UserStatus::Active)->count();
    $bannedUsers  = \App\Models\User::where('status', \App\Enums\UserStatus::Banned)->count();
    $totalRoles   = \Spatie\Permission\Models\Role::count();
  @endphp

  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <span class="text-body-secondary d-block mb-1">Total usuarios</span>
            <h4 class="mb-1">{{ number_format($totalUsers) }}</h4>
            <small class="text-body-secondary">registrados en el sistema</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="icon-base ti tabler-users icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <span class="text-body-secondary d-block mb-1">Usuarios activos</span>
            <h4 class="mb-1">{{ number_format($activeUsers) }}</h4>
            <small class="text-success">{{ $totalUsers > 0 ? round($activeUsers / $totalUsers * 100) : 0 }}% del total</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="icon-base ti tabler-user-check icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <span class="text-body-secondary d-block mb-1">Bloqueados</span>
            <h4 class="mb-1">{{ number_format($bannedUsers) }}</h4>
            <small class="{{ $bannedUsers > 0 ? 'text-danger' : 'text-body-secondary' }}">accesos restringidos</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-danger">
              <i class="icon-base ti tabler-user-off icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <span class="text-body-secondary d-block mb-1">Roles</span>
            <h4 class="mb-1">{{ number_format($totalRoles) }}</h4>
            <small class="text-body-secondary">roles configurados</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="icon-base ti tabler-shield-lock icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endcan

  {{-- Accesos rápidos --}}
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Accesos rápidos</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          @can('users.viewAny')
          <div class="col-sm-6 col-md-4 col-xl-3">
            <a href="{{ route('admin.users.index') }}" class="d-flex align-items-center gap-3 p-3 rounded border text-decoration-none text-body hover-shadow">
              <span class="avatar avatar-sm bg-label-primary rounded">
                <i class="icon-base ti tabler-users"></i>
              </span>
              <div>
                <p class="mb-0 fw-medium">Usuarios</p>
                <small class="text-body-secondary">Gestionar usuarios</small>
              </div>
            </a>
          </div>
          @endcan

          @can('users.create')
          <div class="col-sm-6 col-md-4 col-xl-3">
            <a href="{{ route('admin.users.create') }}" class="d-flex align-items-center gap-3 p-3 rounded border text-decoration-none text-body hover-shadow">
              <span class="avatar avatar-sm bg-label-success rounded">
                <i class="icon-base ti tabler-user-plus"></i>
              </span>
              <div>
                <p class="mb-0 fw-medium">Nuevo usuario</p>
                <small class="text-body-secondary">Crear cuenta</small>
              </div>
            </a>
          </div>
          @endcan

          @can('roles.viewAny')
          <div class="col-sm-6 col-md-4 col-xl-3">
            <a href="{{ route('admin.roles.index') }}" class="d-flex align-items-center gap-3 p-3 rounded border text-decoration-none text-body hover-shadow">
              <span class="avatar avatar-sm bg-label-warning rounded">
                <i class="icon-base ti tabler-shield-lock"></i>
              </span>
              <div>
                <p class="mb-0 fw-medium">Roles y Permisos</p>
                <small class="text-body-secondary">Control de acceso</small>
              </div>
            </a>
          </div>
          @endcan

          @can('settings.view')
          <div class="col-sm-6 col-md-4 col-xl-3">
            <a href="{{ route('admin.settings.index') }}" class="d-flex align-items-center gap-3 p-3 rounded border text-decoration-none text-body hover-shadow">
              <span class="avatar avatar-sm bg-label-info rounded">
                <i class="icon-base ti tabler-settings"></i>
              </span>
              <div>
                <p class="mb-0 fw-medium">Configuración</p>
                <small class="text-body-secondary">Ajustes del sistema</small>
              </div>
            </a>
          </div>
          @endcan

          <div class="col-sm-6 col-md-4 col-xl-3">
            <a href="{{ route('admin.profile.show') }}" class="d-flex align-items-center gap-3 p-3 rounded border text-decoration-none text-body hover-shadow">
              <span class="avatar avatar-sm bg-label-secondary rounded">
                <i class="icon-base ti tabler-user-circle"></i>
              </span>
              <div>
                <p class="mb-0 fw-medium">Mi Perfil</p>
                <small class="text-body-secondary">Ver y editar cuenta</small>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

@endsection
