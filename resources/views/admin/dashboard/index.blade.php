@php use Illuminate\Support\Facades\Auth; @endphp

@extends('admin/layouts/master')

@section('title', 'Panel principal')

@section('admin-vendor-style')
  @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('admin-vendor-script')
  @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

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

  {{-- Stats rápidas --}}
  @if($stats)
  @foreach([
    ['label' => 'Total usuarios',  'value' => $stats['total'],  'icon' => 'tabler-users',       'color' => 'primary', 'hint' => 'registrados en el sistema', 'trend' => $weeklyTrend],
    ['label' => 'Usuarios activos','value' => $stats['active'], 'icon' => 'tabler-user-check',  'color' => 'success', 'hint' => ($stats['total'] > 0 ? round($stats['active'] / $stats['total'] * 100) : 0) . '% del total'],
    ['label' => 'Bloqueados',      'value' => $stats['banned'], 'icon' => 'tabler-user-off',    'color' => 'danger',  'hint' => 'accesos restringidos'],
    ['label' => 'Roles',           'value' => $stats['roles'],  'icon' => 'tabler-shield-lock', 'color' => 'warning', 'hint' => 'roles configurados'],
  ] as $card)
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div>
            <span class="text-body-secondary d-block mb-1">{{ $card['label'] }}</span>
            <h4 class="mb-1">{{ number_format($card['value']) }}</h4>
            @if(!empty($card['trend']))
              @php $t = $card['trend']; @endphp
              <small class="d-flex align-items-center gap-1">
                @if($t['trend'] === 'up')
                  <i class="icon-base ti tabler-trending-up text-success"></i>
                  <span class="text-success fw-medium">+{{ $t['change_percent'] }}%</span>
                @elseif($t['trend'] === 'down')
                  <i class="icon-base ti tabler-trending-down text-danger"></i>
                  <span class="text-danger fw-medium">{{ $t['change_percent'] }}%</span>
                @else
                  <i class="icon-base ti tabler-minus text-muted"></i>
                @endif
                <span class="text-body-secondary">vs. semana anterior ({{ $t['current'] }} nuevos)</span>
              </small>
            @else
              <small class="text-body-secondary">{{ $card['hint'] }}</small>
            @endif
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-{{ $card['color'] }}">
              <i class="icon-base ti {{ $card['icon'] }} icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endforeach
  @endif

  {{-- Gráfica de registros + actividad reciente --}}
  @if($chart || $recentActivity)
  <div class="col-xl-8 col-12">
    @if($chart)
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Usuarios registrados por mes</h5>
        <span class="badge bg-label-primary">Últimos 12 meses</span>
      </div>
      <div class="card-body">
        <div id="chart-registros"></div>
      </div>
    </div>
    @endif
  </div>
  <div class="col-xl-4 col-12">
    @if($recentActivity)
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Actividad reciente</h5>
        @can('activitylog.viewAny')
          <a href="{{ route('admin.activity.index') }}" class="btn btn-sm btn-label-primary">Ver todo</a>
        @endcan
      </div>
      <ul class="list-group list-group-flush">
        @forelse($recentActivity as $activity)
        @php
          $eventColors = ['created' => 'success', 'updated' => 'info', 'deleted' => 'danger', 'restored' => 'warning'];
          $eventColor  = $eventColors[$activity->event] ?? 'secondary';
        @endphp
        <li class="list-group-item d-flex align-items-start gap-3 py-3">
          <div class="avatar avatar-sm flex-shrink-0">
            <span class="avatar-initial rounded-circle bg-label-{{ $eventColor }}">
              <i class="icon-base ti tabler-activity icon-sm"></i>
            </span>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <p class="mb-0 small text-truncate">{{ $activity->description }}</p>
            <small class="text-body-secondary">
              {{ $activity->causer?->name ?? 'Sistema' }} · {{ $activity->created_at->diffForHumans() }}
            </small>
          </div>
        </li>
        @empty
        <li class="list-group-item text-center text-muted py-4">Sin actividad registrada.</li>
        @endforelse
      </ul>
    </div>
    @endif
  </div>
  @endif

  {{-- Últimos usuarios --}}
  @if($recentUsers)
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Últimos usuarios registrados</h5>
        @can('users.viewAny')
          <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-label-primary">Ver todos</a>
        @endcan
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr><th>Usuario</th><th>Email</th><th>Rol</th><th>Estado</th><th>Registrado</th></tr>
          </thead>
          <tbody>
            @foreach($recentUsers as $u)
            <tr>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="avatar avatar-sm">
                    <span class="avatar-initial rounded-circle bg-label-primary">{{ mb_substr($u->name, 0, 1) }}</span>
                  </div>
                  <span class="fw-medium">{{ $u->name }}</span>
                </div>
              </td>
              <td>{{ $u->email }}</td>
              <td><span class="badge bg-label-secondary">{{ $u->roles->first()?->name ?? '—' }}</span></td>
              <td>{!! statusBadge($u->status) !!}</td>
              <td><small class="text-body-secondary">{{ $u->created_at->diffForHumans() }}</small></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

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

          @can('activitylog.viewAny')
          <div class="col-sm-6 col-md-4 col-xl-3">
            <a href="{{ route('admin.activity.index') }}" class="d-flex align-items-center gap-3 p-3 rounded border text-decoration-none text-body hover-shadow">
              <span class="avatar avatar-sm bg-label-danger rounded">
                <i class="icon-base ti tabler-history"></i>
              </span>
              <div>
                <p class="mb-0 fw-medium">Auditoría</p>
                <small class="text-body-secondary">Registro de actividad</small>
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

@section('admin-page-script')
@if($chart)
<script>
'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const el = document.querySelector('#chart-registros');
  if (!el || typeof ApexCharts === 'undefined') return;

  const styles  = getComputedStyle(document.documentElement);
  const primary = styles.getPropertyValue('--bs-primary').trim() || '#1340A0';

  new ApexCharts(el, {
    chart: { type: 'area', height: 320, toolbar: { show: false }, fontFamily: 'inherit' },
    series: [{ name: 'Usuarios registrados', data: @json($chart['values']) }],
    xaxis: {
      categories: @json($chart['labels']),
      axisBorder: { show: false },
      axisTicks: { show: false }
    },
    yaxis: { labels: { formatter: v => Math.round(v) } },
    colors: [primary],
    stroke: { curve: 'smooth', width: 3 },
    fill: {
      type: 'gradient',
      gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 95] }
    },
    dataLabels: { enabled: false },
    grid: { borderColor: styles.getPropertyValue('--bs-border-color').trim() || '#e6e6e8', strokeDashArray: 4 },
    tooltip: { y: { formatter: v => v + ' usuario' + (v === 1 ? '' : 's') } }
  }).render();
});
</script>
@endif
@endsection
