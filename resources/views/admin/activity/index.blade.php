@extends('admin/layouts/master')

@section('title', 'Auditoría')

@section('admin-vendor-style')
  @vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('admin-vendor-script')
  @vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('admin-content')

<x-breadcrumb title="Auditoría del Sistema" :items="[['label' => 'Auditoría']]">
  <x-slot name="actions">
    @can('activitylog.export')
      <a href="{{ route('admin.activity.export.csv', request()->query()) }}" class="btn btn-label-secondary">
        <i class="icon-base ti tabler-file-type-csv icon-sm me-1"></i> Exportar CSV
      </a>
    @endcan
  </x-slot>
</x-breadcrumb>

{{-- ─── Stat cards ─────────────────────────────────────────────────────────── --}}
<div class="row g-6 mb-6">
  @foreach([
    ['label' => 'Total registros',  'value' => $stats['total'],   'icon' => 'tabler-history',        'color' => 'primary', 'hint' => 'Toda la actividad'],
    ['label' => 'Hoy',              'value' => $stats['hoy'],     'icon' => 'tabler-calendar-event', 'color' => 'success', 'hint' => 'Acciones de hoy'],
    ['label' => 'Últimos 7 días',   'value' => $stats['semana'],  'icon' => 'tabler-calendar-week',  'color' => 'info',    'hint' => 'Actividad semanal'],
    ['label' => 'Usuarios activos', 'value' => $stats['actores'], 'icon' => 'tabler-user-check',     'color' => 'warning', 'hint' => 'Con acciones registradas'],
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
    <form method="GET" action="{{ route('admin.activity.index') }}">
      <div class="row g-4 align-items-end">
        <div class="col-md-2">
          <label class="form-label" for="filtro-modulo">Módulo</label>
          <select id="filtro-modulo" name="modulo" class="select2 form-select">
            <option value="">Todos</option>
            @foreach($options['modulos'] as $modulo)
              <option value="{{ $modulo }}" {{ request('modulo') === $modulo ? 'selected' : '' }}>{{ ucfirst($modulo) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label" for="filtro-evento">Evento</label>
          <select id="filtro-evento" name="evento" class="select2 form-select">
            <option value="">Todos</option>
            @foreach($options['eventos'] as $evento)
              <option value="{{ $evento }}" {{ request('evento') === $evento ? 'selected' : '' }}>{{ ucfirst($evento) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label" for="filtro-usuario">Usuario</label>
          <select id="filtro-usuario" name="usuario" class="select2 form-select">
            <option value="">Todos</option>
            @foreach($options['usuarios'] as $u)
              <option value="{{ $u->id }}" {{ request('usuario') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
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
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary flex-grow-1">
            <i class="icon-base ti tabler-filter icon-sm me-1"></i> Filtrar
          </button>
          @if(request()->hasAny(['modulo', 'evento', 'usuario', 'desde', 'hasta', 'q']))
            <a href="{{ route('admin.activity.index') }}" class="btn btn-label-secondary" title="Limpiar filtros">
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
          <th>Usuario</th>
          <th>Módulo</th>
          <th>Evento</th>
          <th>Descripción</th>
          <th style="width:80px;" class="text-center">Detalle</th>
        </tr>
      </thead>
      <tbody>
        @forelse($activities as $activity)
        @php
          $eventColors = ['created' => 'success', 'updated' => 'info', 'deleted' => 'danger', 'restored' => 'warning'];
          $eventColor  = $eventColors[$activity->event] ?? 'secondary';
          $detailPayload = [
              'fecha'       => $activity->created_at->format('d/m/Y H:i:s'),
              'usuario'     => $activity->causer?->name ?? 'Sistema',
              'descripcion' => $activity->description,
              'propiedades' => $activity->properties,
          ];
        @endphp
        <tr>
          <td>
            <span class="fw-medium">{{ $activity->created_at->format('d/m/Y') }}</span>
            <small class="d-block text-muted">{{ $activity->created_at->format('H:i:s') }}</small>
          </td>
          <td>
            @if($activity->causer)
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm">
                  <span class="avatar-initial rounded-circle bg-label-primary">{{ mb_substr($activity->causer->name, 0, 1) }}</span>
                </div>
                <span>{{ $activity->causer->name }}</span>
              </div>
            @else
              <span class="text-muted fst-italic">Sistema</span>
            @endif
          </td>
          <td>
            @if($activity->log_name)
              <span class="badge bg-label-primary">{{ ucfirst($activity->log_name) }}</span>
            @else
              —
            @endif
          </td>
          <td>
            @if($activity->event)
              <span class="badge bg-label-{{ $eventColor }}">{{ ucfirst($activity->event) }}</span>
            @else
              —
            @endif
          </td>
          <td>{{ $activity->description }}</td>
          <td class="text-center">
            @if($activity->properties->isNotEmpty())
              <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill"
                data-bs-toggle="modal" data-bs-target="#activityDetailModal"
                data-activity="{{ json_encode($detailPayload, JSON_UNESCAPED_UNICODE) }}"
                title="Ver detalle">
                <i class="icon-base ti tabler-eye icon-sm"></i>
              </button>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center py-6">
            <i class="icon-base ti tabler-clipboard-off icon-lg text-muted d-block mx-auto mb-2"></i>
            <p class="text-muted mb-0">No hay registros de actividad{{ request()->hasAny(['modulo','evento','usuario','desde','hasta']) ? ' con los filtros aplicados' : '' }}.</p>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($activities->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
      <small class="text-muted">
        Mostrando {{ $activities->firstItem() }}–{{ $activities->lastItem() }} de {{ number_format($activities->total()) }} registros
      </small>
      {{ $activities->links() }}
    </div>
  @endif
</div>

{{-- ─── Modal detalle ──────────────────────────────────────────────────────── --}}
<div class="modal fade" id="activityDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="icon-base ti tabler-list-details icon-sm me-2"></i>Detalle de la actividad</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <dl class="row mb-4">
          <dt class="col-sm-3">Fecha</dt>       <dd class="col-sm-9" id="detail-fecha">—</dd>
          <dt class="col-sm-3">Usuario</dt>     <dd class="col-sm-9" id="detail-usuario">—</dd>
          <dt class="col-sm-3">Descripción</dt> <dd class="col-sm-9" id="detail-descripcion">—</dd>
        </dl>
        <div id="detail-propiedades"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('admin-page-script')
<script>
'use strict';

document.addEventListener('DOMContentLoaded', function () {
  // Select2 en filtros
  if (window.jQuery && jQuery.fn.select2) {
    jQuery('.select2').select2({ width: '100%', minimumResultsForSearch: 8 });
  }

  const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

  // Modal de detalle — se llena desde data-activity del botón
  const modal = document.getElementById('activityDetailModal');
  modal.addEventListener('show.bs.modal', function (event) {
    const data = JSON.parse(event.relatedTarget.dataset.activity);

    document.getElementById('detail-fecha').textContent       = data.fecha;
    document.getElementById('detail-usuario').textContent     = data.usuario;
    document.getElementById('detail-descripcion').textContent = data.descripcion;

    const props     = data.propiedades || {};
    const container = document.getElementById('detail-propiedades');

    // Formato Spatie: {attributes: {...}, old: {...}} → tabla antes/después
    if (props.attributes || props.old) {
      const keys = [...new Set([...Object.keys(props.attributes || {}), ...Object.keys(props.old || {})])];
      let rows = keys.map(k => `
        <tr>
          <td class="fw-medium">${esc(k)}</td>
          <td><span class="badge bg-label-danger">${esc(props.old?.[k] ?? '—')}</span></td>
          <td><span class="badge bg-label-success">${esc(props.attributes?.[k] ?? '—')}</span></td>
        </tr>`).join('');
      container.innerHTML = `
        <h6 class="mb-3">Cambios</h6>
        <div class="table-responsive border rounded">
          <table class="table table-sm mb-0">
            <thead><tr><th>Campo</th><th>Antes</th><th>Después</th></tr></thead>
            <tbody>${rows}</tbody>
          </table>
        </div>`;
    } else if (props.changes && typeof props.changes === 'object' && Object.keys(props.changes).length) {
      // Formato propio (Settings): {group, changes: {campo: {before, after}}} → tabla antes/después
      let rows = Object.entries(props.changes).map(([k, v]) => `
        <tr>
          <td class="fw-medium">${esc(k)}</td>
          <td><span class="badge bg-label-danger">${esc(v?.before ?? '—')}</span></td>
          <td><span class="badge bg-label-success">${esc(v?.after ?? '—')}</span></td>
        </tr>`).join('');
      const groupLabel = props.group ? `<span class="text-muted small d-block mb-2">Grupo: ${esc(props.group)}</span>` : '';
      container.innerHTML = `
        <h6 class="mb-3">Cambios</h6>
        ${groupLabel}
        <div class="table-responsive border rounded">
          <table class="table table-sm mb-0">
            <thead><tr><th>Campo</th><th>Antes</th><th>Después</th></tr></thead>
            <tbody>${rows}</tbody>
          </table>
        </div>`;
    } else if (Object.keys(props).length) {
      // Propiedades sueltas → lista clave/valor
      let rows = Object.entries(props).map(([k, v]) => `
        <tr><td class="fw-medium" style="width:35%;">${esc(k)}</td><td>${esc(typeof v === 'object' ? JSON.stringify(v) : v)}</td></tr>`).join('');
      container.innerHTML = `
        <h6 class="mb-3">Propiedades</h6>
        <div class="table-responsive border rounded">
          <table class="table table-sm mb-0"><tbody>${rows}</tbody></table>
        </div>`;
    } else {
      container.innerHTML = '';
    }
  });
});
</script>
@endsection
