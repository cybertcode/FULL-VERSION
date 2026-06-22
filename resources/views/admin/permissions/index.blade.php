@extends('admin/layouts/master')

@section('title', 'Permisos')

@section('admin-vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  ])
@endsection

@section('admin-vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('admin-content')

<x-breadcrumb :items="[['label' => 'Administración', 'url' => '#'], ['label' => 'Permisos del sistema']]" />

{{-- Stats cards ─────────────────────────────────────────────────────── --}}
<div class="row g-4 mb-6" id="permStatsRow">
  <div class="col-6 col-md-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-md flex-shrink-0">
          <span class="avatar-initial rounded bg-label-primary">
            <i class="icon-base ti tabler-shield-lock icon-md"></i>
          </span>
        </div>
        <div>
          <div class="text-muted small">Total permisos</div>
          <div class="fw-bold h5 mb-0" id="statTotal">—</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-md flex-shrink-0">
          <span class="avatar-initial rounded bg-label-info">
            <i class="icon-base ti tabler-layout-grid icon-md"></i>
          </span>
        </div>
        <div>
          <div class="text-muted small">Módulos</div>
          <div class="fw-bold h5 mb-0" id="statModules">—</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-md flex-shrink-0">
          <span class="avatar-initial rounded bg-label-success">
            <i class="icon-base ti tabler-users-group icon-md"></i>
          </span>
        </div>
        <div>
          <div class="text-muted small">Asignados a roles</div>
          <div class="fw-bold h5 mb-0" id="statAssigned">—</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card h-100">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="avatar avatar-md flex-shrink-0">
          <span class="avatar-initial rounded bg-label-warning">
            <i class="icon-base ti tabler-shield-off icon-md"></i>
          </span>
        </div>
        <div>
          <div class="text-muted small">Sin asignar</div>
          <div class="fw-bold h5 mb-0" id="statUnassigned">—</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Tabla ────────────────────────────────────────────────────────────── --}}
<div class="card">
  {{-- Toolbar: filtros + export ──────────────────────────────────────── --}}
  <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3 py-3">
    <div class="d-flex flex-wrap gap-2 align-items-center">
      <span class="text-muted small fw-semibold text-nowrap">Módulo:</span>
      <button class="btn btn-sm btn-primary module-filter-btn active" data-module="">Todos</button>
      {{-- botones de módulo se generan dinámicamente por JS --}}
    </div>
    @can('roles.viewAny')
    <div class="btn-group">
      <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="icon-base ti tabler-download me-1"></i> Exportar
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportPermissosProfesional('pdf')">
          <i class="icon-base ti tabler-file-type-pdf me-2 text-danger"></i>PDF
        </a></li>
        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportPermissosProfesional('excel')">
          <i class="icon-base ti tabler-file-spreadsheet me-2 text-success"></i>Excel
        </a></li>
        <li><a class="dropdown-item" href="javascript:void(0)" onclick="exportPermissosProfesional('csv')">
          <i class="icon-base ti tabler-file-text me-2 text-info"></i>CSV
        </a></li>
      </ul>
    </div>
    @endcan
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-permissions table border-top">
      <thead>
        <tr>
          <th></th>
          <th></th>
          <th>Permiso</th>
          <th>Módulo</th>
          <th>Asignado a</th>
          <th>Fecha</th>
          <th>Acciones</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

{{-- Modal de detalle ─────────────────────────────────────────────────── --}}
<div class="modal fade" id="permDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="icon-base ti tabler-shield-check me-2 text-primary"></i>
          Detalle del permiso
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-4">
          <div class="text-muted small mb-1">Nombre técnico</div>
          <code id="pdName" class="fs-6"></code>
        </div>
        <div class="mb-4">
          <div class="text-muted small mb-1">Label visible</div>
          <div id="pdLabel" class="fw-semibold"></div>
        </div>
        <div class="row mb-4">
          <div class="col-6">
            <div class="text-muted small mb-1">Módulo</div>
            <span id="pdModule" class="badge bg-label-secondary"></span>
          </div>
          <div class="col-6">
            <div class="text-muted small mb-1">Acción</div>
            <span id="pdAction" class="badge bg-label-info"></span>
          </div>
        </div>
        <div class="mb-4">
          <div class="text-muted small mb-2">Roles que tienen este permiso</div>
          <div id="pdRoles"></div>
        </div>
        <div>
          <div class="text-muted small mb-1">Fecha de creación</div>
          <div id="pdCreatedAt" class="text-muted small"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-label-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('admin-page-script')
<script>
'use strict';

// ── URLs de export ─────────────────────────────────────────────────────
const EXPORT_URLS = {
  pdf:   '{{ route('admin.permissions.export.pdf') }}',
  excel: '{{ route('admin.permissions.export.excel') }}',
  csv:   '{{ route('admin.permissions.export.csv') }}',
};

