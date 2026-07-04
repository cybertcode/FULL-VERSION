@extends('admin/layouts/master')

@section('title', 'Usuarios')

@section('admin-vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  ])
@endsection

@section('admin-vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  ])
@endsection

@section('admin-content')

<x-breadcrumb :items="[['label' => 'Usuarios']]" />

{{-- ─── Stat Cards ───────────────────────────────────────────────────────────── --}}
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-2">
    <div class="card cursor-pointer stat-card" data-filter-status="" style="transition:box-shadow .15s">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['total'] }}</h4>
            </div>
            <small class="mb-0 text-muted">Todos los usuarios</small>
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
  <div class="col-sm-6 col-xl-2">
    <div class="card cursor-pointer stat-card" data-filter-status="active" style="transition:box-shadow .15s">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Activos</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['active'] }}</h4>
              <small class="text-success">({{ $stats['total'] > 0 ? round($stats['active'] / $stats['total'] * 100) : 0 }}%)</small>
            </div>
            <small class="mb-0 text-muted">Cuentas activas</small>
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
  <div class="col-sm-6 col-xl-2">
    <div class="card cursor-pointer stat-card" data-filter-status="inactive" style="transition:box-shadow .15s">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Inactivos</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['inactive'] }}</h4>
            </div>
            <small class="mb-0 text-muted">Cuentas inactivas</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="icon-base ti tabler-user-pause icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-2">
    <div class="card cursor-pointer stat-card" data-filter-status="banned" style="transition:box-shadow .15s">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Bloqueados</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['banned'] }}</h4>
            </div>
            <small class="mb-0 text-muted">Acceso bloqueado</small>
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
  <div class="col-sm-6 col-xl-2">
    <div class="card cursor-pointer stat-card" data-filter-verificado="1" style="transition:box-shadow .15s">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Verificados</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['verified'] }}</h4>
              <small class="text-info">({{ $stats['total'] > 0 ? round($stats['verified'] / $stats['total'] * 100) : 0 }}%)</small>
            </div>
            <small class="mb-0 text-muted">Email confirmado</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-info">
              <i class="icon-base ti tabler-mail-check icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-2">
    <div class="card cursor-pointer stat-card" data-filter-acceso="inactivo" style="transition:box-shadow .15s">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Sin acceso</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['sin_acceso'] }}</h4>
            </div>
            <small class="mb-0 text-muted">+30 días sin ingresar</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-secondary">
              <i class="icon-base ti tabler-clock-off icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ─── Barra de acciones masivas (oculta hasta seleccionar filas) ─────────────── --}}
<div id="bulkBar" class="card mb-4 border-primary d-none">
  <div class="card-body py-3 d-flex align-items-center gap-3 flex-wrap">
    <span class="fw-medium text-primary">
      <i class="icon-base ti tabler-checks icon-18px me-1"></i>
      <span id="bulkCount">0</span> usuario(s) seleccionado(s)
    </span>
    <div class="d-flex gap-2 flex-wrap ms-auto">
      @can('users.edit')
      <button class="btn btn-sm btn-success bulk-btn" data-action="activate"    title="Activar seleccionados"><i class="icon-base ti tabler-user-check me-1"></i>Activar</button>
      <button class="btn btn-sm btn-warning bulk-btn" data-action="deactivate"  title="Inactivar seleccionados"><i class="icon-base ti tabler-user-pause me-1"></i>Inactivar</button>
      <button class="btn btn-sm btn-secondary bulk-btn" data-action="ban"       title="Bloquear seleccionados"><i class="icon-base ti tabler-user-off me-1"></i>Bloquear</button>
      <button class="btn btn-sm btn-info bulk-btn" data-action="verify_email"   title="Verificar email seleccionados"><i class="icon-base ti tabler-mail-check me-1"></i>Verificar email</button>
      @endcan
      @can('users.restore')
      <button class="btn btn-sm btn-label-success bulk-btn" data-action="restore" title="Restaurar seleccionados"><i class="icon-base ti tabler-restore me-1"></i>Restaurar</button>
      @endcan
      @can('users.delete')
      <button class="btn btn-sm btn-danger bulk-btn" data-action="delete"       title="Eliminar seleccionados"><i class="icon-base ti tabler-trash me-1"></i>Eliminar</button>
      @endcan
      @can('users.forceDelete')
      <button class="btn btn-sm btn-label-danger bulk-btn" data-action="force_delete" title="Eliminar permanentemente"><i class="icon-base ti tabler-trash-x me-1"></i>Perm.</button>
      @endcan
    </div>
  </div>
</div>

