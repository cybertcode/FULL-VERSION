@extends('admin/layouts/master')

@section('title', 'Roles y Permisos')

@section('admin-vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  ])
@endsection

@section('admin-vendor-script')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('admin-content')

<x-breadcrumb :items="[['label' => 'Administración', 'url' => '#'], ['label' => 'Roles y Permisos']]" />

<p class="mb-6 text-body-secondary">Un rol define qué acciones puede realizar un usuario en el sistema. Asigna roles para controlar el acceso.</p>

{{-- ─── Role cards ─────────────────────────────────────────────────────────── --}}
<div class="row g-6">

  @foreach ($roles as $role)
    @php $isSuperAdmin = $role->name === 'Super-Admin'; @endphp
    <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="fw-normal mb-0 text-body">
              Total {{ $role->users_count }} {{ $role->users_count === 1 ? 'usuario' : 'usuarios' }}
            </h6>
            @if ($role->users_count > 0)
              <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                @foreach ($role->topUsers as $u)
                  <li class="avatar pull-up"
                      data-bs-toggle="tooltip" data-popup="tooltip-custom"
                      data-bs-placement="top" title="{{ $u->name }}">
                    @if ($u->avatar)
                      <img class="rounded-circle" src="{{ $u->avatar_url }}" alt="{{ $u->name }}" />
                    @else
                      @php
                        $colors = ['primary','success','danger','warning','info'];
                        $col    = $colors[ord($u->name[0]) % count($colors)];
                        $ini    = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', $u->name), 0, 2)));
                      @endphp
                      <span class="avatar-initial rounded-circle bg-label-{{ $col }}">{{ $ini }}</span>
                    @endif
                  </li>
                @endforeach
                @if ($role->users_count > 4)
                  <li class="avatar">
                    <span class="avatar-initial rounded-circle pull-up"
                          data-bs-toggle="tooltip" data-bs-placement="bottom"
                          title="{{ $role->users_count - 4 }} más">
                      +{{ $role->users_count - 4 }}
                    </span>
                  </li>
                @endif
              </ul>
            @endif
          </div>

          <div class="d-flex justify-content-between align-items-end">
            <div class="role-heading">
              <h5 class="mb-1">{{ $role->name }}</h5>
              @if ($isSuperAdmin)
                <span class="text-muted small">Acceso total al sistema</span>
              @else
                @if($role->description)
                  <small class="text-muted d-block mb-1">{{ $role->description }}</small>
                @endif
                @can('roles.edit')
                  <a href="javascript:;"
                     data-bs-toggle="modal" data-bs-target="#addRoleModal"
                     class="role-edit-modal"
                     data-role-id="{{ $role->id }}"
                     data-role-name="{{ $role->name }}"
                     data-role-description="{{ $role->description }}"
                     data-role-permissions="{{ $role->permissions->pluck('name')->toJson() }}"
                     data-system-role="0">
                    Editar Rol
                  </a>
                @endcan
              @endif
            </div>

            <div class="d-flex align-items-center gap-1">
              {{-- Copiar rol --}}
              @if (!$isSuperAdmin)
                @can('roles.create')
                  <a href="javascript:void(0);"
                     class="btn btn-icon btn-text-secondary rounded-pill waves-effect clone-role"
                     data-role-id="{{ $role->id }}"
                     data-role-name="{{ $role->name }}"
                     data-bs-toggle="tooltip" title="Copiar rol">
                    <i class="icon-base ti tabler-copy icon-md"></i>
                  </a>
                @endcan
              @endif

              {{-- Eliminar rol --}}
              @if (!$isSuperAdmin)
                @can('roles.delete')
                  @if ($role->users_count === 0)
                    <a href="javascript:void(0);"
                       class="btn btn-icon btn-text-secondary rounded-pill waves-effect"
                       data-bs-toggle="tooltip" title="Eliminar rol"
                       onclick="confirmDeleteUrl('{{ route('admin.roles.destroy', $role) }}', '{{ addslashes($role->name) }}')">
                      <i class="icon-base ti tabler-trash icon-md text-danger"></i>
                    </a>
                  @else
                    <span class="btn btn-icon btn-text-secondary rounded-pill disabled"
                          data-bs-toggle="tooltip" title="No se puede eliminar: tiene usuarios asignados">
                      <i class="icon-base ti tabler-trash icon-md text-muted"></i>
                    </span>
                  @endif
                @endcan
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  @endforeach

  {{-- Card: Agregar nuevo rol --}}
  @can('roles.create')
    <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card h-100">
        <div class="row h-100">
          <div class="col-sm-5">
            <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-4">
              <i class="ti tabler-shield-plus text-primary" style="font-size:5rem;opacity:.85;"></i>
            </div>
          </div>
          <div class="col-sm-7">
            <div class="card-body text-sm-end text-center ps-sm-0">
              <button data-bs-target="#addRoleModal" data-bs-toggle="modal"
                      class="btn btn-sm btn-primary mb-4 text-nowrap add-new-role">
                Agregar Nuevo Rol
              </button>
              <p class="mb-0">Agrega un nuevo rol,<br>si aún no existe.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endcan

  {{-- ─── Alertas de riesgo ────────────────────────────────────────────────── --}}
  @if ($riskStats['inactivePrivileged'] > 0 || $riskStats['sinRol'] > 0)
  <div class="col-12 mt-2">
    <div class="row g-3">
      @if ($riskStats['inactivePrivileged'] > 0)
      <div class="col-md-6">
        <div class="alert alert-warning d-flex align-items-start gap-3 mb-0" role="alert">
          <i class="icon-base ti tabler-alert-triangle icon-24px mt-1 flex-shrink-0"></i>
          <div>
            <div class="fw-semibold">{{ $riskStats['inactivePrivileged'] }} cuenta(s) privilegiada(s) inactiva(s)</div>
            <small>Usuarios con rol <strong>admin</strong> o superior que no ingresan hace más de 30 días. Considera revisar o revocar el acceso.</small>
          </div>
        </div>
      </div>
      @endif
      @if ($riskStats['sinRol'] > 0)
      <div class="col-md-6">
        <div class="alert alert-info d-flex align-items-start gap-3 mb-0" role="alert">
          <i class="icon-base ti tabler-user-question icon-24px mt-1 flex-shrink-0"></i>
          <div>
            <div class="fw-semibold">{{ $riskStats['sinRol'] }} usuario(s) sin rol asignado</div>
            <small>Estos usuarios no tienen ningún rol. Asígnales uno para definir sus permisos en el sistema.</small>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
  @endif

  {{-- ─── Asignación de roles ────────────────────────────────────────────────── --}}
  <div class="col-12">
    <div class="d-flex align-items-center justify-content-between mt-4 mb-2">
      <div>
        <h4 class="mb-1">Asignación de roles</h4>
        <p class="mb-0 text-muted small">Cambia el rol de cada usuario directamente sin entrar a editar su cuenta.</p>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-header border-bottom py-3">
        <div class="row align-items-center g-3">
          <div class="col-6 col-sm-4 col-lg-3">
            <select id="filterRoleTable" class="form-select">
              <option value="">Todos los roles</option>
              @foreach ($roles as $role)
                <option value="{{ $role->name }}">{{ $role->name }} ({{ $role->users_count }})</option>
              @endforeach
            </select>
          </div>
          <div class="col-6 col-sm-4 col-lg-3">
            <select id="filterStatusTable" class="form-select">
              <option value="">Todos los estados</option>
              @foreach ($statuses as $status)
                <option value="{{ $status->value }}">{{ $status->label() }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 col-sm-4 col-lg-6 d-flex justify-content-sm-end align-items-center gap-2 flex-wrap">
            <span class="text-muted small d-none d-lg-inline">
              <i class="icon-base ti tabler-info-circle icon-14px me-1"></i>
              <strong>Super-Admin</strong> no es modificable desde aquí.
            </span>
            <div class="dropdown">
              <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportRolesBtn" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="icon-base ti tabler-upload me-1 icon-sm"></i>
                <span class="d-none d-sm-inline-block">Exportar</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportRolesBtn">
                <li>
                  <a class="dropdown-item" href="javascript:void(0)" onclick="exportRolesProfesional('pdf')">
                    <i class="icon-base ti tabler-file-type-pdf me-2 text-danger"></i>PDF
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="javascript:void(0)" onclick="exportRolesProfesional('excel')">
                    <i class="icon-base ti tabler-file-spreadsheet me-2 text-success"></i>Excel
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="javascript:void(0)" onclick="exportRolesProfesional('csv')">
                    <i class="icon-base ti tabler-file-text me-2 text-info"></i>CSV
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="card-datatable">
        <table class="datatables-role-users table border-top">
          <thead>
            <tr>
              <th></th>
              <th>Usuario</th>
              <th>Rol actual</th>
              <th>Último acceso</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- ─── Modal Historial de rol ──────────────────────────────────────────────── --}}