function exportPermissosProfesional(format) {
  const url    = new URL(EXPORT_URLS[format], location.origin);
  const mod    = document.querySelector('.module-filter-btn.active')?.dataset.module || '';
  const search = document.querySelector('.dt-search input')?.value.trim() || '';
  if (mod)    url.searchParams.set('module', mod);
  if (search) url.searchParams.set('search', search);
  showToast('info', `Preparando ${format.toUpperCase()}… se descargará en breve.`);
  window.open(url.toString(), '_blank');
}

document.addEventListener('DOMContentLoaded', function () {
  let allData      = [];
  let activeModule = '';

  const detailModal = new bootstrap.Modal(document.getElementById('permDetailModal'));
  const dtEl        = document.querySelector('.datatables-permissions');
  if (!dtEl) return;

  const defaultPerPage = @json(config('app-settings.pagination.default', 15));

  const dt = new DataTable(dtEl, {
    pageLength: defaultPerPage,
    ajax: {
      url: '{{ route('admin.permissions.data') }}',
      dataSrc: function (json) {
        allData = json.data;
        updateStats(allData);
        buildModuleFilters(allData);
        return allData;
      }
    },
    columns: [
      { data: 'id' },
      { data: 'id' },
      { data: 'label' },
      { data: 'module' },
      { data: 'roles' },
      { data: 'created_at' },
      { data: null, defaultContent: '' },
    ],
    columnDefs: [
      {
        className: 'control',
        orderable: false,
        searchable: false,
        responsivePriority: 2,
        targets: 0,
        render: () => ''
      },
      { targets: 1, searchable: false, visible: false },
      {
        targets: 2,
        render: (data, type, full) =>
          `<span class="text-nowrap text-heading fw-medium">${full.label}</span>
           <div class="text-muted small">${full.name}</div>`
      },
      {
        targets: 3,
        render: (data, type, full) => {
          const colors = {
            users: 'primary', roles: 'success', settings: 'warning',
            activitylog: 'info', dashboard: 'secondary', permisos: 'danger'
          };
          const labels = {
            users:       'Usuarios',
            roles:       'Roles',
            settings:    'Configuración',
            activitylog: 'Reg. Actividad',
            dashboard:   'Dashboard',
            permisos:    'Permisos',
          };
          const c = colors[full.module] || 'secondary';
          const l = labels[full.module] || full.module;
          return `<span class="badge bg-label-${c}">${l}</span>`;
        }
      },
      {
        targets: 4,
        orderable: false,
        render: (data, type, full) => {
          if (!full.roles || full.roles.length === 0)
            return '<span class="text-muted small">Sin asignar</span>';
          return full.roles.map(r =>
            `<span class="badge bg-label-primary me-1 mb-1">${r}</span>`
          ).join('');
        }
      },
      {
        targets: 5,
        orderable: false,
        render: (data, type, full) =>
          `<span class="text-nowrap text-muted small">${full.created_at ?? '—'}</span>`
      },
      {
        targets: 6,
        orderable: false,
        searchable: false,
        responsivePriority: 1,
        render: (data, type, full) =>
          `<button class="btn btn-sm btn-icon btn-label-secondary perm-detail-btn"
                   data-id="${full.id}" title="Ver detalle">
             <i class="icon-base ti tabler-eye"></i>
           </button>`
      },
    ],
    order: [[2, 'asc']],
    layout: {
      topStart: {
        rowClass: 'row m-3 my-0 justify-content-between',
        features: [{ pageLength: { menu: [...new Set([10, 25, 50, 100, defaultPerPage])].sort((a,b)=>a-b), text: 'Mostrar _MENU_' } }]
      },
      topEnd: {
        features: [{ search: { placeholder: 'Buscar permisos', text: '_INPUT_' } }]
      },
      bottomStart: {
        rowClass: 'row mx-3 justify-content-between',
        features: ['info']
      },
      bottomEnd: 'paging'
    },
    language: {
      paginate: {
        next:     '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
        first:    '<i class="icon-base ti tabler-chevrons-left icon-18px"></i>',
        last:     '<i class="icon-base ti tabler-chevrons-right icon-18px"></i>',
      },
      info:            'Mostrando _START_ a _END_ de _TOTAL_ permisos',
      infoFiltered:    '(filtrado de _MAX_ permisos en total)',
      infoEmpty:       'Mostrando 0 a 0 de 0 permisos',
      search:          'Buscar:',
      lengthMenu:      'Mostrar _MENU_',
      zeroRecords:     'No se encontraron permisos',
      emptyTable:      'No hay permisos registrados',
      loadingRecords:  'Cargando...',
      processing:      'Procesando...',
    },
    responsive: {
      details: {
        display: DataTable.Responsive.display.modal({
          header: row => 'Detalle de ' + row.data()['label']
        }),
        type: 'column',
        renderer: function (api, rowIdx, columns) {
          const data = columns
            .filter(col => col.title !== '')
            .map(col => `<tr><td>${col.title}:</td><td>${col.data}</td></tr>`)
            .join('');
          if (!data) return false;
          const div = document.createElement('div');
          div.classList.add('table-responsive');
          const table = document.createElement('table');
          table.classList.add('table');
          table.innerHTML = `<tbody>${data}</tbody>`;
          div.appendChild(table);
          return div;
        }
      }
    }
  });

  // ── Fix CSS de DataTable ─────────────────────────────────────────────
  setTimeout(() => {
    [
      { selector: '.dt-search', classToAdd: 'me-4' },
      { selector: '.dt-search .form-control', classToRemove: 'form-control-sm' },
      { selector: '.dt-length', classToAdd: 'mb-0 mb-md-5' },
      { selector: '.dt-length .form-select', classToRemove: 'form-select-sm' },
      { selector: '.dt-layout-start', classToAdd: 'mt-0 px-5' },
      { selector: '.dt-layout-end', classToRemove: 'justify-content-between', classToAdd: 'justify-content-md-between justify-content-center d-flex flex-wrap gap-md-4 mb-sm-0 mb-6 mt-0' },
      { selector: '.dt-layout-table', classToRemove: 'row mt-2' },
      { selector: '.dt-layout-full', classToRemove: 'col-md col-12', classToAdd: 'table-responsive' },
    ].forEach(({ selector, classToRemove, classToAdd }) => {
      document.querySelectorAll(selector).forEach(el => {
        classToRemove?.split(' ').forEach(c => el.classList.remove(c));
        classToAdd?.split(' ').forEach(c => el.classList.add(c));
      });
    });
  }, 100);

  // ── Stats ────────────────────────────────────────────────────────────
  function updateStats(data) {
    const total      = data.length;
    const modules    = new Set(data.map(p => p.module)).size;
    const assigned   = data.filter(p => p.roles && p.roles.length > 0).length;
    const unassigned = total - assigned;

    document.getElementById('statTotal').textContent      = total;
    document.getElementById('statModules').textContent    = modules;
    document.getElementById('statAssigned').textContent   = assigned;
    document.getElementById('statUnassigned').textContent = unassigned;
  }

  // ── Botones de módulo ────────────────────────────────────────────────
  const moduleColors = {
    users: 'primary', roles: 'success', settings: 'warning',
    activitylog: 'info', dashboard: 'secondary'
  };
  const moduleLabels = {
    users:       'Usuarios',
    roles:       'Roles',
    settings:    'Configuración',
    activitylog: 'Registro de actividad',
    dashboard:   'Dashboard',
    permisos:    'Permisos',
  };

  function buildModuleFilters(data) {
    const modules = [...new Set(data.map(p => p.module))].sort();
    const bar = document.querySelector('.module-filter-btn[data-module=""]').parentElement;

    // Eliminar botones anteriores (excepto "Todos")
    bar.querySelectorAll('.module-filter-btn:not([data-module=""])').forEach(b => b.remove());

    modules.forEach(mod => {
      const count = data.filter(p => p.module === mod).length;
      const c     = moduleColors[mod] || 'secondary';
      const label = moduleLabels[mod] || mod;
      const btn   = document.createElement('button');
      btn.className      = `btn btn-sm btn-label-${c} module-filter-btn`;
      btn.dataset.module = mod;
      btn.innerHTML = `${label} <span class="badge bg-${c} ms-1">${count}</span>`;
      bar.appendChild(btn);
    });
  }

  // ── Filtro por módulo click ──────────────────────────────────────────
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.module-filter-btn');
    if (!btn) return;

    document.querySelectorAll('.module-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    activeModule = btn.dataset.module;

    // Filtrar tabla por módulo usando search de columna 3
    if (activeModule) {
      dt.column(3).search('^' + activeModule + '$', true, false).draw();
    } else {
      dt.column(3).search('').draw();
    }
  });

  // ── Modal de detalle ─────────────────────────────────────────────────
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.perm-detail-btn');
    if (!btn) return;

    const id   = parseInt(btn.dataset.id);
    const perm = allData.find(p => p.id === id);
    if (!perm) return;

    const modLabels = {
      users: 'Usuarios', roles: 'Roles', settings: 'Configuración',
      activitylog: 'Reg. Actividad', dashboard: 'Dashboard', permisos: 'Permisos',
    };
    document.getElementById('pdName').textContent   = perm.name;
    document.getElementById('pdLabel').textContent  = perm.label;
    document.getElementById('pdModule').textContent = modLabels[perm.module] || perm.module;
    document.getElementById('pdAction').textContent = perm.action || '—';
    document.getElementById('pdCreatedAt').textContent = perm.created_at ?? '—';

    const rolesEl = document.getElementById('pdRoles');
    if (!perm.roles || perm.roles.length === 0) {
      rolesEl.innerHTML = '<span class="badge bg-label-warning">Sin asignar a ningún rol</span>';
    } else {
      rolesEl.innerHTML = perm.roles.map(r =>
        `<span class="badge bg-label-primary me-1 mb-1">${r}</span>`
      ).join('');
    }

    detailModal.show();
  });
});
</script>
@endsection