{{-- ─── Tabla ───────────────────────────────────────────────────────────────── --}}
<div class="card">
  <div class="card-header border-bottom">
    <div class="row align-items-center g-3">

      {{-- Rol --}}
      <div class="col-12 col-sm-4 col-lg-3">
        <select id="filterRole" class="form-select filter-main">
          <option value="">Todos los roles</option>
          @foreach ($roles as $role)
            <option value="{{ $role }}">{{ $role }}</option>
          @endforeach
        </select>
      </div>

      {{-- Estado --}}
      <div class="col-12 col-sm-4 col-lg-3">
        <select id="filterStatus" class="form-select filter-main">
          <option value="">Todos los estados</option>
          @foreach ($statuses as $status)
            <option value="{{ $status->value }}">{{ $status->label() }}</option>
          @endforeach
          <option value="__deleted__">🗑 Eliminados</option>
        </select>
      </div>

      {{-- Área --}}
      <div class="col-12 col-sm-4 col-lg-3">
        <select id="filterArea" class="form-select filter-main">
          <option value="">Todas las áreas</option>
          @foreach ($areas as $area)
            <option value="{{ $area }}">{{ $area }}</option>
          @endforeach
        </select>
      </div>

      {{-- Botones derecha --}}
      <div class="col-12 col-lg-3 d-flex align-items-center justify-content-lg-end gap-2">
        {{-- Filtros avanzados --}}
        <button type="button" class="btn btn-label-secondary position-relative"
                data-bs-toggle="offcanvas" data-bs-target="#offcanvasFilters">
          <i class="icon-base ti tabler-adjustments-horizontal me-1 icon-16px"></i>
          Avanzado
          <span id="badgeFiltersAvanzados"
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary d-none">
            0
          </span>
        </button>
        {{-- Limpiar --}}
        <button type="button" id="btnClearAll" class="btn btn-label-danger d-none">
          <i class="icon-base ti tabler-filter-off icon-16px"></i>
        </button>
      </div>

    </div>
  </div>

  <div class="card-datatable">
    <table class="datatables-users table border-top">
      <thead>
        <tr>
          <th></th>
          <th><input type="checkbox" id="dtUsersCheckAll" class="form-check-input mt-0" title="Seleccionar todos"></th>
          <th>Usuario</th>
          <th>Cargo / Área</th>
          <th>Teléfono</th>
          <th>Ingreso</th>
          <th>Último acceso</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

{{-- ─── Leyenda ─────────────────────────────────────────────────────────────── --}}
@php
$legendItems = [
  ['icon' => 'tabler-checkbox',          'color' => 'primary',   'text' => 'Marca el <strong class="text-body">checkbox</strong> de cada fila para seleccionar usuarios y ejecutar acciones masivas.'],
  ['icon' => 'tabler-adjustments',       'color' => 'info',      'text' => 'Usa los <strong class="text-body">filtros avanzados</strong> para acotar por estado, rol, fecha de ingreso o verificación.'],
  ['icon' => 'tabler-file-export',       'color' => 'success',   'text' => 'Los botones de <strong class="text-body">Exportar</strong> respetan los filtros activos y exportan todos los registros coincidentes.'],
  ['icon' => 'tabler-shield-exclamation','color' => 'warning',   'text' => 'Los usuarios con rol <strong class="text-body">Super-Admin</strong> no pueden ser eliminados ni modificados masivamente.'],
];
@endphp
<x-table-legend :items="$legendItems" />