<div class="modal fade" id="roleHistoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="icon-base ti tabler-history icon-18px me-2 text-primary"></i>
          Historial de rol — <span id="historyUserName"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <div id="historyLoading" class="text-center py-5 d-none">
          <div class="spinner-border text-primary" role="status"></div>
        </div>
        <div id="historyEmpty" class="text-center py-5 text-muted d-none">
          <i class="icon-base ti tabler-mood-empty icon-40px mb-2 d-block"></i>
          Sin cambios registrados aún.
        </div>
        <div class="table-responsive" id="historyTableWrap">
          <table class="table table-sm mb-0">
            <thead class="table-light">
              <tr>
                <th>Fecha</th>
                <th>Anterior</th>
                <th></th>
                <th>Nuevo</th>
                <th>Por</th>
              </tr>
            </thead>
            <tbody id="historyTableBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ─── Modal Crear / Editar Rol ───────────────────────────────────────────── --}}
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-6">
          <h4 class="role-title mb-2">Agregar Nuevo Rol</h4>
          <p class="text-body-secondary">Configura los permisos del rol</p>
        </div>

        <form id="roleForm" method="POST">
          @csrf
          <input type="hidden" name="_method" id="formMethod" value="POST">

          <div class="col-12 form-control-validation mb-3">
            <label class="form-label" for="modalRoleName">Nombre del Rol <span class="text-danger">*</span></label>
            <input type="text" id="modalRoleName" name="name"
                   class="form-control" placeholder="ej. supervisor, auditor"
                   maxlength="100" />
            <div id="roleNameAlert" class="alert alert-info py-2 mt-2 d-none">
              <i class="icon-base ti tabler-info-circle me-1"></i>
              El nombre de los roles del sistema no puede modificarse.
            </div>
          </div>

          <div class="col-12 mb-3">
            <label class="form-label" for="modalRoleDescription">Descripción <span class="text-muted fw-normal">(opcional)</span></label>
            <input type="text" id="modalRoleDescription" name="description"
                   class="form-control" placeholder="Breve descripción del rol y sus responsabilidades"
                   maxlength="200" />
          </div>

          <div class="col-12">
            <h5 class="mb-1">Permisos del Rol</h5>
            <p class="text-body-secondary small mb-4">Marca los permisos que tendrá este rol.</p>
            @php
              $moduleConfig = [
                'users'       => ['label' => 'Usuarios',             'icon' => 'tabler-users'],
                'roles'       => ['label' => 'Roles',                 'icon' => 'tabler-shield-lock'],
                'settings'    => ['label' => 'Configuración',         'icon' => 'tabler-settings'],
                'activitylog' => ['label' => 'Registro de Actividad', 'icon' => 'tabler-list-check'],
                'dashboard'   => ['label' => 'Dashboard',             'icon' => 'tabler-layout-dashboard'],
              ];
            @endphp
            <div class="table-responsive">
              <table class="table table-flush-spacing">
                <tbody>
                  <tr class="table-active">
                    <td class="fw-semibold">
                      <i class="icon-base ti tabler-shield-check icon-xs me-1 text-primary"></i>
                      Acceso Completo
                      <i class="icon-base ti tabler-info-circle icon-xs ms-1 text-muted"
                         data-bs-toggle="tooltip" data-bs-placement="top"
                         title="Selecciona todos los permisos disponibles"></i>
                    </td>
                    <td>
                      <div class="d-flex justify-content-end">
                        <div class="form-check mb-0">
                          <input class="form-check-input" type="checkbox" id="selectAll" />
                          <label class="form-check-label fw-medium" for="selectAll">Seleccionar Todo</label>
                        </div>
                      </div>
                    </td>
                  </tr>
                  @foreach ($permissionsGrouped as $module => $perms)
                    @php $cfg = $moduleConfig[$module] ?? ['label' => ucfirst($module), 'icon' => 'tabler-circle']; @endphp
                    <tr>
                      <td class="text-nowrap align-middle" style="width:230px">
                        <div class="d-flex align-items-center gap-2">
                          <div class="avatar avatar-xs">
                            <span class="avatar-initial rounded bg-label-primary">
                              <i class="icon-base ti {{ $cfg['icon'] }} icon-xs"></i>
                            </span>
                          </div>
                          <div>
                            <span class="fw-medium text-heading d-block">{{ $cfg['label'] }}</span>
                            <small class="text-muted">{{ $perms->count() }} {{ $perms->count() === 1 ? 'permiso' : 'permisos' }}</small>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex justify-content-end flex-wrap gap-2">
                          @foreach ($perms as $permission)
                            <div class="form-check mb-0">
                              <input class="form-check-input perm-checkbox"
                                     type="checkbox"
                                     name="permissions[]"
                                     value="{{ $permission->name }}"
                                     id="perm-{{ $permission->id }}" />
                              <label class="form-check-label small" for="perm-{{ $permission->id }}">
                                {{ $permission->label ?? $permission->name }}
                              </label>
                            </div>
                          @endforeach
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-primary me-sm-4 me-1">Guardar</button>
            <button type="reset" class="btn btn-label-secondary"
                    data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@section('admin-page-script')
