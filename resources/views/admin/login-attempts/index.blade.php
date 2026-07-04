@php use Illuminate\Support\Str; @endphp
@extends('admin/layouts/master')

@section('title', 'Intentos de inicio de sesión')

@section('admin-content')

<x-breadcrumb title="Intentos de inicio de sesión" :items="[['label' => 'Intentos de login']]" />

{{-- ─── Stat cards ─────────────────────────────────────────────────────────── --}}
<div class="row g-6 mb-6">
  @foreach([
    ['label' => 'Total registrado',    'value' => $stats['total'],          'icon' => 'tabler-login',          'color' => 'primary', 'hint' => 'Histórico completo'],
    ['label' => 'Exitosos hoy',        'value' => $stats['exitosos_hoy'],   'icon' => 'tabler-shield-check',   'color' => 'success', 'hint' => 'Logins correctos de hoy'],
    ['label' => 'Fallidos hoy',        'value' => $stats['fallidos_hoy'],   'icon' => 'tabler-shield-x',       'color' => 'danger',   'hint' => 'Intentos incorrectos de hoy'],
    ['label' => 'IPs con fallos (1m)', 'value' => $stats['ips_bloqueadas'], 'icon' => 'tabler-alert-triangle', 'color' => 'warning',  'hint' => 'Posible fuerza bruta activa'],
  ] as $card)
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">{{ $card['label'] }}</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ number_format($card['value']) }}</h4>
            </div>
            <small class="mb-0 text-muted">{{ $card['hint'] }}</small>
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
</div>

{{-- ─── Filtros + tabla ────────────────────────────────────────────────────── --}}
<div class="card">
  <div class="card-header border-bottom">
    <form method="GET" action="{{ route('admin.login-attempts.index') }}">
      <div class="row g-4 align-items-end">
        <div class="col-md-3">
          <label class="form-label" for="filtro-email">Correo</label>
          <input type="text" id="filtro-email" name="email" class="form-control" placeholder="usuario@correo.com" value="{{ request('email') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label" for="filtro-ip">IP</label>
          <input type="text" id="filtro-ip" name="ip" class="form-control" placeholder="192.168.1.1" value="{{ request('ip') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label" for="filtro-estado">Estado</label>
          <select id="filtro-estado" name="estado" class="form-select">
            <option value="">Todos</option>
            <option value="exitoso" {{ request('estado') === 'exitoso' ? 'selected' : '' }}>Exitoso</option>
            <option value="fallido" {{ request('estado') === 'fallido' ? 'selected' : '' }}>Fallido</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label" for="filtro-desde">Desde</label>
          <input type="date" id="filtro-desde" name="desde" class="form-control" value="{{ request('desde') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label" for="filtro-hasta">Hasta</label>
          <input type="date" id="filtro-hasta" name="hasta" class="form-control" value="{{ request('hasta') }}">
        </div>
        <div class="col-md-1 d-flex gap-2">
          <button type="submit" class="btn btn-primary flex-grow-1" title="Filtrar">
            <i class="icon-base ti tabler-filter icon-sm"></i>
          </button>
          @if(request()->hasAny(['email', 'ip', 'estado', 'desde', 'hasta']))
            <a href="{{ route('admin.login-attempts.index') }}" class="btn btn-label-secondary" title="Limpiar filtros">
              <i class="icon-base ti tabler-x icon-sm"></i>
            </a>
          @endif
        </div>
      </div>
    </form>
  </div>

  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th style="width:160px;">Fecha</th>
          <th>Correo</th>
          <th>Dirección IP</th>
          <th>Navegador / Dispositivo</th>
          <th style="width:120px;">Resultado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($attempts as $attempt)
        <tr>
          <td>
            <span class="fw-medium">{{ $attempt->created_at->format('d/m/Y') }}</span>
            <small class="d-block text-muted">{{ $attempt->created_at->format('H:i:s') }}</small>
          </td>
          <td>{{ $attempt->email }}</td>
          <td><code>{{ $attempt->ip_address }}</code></td>
          <td><small class="text-muted">{{ Str::limit($attempt->user_agent, 60) }}</small></td>
          <td>
            @if($attempt->successful)
              <span class="badge bg-label-success"><i class="icon-base ti tabler-check icon-xs me-1"></i>Exitoso</span>
            @else
              <span class="badge bg-label-danger"><i class="icon-base ti tabler-x icon-xs me-1"></i>Fallido</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-6">
            <i class="icon-base ti tabler-shield-off icon-lg text-muted d-block mx-auto mb-2"></i>
            <p class="text-muted mb-0">No hay intentos de inicio de sesión registrados{{ request()->hasAny(['email','ip','estado','desde','hasta']) ? ' con los filtros aplicados' : '' }}.</p>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($attempts->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
      <small class="text-muted">
        Mostrando {{ $attempts->firstItem() }}–{{ $attempts->lastItem() }} de {{ number_format($attempts->total()) }} registros
      </small>
      {{ $attempts->links() }}
    </div>
  @endif
</div>

@endsection