{{-- ─── Offcanvas filtros avanzados ────────────────────────────────────────── --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFilters">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title d-flex align-items-center gap-2">
      <i class="icon-base ti tabler-adjustments-horizontal text-primary"></i>
      Filtros avanzados
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">

    <p class="text-muted small mb-4">Aplica filtros adicionales para acotar la búsqueda.</p>

    {{-- Email verificado --}}
    <div class="mb-5">
      <label class="form-label fw-medium">
        <i class="icon-base ti tabler-mail-check icon-16px me-1 text-muted"></i>Email verificado
      </label>
      <div class="d-flex gap-3">
        <div class="form-check">
          <input class="form-check-input filter-adv" type="radio" name="filterVerificado" id="verificadoTodos" value="" checked>
          <label class="form-check-label" for="verificadoTodos">Todos</label>
        </div>
        <div class="form-check">
          <input class="form-check-input filter-adv" type="radio" name="filterVerificado" id="verificadoSi" value="1">
          <label class="form-check-label" for="verificadoSi">Verificado</label>
        </div>
        <div class="form-check">
          <input class="form-check-input filter-adv" type="radio" name="filterVerificado" id="verificadoNo" value="0">
          <label class="form-check-label" for="verificadoNo">Sin verificar</label>
        </div>
      </div>
    </div>

    {{-- Último acceso --}}
    <div class="mb-5">
      <label class="form-label fw-medium">
        <i class="icon-base ti tabler-clock icon-16px me-1 text-muted"></i>Último acceso
      </label>
      <select id="filterUltimoAcceso" class="form-select filter-adv">
        <option value="">Cualquier momento</option>
        <option value="hoy">Hoy</option>
        <option value="semana">Últimos 7 días</option>
        <option value="mes">Últimos 30 días</option>
        <option value="inactivo">Más de 90 días / nunca</option>
        <option value="nunca">Nunca ingresó</option>
      </select>
    </div>

    {{-- Sexo --}}
    <div class="mb-5">
      <label class="form-label fw-medium">
        <i class="icon-base ti tabler-gender-bigender icon-16px me-1 text-muted"></i>Sexo
      </label>
      <div class="d-flex gap-3">
        <div class="form-check">
          <input class="form-check-input filter-adv" type="radio" name="filterSexo" id="sexoTodos" value="" checked>
          <label class="form-check-label" for="sexoTodos">Todos</label>
        </div>
        <div class="form-check">
          <input class="form-check-input filter-adv" type="radio" name="filterSexo" id="sexoM" value="M">
          <label class="form-check-label" for="sexoM">Masculino</label>
        </div>
        <div class="form-check">
          <input class="form-check-input filter-adv" type="radio" name="filterSexo" id="sexoF" value="F">
          <label class="form-check-label" for="sexoF">Femenino</label>
        </div>
      </div>
    </div>

    {{-- Perfil laboral --}}
    <div class="mb-5">
      <label class="form-label fw-medium">
        <i class="icon-base ti tabler-id-badge icon-16px me-1 text-muted"></i>Perfil laboral
      </label>
      <select id="filterConPerfil" class="form-select filter-adv">
        <option value="">Todos</option>
        <option value="1">Con cargo asignado</option>
        <option value="0">Sin cargo</option>
      </select>
    </div>

    {{-- Departamento --}}
    <div class="mb-5">
      <label class="form-label fw-medium">
        <i class="icon-base ti tabler-map-pin icon-16px me-1 text-muted"></i>Departamento
      </label>
      <select id="filterDepartamento" class="form-select filter-adv">
        <option value="">Todos</option>
        @foreach ($departamentos as $dep)
          <option value="{{ $dep }}">{{ $dep }}</option>
        @endforeach
      </select>
    </div>

    {{-- Rango fecha de ingreso --}}
    <div class="mb-5">
      <label class="form-label fw-medium">
        <i class="icon-base ti tabler-calendar-check icon-16px me-1 text-muted"></i>Fecha de ingreso
      </label>
      <div class="row g-2">
        <div class="col-6">
          <label class="form-label small text-muted">Desde</label>
          <input type="date" id="filterIngresoDesde" class="form-control filter-adv">
        </div>
        <div class="col-6">
          <label class="form-label small text-muted">Hasta</label>
          <input type="date" id="filterIngresoHasta" class="form-control filter-adv">
        </div>
      </div>
    </div>

    {{-- Rango fecha de registro --}}
    <div class="mb-5">
      <label class="form-label fw-medium">
        <i class="icon-base ti tabler-calendar-plus icon-16px me-1 text-muted"></i>Fecha de registro
      </label>
      <div class="row g-2">
        <div class="col-6">
          <label class="form-label small text-muted">Desde</label>
          <input type="date" id="filterRegistroDesde" class="form-control filter-adv">
        </div>
        <div class="col-6">
          <label class="form-label small text-muted">Hasta</label>
          <input type="date" id="filterRegistroHasta" class="form-control filter-adv">
        </div>
      </div>
    </div>

    <hr>

    <button type="button" id="btnClearAdv" class="btn btn-label-secondary w-100">
      <i class="icon-base ti tabler-filter-off me-1"></i>Limpiar filtros avanzados
    </button>

  </div>
</div>

@endsection

@section('admin-page-script')
<script>
'use strict';

document.addEventListener('DOMContentLoaded', function () {

  const dtUsersTable = document.querySelector('.datatables-users');
  if (!dtUsersTable) return;

  // ── Helpers para leer filtros ─────────────────────────────────────────────
  const val = id => document.getElementById(id)?.value ?? '';
  const radioVal = name => document.querySelector(`input[name="${name}"]:checked`)?.value ?? '';

  function getMainFilters() {
    const status = val('filterStatus');
    return {
      role:         val('filterRole'),
      status:       status === '__deleted__' ? '' : status,
      solo_deleted: status === '__deleted__' ? '1' : '',
      area:         val('filterArea'),
    };
  }

  function getAdvFilters() {
    return {
      verificado:       radioVal('filterVerificado'),
      ultimo_acceso:    val('filterUltimoAcceso'),
      sexo:             radioVal('filterSexo'),
      con_perfil:       val('filterConPerfil'),
      departamento:     val('filterDepartamento'),
      ingreso_desde:    val('filterIngresoDesde'),
      ingreso_hasta:    val('filterIngresoHasta'),
      registro_desde:   val('filterRegistroDesde'),
      registro_hasta:   val('filterRegistroHasta'),
    };
  }

  function countAdvActive() {
    const adv = getAdvFilters();
    return Object.values(adv).filter(v => v !== '').length;
  }

  function hasAnyFilter() {
    const main = getMainFilters();
    return Object.values(main).some(v => v !== '') || countAdvActive() > 0;
  }

  // ── Sincronizar UI de filtros ─────────────────────────────────────────────
  const badgeAdv  = document.getElementById('badgeFiltersAvanzados');
  const btnClear  = document.getElementById('btnClearAll');

  function syncUI() {
    const advCount = countAdvActive();
    if (badgeAdv) {
      badgeAdv.textContent = advCount;
      badgeAdv.classList.toggle('d-none', advCount === 0);
    }
    btnClear?.classList.toggle('d-none', !hasAnyFilter());
  }

  // ── DataTable ─────────────────────────────────────────────────────────────
  const defaultPerPage = @json(config('app-settings.pagination.default', 15));

  const dt = new DataTable(dtUsersTable, {
    pageLength: defaultPerPage,
    processing: true,
    serverSide: true,
    ajax: {
      url: '{{ route('admin.users.data') }}',
      data: function (d) {
        Object.assign(d, getMainFilters(), getAdvFilters());
      },
      dataSrc: function (json) {
        if (typeof json.recordsTotal !== 'number') json.recordsTotal = 0;
        if (typeof json.recordsFiltered !== 'number') json.recordsFiltered = 0;
        json.data = Array.isArray(json.data) ? json.data : [];
        return json.data;
      }
    },
    columns: [
      { data: null },
      { data: null,             orderable: false, searchable: false },
      { data: 'name' },
      { data: 'cargo',          orderable: false, searchable: false },
      { data: 'telefono',       orderable: false, searchable: false },
      { data: 'fecha_ingreso',  orderable: false, searchable: false },
      { data: 'last_login_at',  orderable: false, searchable: false },
      { data: 'role',           orderable: false, searchable: false },
      { data: 'status_label',   orderable: false, searchable: false },
      { data: null,             orderable: false, searchable: false }
    ],
    columnDefs: [
      {
        className: 'control',
        searchable: false,
        orderable: false,
        responsivePriority: 1,
        targets: 0,
        render: () => ''
      },
      {
        targets: 1,
        orderable: false,
        searchable: false,
        responsivePriority: 1,
        render: () => '<input type="checkbox" class="dt-checkboxes form-check-input">'
      },
      {
        targets: 2,
        responsivePriority: 2,
        render: function (data, type, full) {
          const name  = full.name  || '';
          const email = full.email || '';
          const url   = full.avatar_url;
          const fade  = full.deleted_at ? ' opacity-50' : '';

          const avatar = url
            ? `<img src="${url}" alt="${name}" class="rounded-circle" width="34" height="34">`
            : (() => {
                const ini   = name.trim().split(/\s+/).slice(0,2).map(w => [...w][0] ?? '').join('').toUpperCase();
                const cols  = ['primary','success','danger','warning','info'];
                const color = cols[([...name][0] ?? ' ').codePointAt(0) % cols.length];
                return `<span class="avatar-initial rounded-circle bg-label-${color}">${ini}</span>`;
              })();

          return `<div class="d-flex justify-content-start align-items-center user-name${fade}">
            <div class="avatar-wrapper">
              <div class="avatar avatar-sm me-4">${avatar}</div>
            </div>
            <div class="d-flex flex-column">
              <span class="fw-medium text-heading text-truncate">${name}</span>
              <small class="text-muted">${email}</small>
            </div>
          </div>`;
        }
      },
      {
        targets: 3,
        responsivePriority: 6,
        render: function (data, type, full) {
          const cargo = full.cargo || '—';
          const area  = full.area  || '';
          return `<div class="d-flex flex-column">
            <span class="fw-medium text-truncate" style="max-width:200px" title="${cargo}">${cargo}</span>
            ${area ? `<small class="text-muted text-truncate" style="max-width:200px" title="${area}">${area}</small>` : ''}
          </div>`;
        }
      },
      {
        targets: 4,
        responsivePriority: 8,
        render: (d, t, full) => full.telefono
          ? `<span class="text-nowrap"><i class="icon-base ti tabler-phone me-1 text-muted icon-14px"></i>${full.telefono}</span>`
          : '<span class="text-muted">—</span>'
      },
      {
        targets: 5,
        responsivePriority: 9,
        render: (d, t, full) => full.fecha_ingreso
          ? `<span class="text-nowrap">${full.fecha_ingreso}</span>`
          : '<span class="text-muted">—</span>'
      },
      {
        targets: 6,
        responsivePriority: 10,
        render: function (d, t, full) {
          if (!full.last_login_at) return '<span class="text-muted fst-italic small">Nunca</span>';
          const verified = full.email_verified
            ? '<i class="icon-base ti tabler-mail-check icon-14px text-success ms-1" title="Email verificado"></i>'
            : '<i class="icon-base ti tabler-mail-x icon-14px text-warning ms-1" title="Sin verificar"></i>';
          return `<div class="d-flex flex-column">
            <span class="text-nowrap small">${full.last_login_at}${verified}</span>
            ${full.last_login_ip ? `<small class="text-muted">${full.last_login_ip}</small>` : ''}
          </div>`;
        }
      },
      {
        targets: 7,
        responsivePriority: 7,
        render: (d, t, full) => `<span class="text-truncate d-flex align-items-center text-heading">${full.role}</span>`
      },
      {
        targets: 8,
        responsivePriority: 3,
        render: (d, t, full) => full.status
          ? `<span class="badge ${full.status_class}">${full.status_label}</span>`
          : '—'
      },
      {
        targets: -1,
        title: 'Acciones',
        searchable: false,
        orderable: false,
        responsivePriority: 1,
        render: function (data, type, full) {
          const name = (full.name || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');

          // ── Siempre: Ver perfil ───────────────────────────────────────
          const showBtn = full.show_url
            ? `<a href="${full.show_url}" class="btn btn-sm btn-icon" title="Ver perfil">
                 <i class="icon-base ti tabler-eye icon-22px"></i>
               </a>` : '';

          // ── Usuario en papelera ──────────────────────────────────────
          if (full.deleted_at) {
            @can('users.restore')
            const restoreBtn = `<button type="button" class="btn btn-sm btn-icon text-success"
              title="Restaurar usuario"
              data-restore-url="${full.restore_url}"
              data-user-name="${name}">
              <i class="icon-base ti tabler-restore icon-22px"></i>
            </button>`;
            @else
            const restoreBtn = '';
            @endcan

            @can('users.forceDelete')
            const forceDelBtn = `<button type="button" class="btn btn-sm btn-icon text-danger"
              title="Eliminar permanentemente"
              data-force-delete-url="${full.force_delete_url}"
              data-user-name="${name}">
              <i class="icon-base ti tabler-trash-x icon-22px"></i>
            </button>`;
            @else
            const forceDelBtn = '';
            @endcan

            return `<div class="d-flex align-items-center gap-1">${showBtn}${restoreBtn}${forceDelBtn}</div>`;
          }

          // ── Usuario activo: Editar (siempre visible si tiene permiso) ─
          @can('users.edit')
          const editBtn = full.edit_url
            ? `<a href="${full.edit_url}" class="btn btn-sm btn-icon" title="Editar">
                 <i class="icon-base ti tabler-edit icon-22px"></i>
               </a>` : '';
          @else
          const editBtn = '';
          @endcan

          // ── Eliminar: visible directamente (impacto medio, reversible) ─
          @can('users.delete')
          const deleteBtn = full.delete_url
            ? `<form id="del-user-${full.id}" action="${full.delete_url}" method="POST" class="d-inline">
                 <input type="hidden" name="_token" value="{{ csrf_token() }}">
                 <input type="hidden" name="_method" value="DELETE">
               </form>
               <button type="button" class="btn btn-sm btn-icon text-danger" title="Eliminar"
                 onclick="confirmDelete('del-user-${full.id}', '${name}')">
                 <i class="icon-base ti tabler-trash icon-22px"></i>
               </button>` : '';
          @else
          const deleteBtn = '';
          @endcan

          // ── Dropdown: acciones de mayor impacto / menor frecuencia ────
          const dropdownItems = [
            @can('users.edit')
            full.verify_email_url
              ? `<a href="javascript:;" class="dropdown-item"
                   data-verify-url="${full.verify_email_url}"
                   data-user-name="${name}">
                   <i class="icon-base ti tabler-mail-check icon-sm me-2 text-warning"></i>Verificar email
                 </a>` : '',
            full.resend_verify_url
              ? `<a href="javascript:;" class="dropdown-item"
                   data-resend-url="${full.resend_verify_url}"
                   data-user-name="${name}">
                   <i class="icon-base ti tabler-send icon-sm me-2 text-info"></i>Reenviar verificación
                 </a>` : '',
            full.reset_password_url
              ? `<a href="javascript:;" class="dropdown-item"
                   data-reset-url="${full.reset_password_url}"
                   data-user-name="${name}">
                   <i class="icon-base ti tabler-key icon-sm me-2 text-secondary"></i>Resetear contraseña
                 </a>` : '',
            @endcan
            full.impersonate_url
              ? `<form id="impersonate-user-${full.id}" action="${full.impersonate_url}" method="POST" class="d-none">
                   <input type="hidden" name="_token" value="{{ csrf_token() }}">
                 </form>
                 <a href="javascript:;" class="dropdown-item"
                   onclick="confirmAction({
                     title: 'Iniciar sesión como ${name}',
                     text: 'Verás el panel exactamente como este usuario. Puedes volver a tu sesión en cualquier momento.',
                     confirmText: 'Sí, continuar',
                     onConfirm: () => document.getElementById('impersonate-user-${full.id}').submit()
                   })">
                   <i class="icon-base ti tabler-user-shield icon-sm me-2 text-primary"></i>Iniciar sesión como
                 </a>` : '',
          ].filter(Boolean).join('');

          const dropdown = dropdownItems
            ? `<div class="dropdown">
                 <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                   <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                 </button>
                 <div class="dropdown-menu dropdown-menu-end m-0">${dropdownItems}</div>
               </div>` : '';

          return `<div class="d-flex align-items-center gap-1">${showBtn}${editBtn}${deleteBtn}${dropdown}</div>`;
        }
      }
    ],
    select: { style: 'multi', selector: 'td:nth-child(1), td:nth-child(2)' },
    order: [[2, 'asc']],
    responsive: {
      details: {
        display: DataTable.Responsive.display.modal({
          header: row => 'Detalles de ' + (row.data().name || '')
        }),
        type: 'column',
        renderer: function (api, rowIdx, columns) {
          const data = columns
            .filter(col => col.columnIndex > 1 && col.title !== '' && !col.title.includes('<input'))
            .map(col => `<tr><td>${col.title}:</td><td>${col.data}</td></tr>`)
            .join('');
          if (!data) return false;
          const div = document.createElement('div');
          div.classList.add('table-responsive');
          div.innerHTML = `<table class="table"><tbody>${data}</tbody></table>`;
          return div;
        }
      }
    },
    layout: {
      topStart: {
        rowClass: 'row m-3 my-0 justify-content-between',
        features: [{ pageLength: { menu: [...new Set([10, 25, 50, 100, defaultPerPage])].sort((a,b)=>a-b), text: '_MENU_' } }]
      },
      topEnd: {
        features: [
          { search: { placeholder: 'Buscar usuario, cargo, DNI…', text: '_INPUT_' } },
          {
            buttons: [
              {
                extend: 'collection',
                className: 'btn btn-label-secondary dropdown-toggle me-4',
                text: '<i class="icon-base ti tabler-upload me-2 icon-sm"></i>Exportar',
                buttons: [
                  {
                    text: '<i class="icon-base ti tabler-file-type-pdf me-2 text-danger"></i>PDF',
                    className: 'dropdown-item',
                    action: () => exportProfesional('pdf')
                  },
                  {
                    text: '<i class="icon-base ti tabler-file-spreadsheet me-2 text-success"></i>Excel',
                    className: 'dropdown-item',
                    action: () => exportProfesional('excel')
                  },
                  {
                    text: '<i class="icon-base ti tabler-file-text me-2 text-info"></i>CSV',
                    className: 'dropdown-item',
                    action: () => exportProfesional('csv')
                  },
                ]
              }
              @can('users.create')
              ,{
                text: '<i class="icon-base ti tabler-plus icon-sm me-0 me-sm-2"></i><span class="d-none d-sm-inline-block">Nuevo Usuario</span>',
                className: 'btn btn-primary',
                action: () => window.location.href = '{{ route('admin.users.create') }}'
              }
              @endcan
            ]
          }
        ]
      },
      bottomStart: {
        rowClass: 'row mx-3 justify-content-between',
        features: [{ info: { text: 'Mostrando _START_ a _END_ de _TOTAL_ usuarios' } }]
      },
      bottomEnd: 'paging'
    },
    displayLength: 10,
    language: {
      info:         'Mostrando _START_–_END_ de _TOTAL_ usuarios',
      infoEmpty:    'No hay usuarios registrados',
      infoFiltered: '(filtrado de _MAX_ usuarios en total)',
      zeroRecords:  'No se encontraron usuarios con estos filtros',
      emptyTable:   'No hay usuarios en el sistema',
      select: {
        rows: { _: '%d usuarios seleccionados', 0: '', 1: '1 usuario seleccionado' }
      }
    },
    initComplete: () => document.querySelectorAll('.dt-buttons .btn').forEach(b => b.classList.remove('btn-secondary'))
  });

  // ── Función exportBody reutilizable ───────────────────────────────────────
  function exportBody(inner) {
    if (!inner || inner.indexOf('<') === -1) return inner;
    const doc = new DOMParser().parseFromString(inner, 'text/html');
    const el  = doc.querySelector('.fw-medium') || doc.querySelector('.user-name');
    return el ? el.textContent.trim() : (doc.body.textContent || '').trim();
  }

  // ── Reload con syncUI ─────────────────────────────────────────────────────
  function reload() { dt.ajax.reload(); syncUI(); }

  // ── Filtros principales — change inmediato ────────────────────────────────
  document.querySelectorAll('.filter-main').forEach(el => el.addEventListener('change', reload));

  // ── Filtros avanzados — change inmediato, fecha con debounce ─────────────
  let debounce;
  document.querySelectorAll('.filter-adv').forEach(el => {
    const isDate = el.type === 'date';
    el.addEventListener(isDate ? 'input' : 'change', () => {
      clearTimeout(debounce);
      debounce = setTimeout(reload, isDate ? 400 : 0);
    });
  });

  // ── Stat cards clickeables ────────────────────────────────────────────────
  document.querySelectorAll('.stat-card').forEach(card => {
    card.addEventListener('click', function () {
      // Limpiar todos los filtros antes de aplicar el de la card
      document.querySelectorAll('.filter-main, .filter-adv').forEach(el => {
        if (el.type === 'radio') el.checked = el.value === '';
        else el.value = '';
      });

      // Aplicar filtro según tipo de card
      if ('filterStatus' in this.dataset) {
        document.getElementById('filterStatus').value = this.dataset.filterStatus;
      } else if ('filterVerificado' in this.dataset) {
        document.querySelector(`input[name="filterVerificado"][value="${this.dataset.filterVerificado}"]`).checked = true;
      } else if ('filterAcceso' in this.dataset) {
        document.getElementById('filterUltimoAcceso').value = this.dataset.filterAcceso;
      }

      document.querySelectorAll('.stat-card').forEach(c => c.classList.remove('shadow'));
      this.classList.add('shadow');
      reload();
    });
  });

  // ── Limpiar TODOS los filtros ─────────────────────────────────────────────
  document.getElementById('btnClearAll')?.addEventListener('click', () => {
    document.querySelectorAll('.filter-main, .filter-adv').forEach(el => {
      if (el.type === 'radio') {
        el.checked = el.value === '';
      } else {
        el.value = '';
      }
    });
    document.querySelectorAll('.stat-card').forEach(c => c.classList.remove('shadow'));
    reload();
  });

  // ── Limpiar solo avanzados ────────────────────────────────────────────────
  document.getElementById('btnClearAdv')?.addEventListener('click', () => {
    document.querySelectorAll('.filter-adv').forEach(el => {
      if (el.type === 'radio') el.checked = el.value === '';
      else el.value = '';
    });
    reload();
  });

  // ── Reset password (delegado al document) ─────────────────────────────────
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-reset-url]');
    if (!btn) return;
    confirmAction({
      title      : '¿Resetear contraseña?',
      text       : `La contraseña de "${btn.dataset.userName}" será reemplazada por su DNI (o una clave temporal si no tiene DNI).`,
      confirmText: 'Sí, resetear',
      cancelText : 'Cancelar',
      isDanger   : true,
      onConfirm  : () => {
        fetch(btn.dataset.resetUrl, {
          method : 'POST',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }
        })
        .then(r => r.json())
        .then(d => showToast('success', d.message ?? 'Contraseña restablecida.'))
        .catch(() => showToast('error', 'No se pudo restablecer la contraseña.'));
      }
    });
  });

  // ── Helper fetch POST/DELETE con JSON ────────────────────────────────────
  function jsonFetch(url, method = 'POST', body = null) {
    return fetch(url, {
      method,
      headers: {
        'X-CSRF-TOKEN' : '{{ csrf_token() }}',
        'Accept'       : 'application/json',
        'Content-Type' : 'application/json',
      },
      body: body ? JSON.stringify(body) : null,
    }).then(r => r.json());
  }

  // ── Verificar email manualmente ───────────────────────────────────────────
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-verify-url]');
    if (!btn) return;
    confirmAction({
      title      : '¿Verificar email?',
      text       : `Se marcará el email de "${btn.dataset.userName}" como verificado sin enviar correo.`,
      confirmText: 'Sí, verificar',
      cancelText : 'Cancelar',
      isDanger   : false,
      onConfirm  : () => jsonFetch(btn.dataset.verifyUrl)
        .then(d => { showToast('success', d.message ?? 'Email verificado.'); dt.ajax.reload(); })
        .catch(() => showToast('error', 'No se pudo verificar el email.')),
    });
  });

  // ── Reenviar email de verificación ───────────────────────────────────────
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-resend-url]');
    if (!btn) return;
    jsonFetch(btn.dataset.resendUrl)
      .then(d => showToast('success', d.message ?? 'Correo de verificación enviado.'))
      .catch(() => showToast('error', 'No se pudo enviar el correo.'));
  });

  // ── Restaurar usuario ────────────────────────────────────────────────────
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-restore-url]');
    if (!btn) return;
    confirmAction({
      title      : '¿Restaurar usuario?',
      text       : `El usuario "${btn.dataset.userName}" volverá a estar activo en el sistema.`,
      confirmText: 'Sí, restaurar',
      cancelText : 'Cancelar',
      isDanger   : false,
      onConfirm  : () => jsonFetch(btn.dataset.restoreUrl)
        .then(d => { showToast('success', d.message ?? 'Usuario restaurado.'); dt.ajax.reload(); syncUI(); })
        .catch(() => showToast('error', 'No se pudo restaurar el usuario.')),
    });
  });

  // ── Eliminar permanentemente ──────────────────────────────────────────────
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-force-delete-url]');
    if (!btn) return;
    confirmAction({
      title      : '¿Eliminar permanentemente?',
      text       : `Esta acción es IRREVERSIBLE. El usuario "${btn.dataset.userName}" y todos sus datos serán borrados definitivamente.`,
      confirmText: 'Sí, eliminar para siempre',
      cancelText : 'Cancelar',
      isDanger   : true,
      onConfirm  : () => jsonFetch(btn.dataset.forceDeleteUrl, 'DELETE')
        .then(d => { showToast('success', d.message ?? 'Usuario eliminado permanentemente.'); dt.ajax.reload(); syncUI(); })
        .catch(() => showToast('error', 'No se pudo eliminar permanentemente.')),
    });
  });

  // ── Acciones masivas ──────────────────────────────────────────────────────
  const bulkBar   = document.getElementById('bulkBar');
  const bulkCount = document.getElementById('bulkCount');

  function updateBulkBar() {
    const rows    = dt.rows({ selected: true }).data().toArray();
    const count   = rows.length;

    if (!bulkBar) return;
    bulkBar.classList.toggle('d-none', count === 0);
    if (bulkCount) bulkCount.textContent = count;
    if (count === 0) return;

    const statuses   = rows.map(r => r.status);
    const hasActive  = statuses.includes('active');
    const hasInactive= statuses.includes('inactive');
    const hasBanned  = statuses.includes('banned');
    const hasDeleted = rows.some(r => r.deleted_at);
    const hasLiving  = rows.some(r => !r.deleted_at);

    const show = (action, visible) => {
      const btn = bulkBar.querySelector(`[data-action="${action}"]`);
      if (btn) btn.classList.toggle('d-none', !visible);
    };

    show('activate',     hasInactive || hasBanned);
    show('deactivate',   hasActive);
    show('ban',          hasActive || hasInactive);
    show('verify_email', hasLiving);
    show('restore',      hasDeleted);
    show('delete',       hasLiving);
    show('force_delete', true);
  }

  // Cuando DataTable selecciona/deselecciona → sincronizar el checkbox visual
  dt.on('select', function (e, dt2, type, indexes) {
    if (type !== 'row') return;
    dt2.rows(indexes).nodes().each(function (row) {
      const cb = row.querySelector('.dt-checkboxes');
      if (cb) cb.checked = true;
    });
    updateBulkBar();
  });
  dt.on('deselect', function (e, dt2, type, indexes) {
    if (type !== 'row') return;
    dt2.rows(indexes).nodes().each(function (row) {
      const cb = row.querySelector('.dt-checkboxes');
      if (cb) cb.checked = false;
    });
    updateBulkBar();
  });
  // Al redibujar (nueva página, filtro) → limpiar checkboxes visuales huérfanos
  dt.on('draw', function () {
    dtUsersTable.querySelectorAll('.dt-checkboxes').forEach(cb => { cb.checked = false; });
    updateBulkBar();
  });

  // Click directo en el checkbox de fila → toggle select en DataTable
  dtUsersTable.addEventListener('click', function (e) {
    const cb = e.target.closest('.dt-checkboxes');
    if (!cb) return;
    e.stopPropagation();
    const tr = cb.closest('tr');
    if (!tr) return;
    const row = dt.row(tr);
    if (row.count() === 0) return;
    if (row.selected()) { row.deselect(); } else { row.select(); }
  });

  // Checkbox "seleccionar todos" en el header
  const dtUsersCheckAll = document.getElementById('dtUsersCheckAll');
  if (dtUsersCheckAll) {
    dtUsersCheckAll.addEventListener('change', function () {
      if (this.checked) { dt.rows().select(); } else { dt.rows().deselect(); }
    });
    // Actualizar estado indeterminate del header al cambiar selección
    dt.on('select deselect draw', function () {
      const total    = dt.rows().count();
      const selected = dt.rows({ selected: true }).count();
      dtUsersCheckAll.checked       = total > 0 && selected === total;
      dtUsersCheckAll.indeterminate = selected > 0 && selected < total;
    });
  }

  const actionLabels = {
    activate    : 'activar',
    deactivate  : 'inactivar',
    ban         : 'bloquear',
    delete      : 'eliminar (papelera)',
    restore     : 'restaurar',
    force_delete: 'eliminar PERMANENTEMENTE',
    verify_email: 'verificar email de',
  };

  document.querySelectorAll('.bulk-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const action  = this.dataset.action;
      const rows    = dt.rows({ selected: true }).data().toArray();
      const ids     = rows.map(r => r.id);
      const isDanger = ['delete','force_delete','ban'].includes(action);

      if (!ids.length) return;

      confirmAction({
        title      : `¿${actionLabels[action]?.charAt(0).toUpperCase() + actionLabels[action]?.slice(1)} ${ids.length} usuario(s)?`,
        text       : action === 'force_delete'
          ? `Esta acción es IRREVERSIBLE. Se eliminarán ${ids.length} usuario(s) permanentemente.`
          : `Se aplicará la acción a ${ids.length} usuario(s) seleccionado(s).`,
        confirmText: 'Confirmar',
        cancelText : 'Cancelar',
        isDanger,
        onConfirm  : () => jsonFetch('{{ route('admin.users.bulk-action') }}', 'POST', { ids, action })
          .then(d => {
            showToast('success', d.message ?? 'Acción aplicada.');
            dt.rows({ selected: true }).deselect();
            dt.ajax.reload();
            syncUI();
          })
          .catch(() => showToast('error', 'No se pudo completar la acción masiva.')),
      });
    });
  });

  // ── Exportación profesional (PDF / Excel / CSV) ───────────────────────────
  function exportProfesional(formato) {
    const params = new URLSearchParams({
      ...getMainFilters(),
      ...getAdvFilters(),
      search: document.querySelector('.dt-search input')?.value ?? '',
    });
    // Limpiar params vacíos
    for (const [k, v] of [...params]) { if (!v) params.delete(k); }

    const urls = {
      pdf  : '{{ route("admin.users.export.pdf") }}',
      excel: '{{ route("admin.users.export.excel") }}',
      csv  : '{{ route("admin.users.export.csv") }}',
    };

    const url = urls[formato] + (params.toString() ? '?' + params.toString() : '');

    // Toast informativo y descarga en nueva pestaña
    showToast('info', `Preparando ${formato.toUpperCase()}… se descargará en breve.`);
    window.open(url, '_blank');
  }

  // ── Ajuste de clases del layout DataTable ─────────────────────────────────
  setTimeout(() => {
    [
      { s: '.dt-buttons .btn',        r: 'btn-secondary' },
      { s: '.dt-search .form-control', r: 'form-control-sm' },
      { s: '.dt-length .form-select',  r: 'form-select-sm', a: 'ms-0' },
      { s: '.dt-length',               a: 'mb-md-6 mb-0' },
      { s: '.dt-layout-end', r: 'justify-content-between', a: 'd-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap' },
      { s: '.dt-buttons',      a: 'd-flex gap-4 mb-md-0 mb-4' },
      { s: '.dt-layout-table', r: 'row mt-2' },
      { s: '.dt-layout-full',  r: 'col-md col-12', a: 'table-responsive' }
    ].forEach(({ s, r, a }) => {
      document.querySelectorAll(s).forEach(el => {
        if (r) r.split(' ').forEach(c => el.classList.remove(c));
        if (a) a.split(' ').forEach(c => el.classList.add(c));
      });
    });
  }, 100);

});
</script>
@endsection