<script>
'use strict';

document.addEventListener('DOMContentLoaded', function () {

  // ── DataTable asignación de roles ────────────────────────────────────────
  const dtRoleUsers = document.querySelector('.datatables-role-users');
  if (!dtRoleUsers) return;

  const assignableRoles = @json($assignableRoles);

  const roleIconMap = {
    'Super-Admin': 'tabler-crown text-primary',
    'admin':       'tabler-shield text-warning',
    'editor':      'tabler-pencil text-info',
    'user':        'tabler-user text-success',
  };

  const dt = new DataTable(dtRoleUsers, {
    processing: true,
    serverSide: true,
    ajax: {
      url: '{{ route('admin.roles.users.data') }}',
      data: d => {
        d.role   = document.getElementById('filterRoleTable')?.value   ?? '';
        d.status = document.getElementById('filterStatusTable')?.value ?? '';
      },
      dataSrc: json => {
        json.recordsTotal    = json.recordsTotal    ?? 0;
        json.recordsFiltered = json.recordsFiltered ?? 0;
        json.data            = Array.isArray(json.data) ? json.data : [];
        return json.data;
      }
    },
    columns: [
      { data: null,           orderable: false, searchable: false },
      { data: 'name' },
      { data: 'role',         orderable: false, searchable: false },
      { data: 'last_login_at',orderable: false, searchable: false },
      { data: 'status_label', orderable: false, searchable: false },
      { data: null,           orderable: false, searchable: false },
    ],
    columnDefs: [
      {
        className: 'control',
        orderable: false,
        searchable: false,
        responsivePriority: 4,
        targets: 0,
        render: () => ''
      },
      {
        targets: 1,
        responsivePriority: 2,
        render: (d, t, full) => {
          const url = full.avatar_url;
          const ini = (full.name.match(/\b\w/g) || []).slice(0,2).join('').toUpperCase();
          const cols = ['primary','success','danger','warning','info'];
          const col  = cols[full.name.charCodeAt(0) % cols.length];
          const avatar = url
            ? `<img src="${url}" alt="${full.name}" class="rounded-circle" width="34" height="34">`
            : `<span class="avatar-initial rounded-circle bg-label-${col}">${ini}</span>`;
          const sub = full.cargo || full.email;
          return `<div class="d-flex align-items-center gap-3">
            <div class="avatar avatar-sm">${avatar}</div>
            <div class="d-flex flex-column">
              <span class="fw-medium text-heading">${full.name}</span>
              <small class="text-muted">${sub}</small>
            </div>
          </div>`;
        }
      },
      {
        targets: 2,
        responsivePriority: 1,
        render: (d, t, full) => {
          const isSuperAdmin = full.role === 'Super-Admin';
          if (isSuperAdmin) {
            const ico = roleIconMap[full.role] ?? 'tabler-circle text-secondary';
            return `<span class="d-flex align-items-center gap-2">
              <i class="icon-base ti ${ico} icon-18px"></i>${full.role}
            </span>`;
          }
          @can('users.edit')
          const options = assignableRoles.map(r =>
            `<option value="${r}" ${r === full.role ? 'selected' : ''}>${r}</option>`
          ).join('');
          return `<select class="form-select form-select-sm role-inline-select"
                    style="min-width:130px;max-width:180px"
                    data-assign-url="${full.assign_url}"
                    data-user-name="${(full.name).replace(/"/g,'&quot;')}"
                    data-current-role="${full.role}">
                    ${options}
                  </select>`;
          @else
          const ico2 = roleIconMap[full.role] ?? 'tabler-circle text-secondary';
          return `<span class="d-flex align-items-center gap-2">
            <i class="icon-base ti ${ico2} icon-18px"></i>${full.role}
          </span>`;
          @endcan
        }
      },
      {
        targets: 3,
        responsivePriority: 5,
        render: (d, t, full) => full.last_login_at
          ? `<span class="small text-muted">${full.last_login_at}</span>`
          : '<span class="text-muted fst-italic small">Nunca</span>'
      },
      {
        targets: 4,
        responsivePriority: 3,
        render: (d, t, full) => full.status
          ? `<span class="badge ${full.status_class}">${full.status_label}</span>`
          : '—'
      },
      {
        targets: -1,
        responsivePriority: 1,
        orderable: false,
        searchable: false,
        render: (d, t, full) => {
          const name = (full.name || '').replace(/"/g, '&quot;');
          return `<div class="d-flex align-items-center gap-1">
            ${full.show_url
              ? `<a href="${full.show_url}" class="btn btn-sm btn-icon" title="Ver perfil">
                   <i class="icon-base ti tabler-eye icon-22px"></i>
                 </a>`
              : ''}
            <button type="button" class="btn btn-sm btn-icon" title="Ver historial de rol"
              data-history-url="${full.history_url}"
              data-user-name="${name}">
              <i class="icon-base ti tabler-history icon-22px text-secondary"></i>
            </button>
          </div>`;
        }
      }
    ],
    order: [[1, 'asc']],
    layout: {
      topStart: {
        rowClass: 'row my-md-0 me-3 ms-0 justify-content-between',
        features: [{ pageLength: { menu: [10, 25, 50], text: '_MENU_' } }]
      },
      topEnd: {
        features: [
          { search: { placeholder: 'Buscar usuario…', text: '_INPUT_' } },
          {
            buttons: [{
              extend: 'collection',
              className: 'btn btn-label-secondary dropdown-toggle ms-2',
              text: '<i class="icon-base ti tabler-upload me-1 icon-sm"></i><span class="d-none d-sm-inline-block">Exportar</span>',
              buttons: [
                {
                  extend: 'print', title: 'Asignación de Roles',
                  text: '<i class="icon-base ti tabler-printer me-2"></i>Imprimir',
                  className: 'dropdown-item',
                  exportOptions: { columns: [1, 2, 3, 4], format: { body: exportBody } },
                  customize: win => {
                    win.document.body.style.color = config.colors.headingColor;
                    win.document.body.style.backgroundColor = config.colors.bodyBg;
                    const t = win.document.body.querySelector('table');
                    if (t) { t.classList.add('compact'); t.style.color = 'inherit'; }
                  }
                },
                {
                  extend: 'csv', title: 'Asignación de Roles',
                  text: '<i class="icon-base ti tabler-file-text me-2"></i>CSV',
                  className: 'dropdown-item',
                  exportOptions: { columns: [1, 2, 3, 4], format: { body: exportBody } }
                },
                {
                  extend: 'excel', title: 'Asignación de Roles',
                  text: '<i class="icon-base ti tabler-file-spreadsheet me-2"></i>Excel',
                  className: 'dropdown-item',
                  exportOptions: { columns: [1, 2, 3, 4], format: { body: exportBody } }
                },
                {
                  extend: 'pdf', title: 'Asignación de Roles',
                  text: '<i class="icon-base ti tabler-file-code-2 me-2"></i>PDF',
                  className: 'dropdown-item',
                  exportOptions: { columns: [1, 2, 3, 4], format: { body: exportBody } }
                },
                {
                  extend: 'copy', title: 'Asignación de Roles',
                  text: '<i class="icon-base ti tabler-copy me-2"></i>Copiar',
                  className: 'dropdown-item',
                  exportOptions: { columns: [1, 2, 3, 4], format: { body: exportBody } }
                },
              ]
            }]
          }
        ]
      },
      bottomStart: {
        rowClass: 'row mx-3 justify-content-between',
        features: [{ info: { text: 'Mostrando _START_ a _END_ de _TOTAL_ usuarios' } }]
      },
      bottomEnd: 'paging'
    },
    language: {
      infoEmpty: 'Sin usuarios', zeroRecords: 'Sin resultados',
      paginate: {
        next:     '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>'
      }
    },
    responsive: {
      details: {
        display: DataTable.Responsive.display.modal({ header: row => row.data().name }),
        type: 'column',
        renderer: (api, rowIdx, columns) => {
          const data = columns.filter(c => c.title !== '')
            .map(c => `<tr><td>${c.title}:</td><td>${c.data}</td></tr>`).join('');
          if (!data) return false;
          const div = document.createElement('div');
          div.classList.add('table-responsive');
          div.innerHTML = `<table class="table"><tbody>${data}</tbody></table>`;
          return div;
        }
      }
    },
    initComplete: () => document.querySelectorAll('.dt-buttons .btn').forEach(b => b.classList.remove('btn-secondary'))
  });

  // ── Helper exportBody ─────────────────────────────────────────────────────
  function exportBody(inner) {
    if (!inner || inner.indexOf('<') === -1) return inner;
    const doc = new DOMParser().parseFromString(inner, 'text/html');
    const sel = doc.querySelector('select');
    if (sel) return sel.value;
    return (doc.querySelector('.fw-medium') || doc.body)?.textContent?.trim() ?? inner;
  }

  // ── Filtros ───────────────────────────────────────────────────────────────
  document.getElementById('filterRoleTable')?.addEventListener('change',   () => dt.ajax.reload());
  document.getElementById('filterStatusTable')?.addEventListener('change', () => dt.ajax.reload());

  // ── Select inline de rol ──────────────────────────────────────────────────
  dtRoleUsers.addEventListener('change', function (e) {
    const sel = e.target.closest('.role-inline-select');
    if (!sel) return;
    const newRole    = sel.value;
    const prevRole   = sel.dataset.currentRole;
    const userName   = sel.dataset.userNameDecoded || sel.getAttribute('data-user-name');
    if (newRole === prevRole) return;

    confirmAction({
      title      : '¿Cambiar rol?',
      text       : `Se cambiará el rol de "${userName}" de "${prevRole}" a "${newRole}".`,
      confirmText: 'Sí, cambiar',
      cancelText : 'Cancelar',
      isDanger   : false,
      onConfirm  : () => {
        fetch(sel.dataset.assignUrl, {
          method : 'PATCH',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
          body   : JSON.stringify({ role: newRole }),
        })
        .then(r => r.json())
        .then(d => {
          showToast('success', d.message ?? 'Rol actualizado.');
          sel.dataset.currentRole = newRole;
          dt.ajax.reload(null, false);
        })
        .catch(() => {
          showToast('error', 'No se pudo cambiar el rol.');
          sel.value = prevRole;
        });
      },
      onCancel: () => { sel.value = prevRole; }
    });
  });

  // ── Historial de rol ──────────────────────────────────────────────────────
  const historyModal   = new bootstrap.Modal(document.getElementById('roleHistoryModal'));
  const historyName    = document.getElementById('historyUserName');
  const historyLoading = document.getElementById('historyLoading');
  const historyEmpty   = document.getElementById('historyEmpty');
  const historyWrap    = document.getElementById('historyTableWrap');
  const historyBody    = document.getElementById('historyTableBody');

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-history-url]');
    if (!btn) return;

    historyName.textContent = btn.dataset.userName;
    historyLoading.classList.remove('d-none');
    historyEmpty.classList.add('d-none');
    historyWrap.classList.add('d-none');
    historyBody.innerHTML = '';
    historyModal.show();

    fetch(btn.dataset.historyUrl, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
      .then(r => r.json())
      .then(d => {
        historyLoading.classList.add('d-none');
        if (!d.history || d.history.length === 0) {
          historyEmpty.classList.remove('d-none');
          return;
        }
        historyBody.innerHTML = d.history.map(h => `
          <tr>
            <td class="small text-nowrap">${h.fecha}</td>
            <td><span class="badge bg-label-secondary">${h.old_role}</span></td>
            <td><i class="icon-base ti tabler-arrow-right icon-14px text-muted"></i></td>
            <td><span class="badge bg-label-primary">${h.new_role}</span></td>
            <td class="small text-muted">${h.por}</td>
          </tr>`).join('');
        historyWrap.classList.remove('d-none');
      })
      .catch(() => {
        historyLoading.classList.add('d-none');
        historyEmpty.classList.remove('d-none');
      });
  });

  // ── Ajustar clases DataTable ──────────────────────────────────────────────
  setTimeout(() => {
    [
      { s: '.dt-buttons .btn',         r: 'btn-secondary' },
      { s: '.dt-search .form-control', r: 'form-control-sm' },
      { s: '.dt-length .form-select',  r: 'form-select-sm', a: 'ms-0' },
      { s: '.dt-length',               a: 'mb-md-6 mb-0' },
      { s: '.dt-layout-end',           r: 'justify-content-between', a: 'd-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap' },
      { s: '.dt-layout-table',         r: 'row mt-2' },
      { s: '.dt-layout-full',          r: 'col-md col-12', a: 'table-responsive' }
    ].forEach(({ s, r, a }) => {
      document.querySelectorAll(s).forEach(el => {
        if (r) r.split(' ').forEach(c => el.classList.remove(c));
        if (a) a.split(' ').forEach(c => el.classList.add(c));
      });
    });
  }, 100);

  // ── Modal lógica ────────────────────────────────────────────────────────────
  const selectAll   = document.getElementById('selectAll');
  const permBoxes   = document.querySelectorAll('.perm-checkbox');
  const roleForm    = document.getElementById('roleForm');
  const roleTitle   = document.querySelector('.role-title');
  const nameInput   = document.getElementById('modalRoleName');
  const descInput   = document.getElementById('modalRoleDescription');
  const nameAlert   = document.getElementById('roleNameAlert');
  const addNewBtn   = document.querySelector('.add-new-role');

  const syncSelectAll = () => {
    if (!selectAll) return;
    selectAll.checked = Array.from(permBoxes).every(c => c.checked);
    selectAll.indeterminate = !selectAll.checked && Array.from(permBoxes).some(c => c.checked);
  };

  if (selectAll) {
    selectAll.addEventListener('change', () => permBoxes.forEach(cb => cb.checked = selectAll.checked));
    permBoxes.forEach(cb => cb.addEventListener('change', syncSelectAll));
  }

  if (addNewBtn) {
    addNewBtn.addEventListener('click', () => {
      roleTitle.textContent = 'Agregar Nuevo Rol';
      document.getElementById('formMethod').value = 'POST';
      roleForm.action = '{{ route('admin.roles.store') }}';
      nameInput.value = '';
      if (descInput) descInput.value = '';
      nameInput.removeAttribute('readonly');
      nameAlert.classList.add('d-none');
      permBoxes.forEach(cb => cb.checked = false);
      syncSelectAll();
    });
  }

  document.querySelectorAll('.role-edit-modal').forEach(link => {
    link.addEventListener('click', function () {
      const rolePerms = JSON.parse(this.dataset.rolePermissions || '[]');
      const isSystem  = this.dataset.systemRole === '1';

      roleTitle.textContent = 'Editar Rol';
      document.getElementById('formMethod').value = 'PUT';
      roleForm.action = `/admin/roles/${this.dataset.roleId}`;
      nameInput.value = this.dataset.roleName;
      if (descInput) descInput.value = this.dataset.roleDescription || '';

      if (isSystem) {
        nameInput.setAttribute('readonly', true);
        nameAlert.classList.remove('d-none');
      } else {
        nameInput.removeAttribute('readonly');
        nameAlert.classList.add('d-none');
      }

      permBoxes.forEach(cb => cb.checked = rolePerms.includes(cb.value));
      syncSelectAll();
    });
  });

  // Clonar rol — abre modal con nombre prefijado
  document.querySelectorAll('.clone-role').forEach(btn => {
    btn.addEventListener('click', function () {
      roleTitle.textContent = 'Copiar Rol';
      document.getElementById('formMethod').value = 'POST';
      roleForm.action = '{{ route('admin.roles.store') }}';
      nameInput.value = 'Copia de ' + this.dataset.roleName;
      if (descInput) descInput.value = '';
      nameInput.removeAttribute('readonly');
      nameAlert.classList.add('d-none');
      permBoxes.forEach(cb => cb.checked = false);
      syncSelectAll();
      const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('addRoleModal'));
      modal.show();
    });
  });

});

// ── Exportación profesional roles — scope global para onclick ────────────
function exportRolesProfesional(formato) {
  const params = new URLSearchParams({
    role:   document.getElementById('filterRoleTable')?.value   ?? '',
    status: document.getElementById('filterStatusTable')?.value ?? '',
  });
  for (const [k, v] of [...params]) { if (!v) params.delete(k); }

  const urls = {
    pdf  : '{{ route("admin.roles.export.pdf") }}',
    excel: '{{ route("admin.roles.export.excel") }}',
    csv  : '{{ route("admin.roles.export.csv") }}',
  };
  showToast('info', `Preparando ${formato.toUpperCase()}… se descargará en breve.`);
  window.open(urls[formato] + (params.toString() ? '?' + params.toString() : ''), '_blank');
}
</script>
@endsection
