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

{{-- ─── Buscador de tarjetas ───────────────────────────────────────────────── --}}
<div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
  <div class="input-group input-group-merge" style="max-width:280px">
    <span class="input-group-text"><i class="icon-base ti tabler-search icon-sm"></i></span>
    <input type="text" id="roleCardSearch" class="form-control" placeholder="Buscar rol…" autocomplete="off" />
  </div>
  <small id="roleCardCount" class="text-muted"></small>
</div>

{{-- ─── Role cards ─────────────────────────────────────────────────────────── --}}
<div class="row g-6" id="roleCardsContainer">

  @foreach ($roles as $role)
    @php $isSuperAdmin = $role->name === 'Super-Admin'; @endphp
    <div class="col-xl-4 col-lg-6 col-md-6 role-card-item" data-role-name="{{ strtolower($role->name) }}">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex flex-column gap-1">
              <h6 class="fw-normal mb-0 text-body">
                Total {{ $role->users_count }} {{ $role->users_count === 1 ? 'usuario' : 'usuarios' }}
              </h6>
              <span class="text-muted small">
                <i class="icon-base ti tabler-key icon-xs me-1"></i>
                {{ $role->permissions->count() }} {{ $role->permissions->count() === 1 ? 'permiso' : 'permisos' }}
              </span>
            </div>
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
                        $col    = $colors[mb_ord(mb_strtoupper(mb_substr($u->name, 0, 1)), 'UTF-8') % count($colors)];
                        $ini    = implode('', array_map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1), 'UTF-8'), array_slice(explode(' ', $u->name), 0, 2)));
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
                <a href="javascript:void(0);" class="role-detail-link d-block mt-1 small"
                   data-role-name="{{ $role->name }}"
                   data-role-perms="[]"
                   data-role-is-super-admin="1"
                   data-role-users="{{ $role->users->take(30)->pluck('name')->toJson() }}"
                   data-role-user-count="{{ $role->users_count }}">
                  Ver detalle
                </a>
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
                <a href="javascript:void(0);" class="role-detail-link d-block mt-1 small"
                   data-role-name="{{ $role->name }}"
                   data-role-perms="{{ $role->permissions->map(fn($p) => ['name'=>$p->name,'label'=>$p->label??$p->name])->toJson() }}"
                   data-role-users="{{ $role->users->take(30)->pluck('name')->toJson() }}"
                   data-role-user-count="{{ $role->users_count }}">
                  Ver detalle
                </a>
              @endif
            </div>

            <div class="d-flex align-items-center gap-1">
              {{-- Historial de cambios del rol --}}
              <a href="javascript:void(0);"
                 class="btn btn-icon btn-text-secondary rounded-pill waves-effect role-change-history"
                 data-history-url="{{ route('admin.roles.change-history', $role) }}"
                 data-role-name="{{ $role->name }}"
                 data-bs-toggle="tooltip" title="Historial de cambios del rol">
                <i class="icon-base ti tabler-clock-record icon-md"></i>
              </a>

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
          </div>
        </div>
      </div>
      <div class="card-datatable">
        <table class="datatables-role-users table border-top">
          <thead>
            <tr>
              <th></th>
              <th><input type="checkbox" id="checkAllUsers" class="form-check-input mt-0" title="Seleccionar todos"></th>
              <th>Usuario</th>
              <th>Rol actual</th>
              <th>Último acceso</th>
              <th>Estado</th>
              <th>Registrado</th>
              <th>Acciones</th>
            </tr>
          </thead>
        </table>
        {{-- Barra bulk assign --}}
        <div id="bulkAssignBar" class="d-none border-top px-4 py-3 bg-light d-flex align-items-center gap-3 flex-wrap">
          <span class="fw-semibold text-primary"><span id="bulkCount">0</span> seleccionado(s)</span>
          <div class="d-flex align-items-center gap-2">
            <label class="mb-0 small fw-medium">Asignar rol:</label>
            <select id="bulkRoleSelect" class="form-select form-select-sm" style="min-width:150px">
              <option value="">— Seleccionar —</option>
              @foreach ($assignableRoles as $rName)
                <option value="{{ $rName }}">{{ $rName }}</option>
              @endforeach
            </select>
            <button id="bulkAssignBtn" class="btn btn-sm btn-primary" disabled>
              <i class="icon-base ti tabler-users-group me-1 icon-sm"></i>Aplicar
            </button>
            <span id="bulkRoleHint"></span>
          </div>
          <button id="bulkClearBtn" class="btn btn-sm btn-outline-secondary ms-auto">
            <i class="icon-base ti tabler-x me-1 icon-sm"></i>Cancelar
          </button>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- ─── Leyenda ─────────────────────────────────────────────────────────────── --}}
@php
$legendItems = [
  ['icon' => 'tabler-checkbox',    'color' => 'primary',   'text' => 'Marca el <strong class="text-body">checkbox</strong> de cada fila para seleccionar varios usuarios y asignarles un rol de forma masiva en un solo paso.'],
  ['icon' => 'tabler-user-edit',   'color' => 'warning',   'text' => 'Usa el selector <strong class="text-body">Rol actual</strong> en cada fila para cambiar el rol de forma individual sin salir de esta vista.'],
  ['icon' => 'tabler-history',     'color' => 'info',      'text' => 'El ícono de <strong class="text-body">historial</strong> muestra todos los cambios de rol anteriores del usuario con fecha y responsable.'],
  ['icon' => 'tabler-shield-lock', 'color' => 'secondary', 'text' => 'Los usuarios con rol <strong class="text-body">Super-Admin</strong> no pueden ser modificados ni reasignados desde esta vista.'],
];
@endphp
<x-table-legend :items="$legendItems" />

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

{{-- ─── Modal Historial de cambios del ROL ────────────────────────────────── --}}
<div class="modal fade" id="roleChangeHistoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="icon-base ti tabler-clock-record icon-18px me-2 text-primary"></i>
          Historial de cambios — <span id="roleChangeHistoryName"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <div id="roleChangeHistoryLoading" class="text-center py-5 d-none">
          <div class="spinner-border text-primary" role="status"></div>
        </div>
        <div id="roleChangeHistoryEmpty" class="text-center py-5 text-muted d-none">
          <i class="icon-base ti tabler-mood-empty icon-40px mb-2 d-block"></i>
          Sin cambios registrados aún.
        </div>
        <div class="table-responsive" id="roleChangeHistoryWrap">
          <table class="table table-sm mb-0">
            <thead class="table-light">
              <tr>
                <th>Fecha</th>
                <th>Nombre</th>
                <th>Permisos agregados</th>
                <th>Permisos quitados</th>
                <th>Por</th>
              </tr>
            </thead>
            <tbody id="roleChangeHistoryBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ─── Modal Detalle de rol ───────────────────────────────────────────────── --}}
<div class="modal fade" id="roleDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="icon-base ti tabler-shield icon-18px me-2 text-primary"></i>
          Detalle del rol — <span id="roleDetailName"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-4">
          <div class="col-md-6">
            <h6 class="fw-semibold mb-3"><i class="icon-base ti tabler-key icon-sm me-1 text-primary"></i>Permisos asignados</h6>
            <div id="roleDetailPerms" class="d-flex flex-wrap gap-2"></div>
          </div>
          <div class="col-md-6">
            <h6 class="fw-semibold mb-3"><i class="icon-base ti tabler-users icon-sm me-1 text-primary"></i>Usuarios con este rol</h6>
            <div id="roleDetailUsers" class="d-flex flex-wrap gap-2"></div>
          </div>
        </div>
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

  const defaultPerPage = @json(config('app-settings.pagination.default', 15));

  const dt = new DataTable(dtRoleUsers, {
    pageLength: defaultPerPage,
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
      { data: null,           orderable: false, searchable: false },
      { data: 'name' },
      { data: 'role',         orderable: false, searchable: false },
      { data: 'last_login_at',orderable: false, searchable: false },
      { data: 'status_label', orderable: false, searchable: false },
      { data: 'created_at',   orderable: false, searchable: false },
      { data: null,           orderable: false, searchable: false },
    ],
    columnDefs: [
      // Col 0: responsive control
      { className: 'control', orderable: false, searchable: false, responsivePriority: 4, targets: 0, render: () => '' },
      // Col 1: checkbox bulk
      {
        targets: 1,
        orderable: false,
        searchable: false,
        responsivePriority: 6,
        render: (d, t, full) =>
          `<input type="checkbox" class="form-check-input row-checkbox mt-0" data-user-id="${full.id}" data-current-role="${full.role ?? ''}">`
      },
      // Col 2: usuario
      {
        targets: 2,
        responsivePriority: 2,
        render: (d, t, full) => {
          const url = full.avatar_url;
          const ini = full.name.trim().split(/\s+/).slice(0,2).map(w => [...w][0] ?? '').join('').toUpperCase();
          const cols = ['primary','success','danger','warning','info'];
          const col  = cols[([...full.name][0] ?? ' ').codePointAt(0) % cols.length];
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
      // Col 3: rol actual
      {
        targets: 3,
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
      // Col 4: último acceso
      {
        targets: 4,
        responsivePriority: 5,
        render: (d, t, full) => full.last_login_at
          ? `<span class="small text-muted">${full.last_login_at}</span>`
          : '<span class="text-muted fst-italic small">Nunca</span>'
      },
      // Col 5: estado
      {
        targets: 5,
        responsivePriority: 3,
        render: (d, t, full) => full.status
          ? `<span class="badge ${full.status_class}">${full.status_label}</span>`
          : '—'
      },
      // Col 6: registrado
      {
        targets: 6,
        responsivePriority: 7,
        render: (d, t, full) => `<span class="small text-muted">${full.created_at ?? '—'}</span>`
      },
      // Col 7: acciones
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
    order: [[2, 'asc']],
    layout: {
      topStart: {
        rowClass: 'row my-md-0 me-3 ms-0 justify-content-between',
        features: [{ pageLength: { menu: [...new Set([10, 25, 50, 100, defaultPerPage])].sort((a,b)=>a-b), text: '_MENU_' } }]
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
                  text: '<i class="icon-base ti tabler-file-type-pdf me-2 text-danger"></i>PDF',
                  className: 'dropdown-item',
                  action: () => exportRolesProfesional('pdf')
                },
                {
                  text: '<i class="icon-base ti tabler-file-spreadsheet me-2 text-success"></i>Excel',
                  className: 'dropdown-item',
                  action: () => exportRolesProfesional('excel')
                },
                {
                  text: '<i class="icon-base ti tabler-file-text me-2 text-info"></i>CSV',
                  className: 'dropdown-item',
                  action: () => exportRolesProfesional('csv')
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
      info:         'Mostrando _START_–_END_ de _TOTAL_ usuarios',
      infoEmpty:    'No hay usuarios registrados',
      infoFiltered: '(filtrado de _MAX_ usuarios en total)',
      zeroRecords:  'No se encontraron usuarios con estos filtros',
      emptyTable:   'No hay usuarios en el sistema',
      select: {
        rows: { _: '%d usuarios seleccionados', 0: '', 1: '1 usuario seleccionado' }
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
    // Ignorar botones de historial del ROL (tarjetas) — tienen data-role-name, no data-user-name
    if (!btn || btn.dataset.userName === undefined) return;

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

  // ── Historial de cambios del ROL (tarjeta) ───────────────────────────────
  document.querySelectorAll('.role-change-history').forEach(btn => {
    btn.addEventListener('click', function () {
      const url      = this.dataset.historyUrl;
      const roleName = this.dataset.roleName;
      document.getElementById('roleChangeHistoryName').textContent = roleName;

      const loading = document.getElementById('roleChangeHistoryLoading');
      const empty   = document.getElementById('roleChangeHistoryEmpty');
      const wrap    = document.getElementById('roleChangeHistoryWrap');
      const tbody   = document.getElementById('roleChangeHistoryBody');

      loading.classList.remove('d-none');
      empty.classList.add('d-none');
      wrap.classList.add('d-none');
      tbody.innerHTML = '';

      bootstrap.Modal.getOrCreateInstance(document.getElementById('roleChangeHistoryModal')).show();

      fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
          loading.classList.add('d-none');
          if (!data.history || data.history.length === 0) {
            empty.classList.remove('d-none');
            return;
          }
          wrap.classList.remove('d-none');
          tbody.innerHTML = data.history.map(h => {
            const added   = (h.permissions_added   || []).map(p => `<span class="badge bg-label-success me-1">${p}</span>`).join('') || '—';
            const removed = (h.permissions_removed || []).map(p => `<span class="badge bg-label-danger me-1">${p}</span>`).join('')  || '—';
            const nameChange = h.old_name !== h.new_name
              ? `<span class="text-muted text-decoration-line-through me-1">${h.old_name}</span> → <strong>${h.new_name}</strong>`
              : `<span class="text-muted">Sin cambio</span>`;
            return `<tr>
              <td><small class="text-muted">${h.fecha}</small></td>
              <td>${nameChange}</td>
              <td><div class="d-flex flex-wrap gap-1">${added}</div></td>
              <td><div class="d-flex flex-wrap gap-1">${removed}</div></td>
              <td><small>${h.por}</small></td>
            </tr>`;
          }).join('');
        })
        .catch(() => { loading.classList.add('d-none'); empty.classList.remove('d-none'); });
    });
  });

  // ── Bulk assign ──────────────────────────────────────────────────────────
  const checkAll      = document.getElementById('checkAllUsers');
  const bulkBar       = document.getElementById('bulkAssignBar');
  const bulkCount     = document.getElementById('bulkCount');
  const bulkRoleSel   = document.getElementById('bulkRoleSelect');
  const bulkAssignBtn = document.getElementById('bulkAssignBtn');
  const bulkClearBtn  = document.getElementById('bulkClearBtn');

  function updateBulkBar() {
    const checked = document.querySelectorAll('.row-checkbox:checked');
    const count   = checked.length;

    bulkBar.classList.toggle('d-none', count === 0);
    if (count === 0) return;

    bulkCount.textContent = count;

    const targetRole    = bulkRoleSel.value;
    const selectedRoles = [...checked].map(cb => cb.dataset.currentRole ?? '');
    const allSameRole   = targetRole && selectedRoles.every(r => r === targetRole);

    // Hint visual cuando el rol ya está asignado a todos
    const hint = bulkBar.querySelector('#bulkRoleHint');
    if (hint) {
      hint.textContent  = allSameRole ? 'Todos ya tienen este rol.' : '';
      hint.className    = allSameRole ? 'text-warning small' : '';
    }

    bulkAssignBtn.disabled = (count === 0 || !targetRole || allSameRole);
  }

  // Checkbox "seleccionar todos" en header (solo los visibles en la página actual)
  if (checkAll) {
    checkAll.addEventListener('change', function () {
      document.querySelectorAll('.row-checkbox').forEach(cb => { cb.checked = this.checked; });
      updateBulkBar();
    });
  }

  // Delegación para checkboxes individuales (se generan dinámicamente por DataTable)
  dtRoleUsers.addEventListener('change', function (e) {
    if (e.target.classList.contains('row-checkbox')) {
      updateBulkBar();
      if (checkAll) {
        const all     = document.querySelectorAll('.row-checkbox');
        const checked = document.querySelectorAll('.row-checkbox:checked');
        checkAll.indeterminate = checked.length > 0 && checked.length < all.length;
        checkAll.checked = checked.length === all.length && all.length > 0;
      }
    }
  });

  bulkRoleSel?.addEventListener('change', updateBulkBar);

  bulkClearBtn?.addEventListener('click', () => {
    document.querySelectorAll('.row-checkbox').forEach(cb => { cb.checked = false; });
    if (checkAll) { checkAll.checked = false; checkAll.indeterminate = false; }
    bulkBar.classList.add('d-none');
  });

  bulkAssignBtn?.addEventListener('click', () => {
    const ids  = [...document.querySelectorAll('.row-checkbox:checked')].map(cb => cb.dataset.userId);
    const role = bulkRoleSel.value;
    if (!ids.length || !role) return;

    confirmAction({
      title:       '¿Asignar rol masivo?',
      html:        `Se asignará el rol <strong>${role}</strong> a ${ids.length} usuario(s). Los Super-Admin serán omitidos.`,
      confirmText: 'Sí, asignar',
      isDanger:    false,
      onConfirm: () => {
        fetch('{{ route("admin.roles.users.bulk-assign") }}', {
          method:  'POST',
          headers: {
            'Content-Type':     'application/json',
            'Accept':           'application/json',
            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify({ user_ids: ids.map(Number), role }),
        })
        .then(r => r.json())
        .then(data => {
          showToast('success', data.message);
          dt.ajax.reload(null, false);
          bulkClearBtn.click();
        })
        .catch(() => showToast('error', 'Error al asignar roles. Intenta nuevamente.'));
      }
    });
  });

  // Limpiar checkboxes al recargar DataTable
  dt.on('draw', () => {
    if (checkAll) { checkAll.checked = false; checkAll.indeterminate = false; }
    bulkBar.classList.add('d-none');
  });

  // ── Detalle de rol ───────────────────────────────────────────────────────
  document.querySelectorAll('.role-detail-link').forEach(link => {
    link.addEventListener('click', function () {
      const name      = this.dataset.roleName;
      const perms     = JSON.parse(this.dataset.rolePerms  || '[]');
      const users     = JSON.parse(this.dataset.roleUsers  || '[]');
      const userCount = parseInt(this.dataset.roleUserCount || 0);

      document.getElementById('roleDetailName').textContent = name;

      const isSuperAdmin = this.dataset.roleIsSuperAdmin === '1';
      const permsEl = document.getElementById('roleDetailPerms');
      if (isSuperAdmin) {
        permsEl.innerHTML = `<div class="alert alert-primary py-2 mb-0 w-100">
          <i class="icon-base ti tabler-crown me-2"></i>
          <strong>Acceso total al sistema.</strong> El Super-Admin tiene todos los permisos implícitamente, sin restricciones.
        </div>`;
      } else {
        if (perms.length) {
          // Agrupar por módulo (prefijo antes del punto)
          const groups = {};
          perms.forEach(p => {
            const mod = (p.name || p).split('.')[0];
            if (!groups[mod]) groups[mod] = [];
            groups[mod].push(p);
          });
          permsEl.innerHTML = Object.entries(groups).map(([mod, list]) =>
            `<div class="w-100 mb-2">
              <small class="text-uppercase fw-bold text-muted d-block mb-1" style="letter-spacing:.8px;font-size:.65rem">${mod}</small>
              <div class="d-flex flex-wrap gap-1">
                ${list.map(p => `<span class="badge bg-label-primary" title="${p.name || p}">${p.label || p.name || p}</span>`).join('')}
              </div>
            </div>`
          ).join('');
        } else {
          permsEl.innerHTML = '<span class="text-muted small">Sin permisos asignados</span>';
        }
      }

      const usersEl = document.getElementById('roleDetailUsers');
      let usersHtml = users.map(u => `<span class="badge bg-label-secondary">${u}</span>`).join(' ');
      if (userCount > users.length) {
        usersHtml += ` <span class="badge bg-label-dark">+${userCount - users.length} más</span>`;
      }
      usersEl.innerHTML = usersHtml || '<span class="text-muted small">Sin usuarios asignados</span>';

      bootstrap.Modal.getOrCreateInstance(document.getElementById('roleDetailModal')).show();
    });
  });

  // ── Filtro de tarjetas ───────────────────────────────────────────────────
  const cardSearch  = document.getElementById('roleCardSearch');
  const cardItems   = document.querySelectorAll('.role-card-item');
  const cardCounter = document.getElementById('roleCardCount');

  function filterCards() {
    const q = (cardSearch?.value ?? '').toLowerCase().trim();
    let visible = 0;
    cardItems.forEach(item => {
      const name = item.dataset.roleName ?? '';
      const show = !q || name.includes(q);
      item.style.display = show ? '' : 'none';
      if (show) visible++;
    });
    if (cardCounter) {
      cardCounter.textContent = q ? `${visible} de ${cardItems.length} roles` : '';
    }
  }

  cardSearch?.addEventListener('input', filterCards);

});

// ── Exportación usuarios de la tabla (respeta filtros rol + estado) ──────
function exportRolesProfesional(formato) {
  const params = new URLSearchParams({
    role:   document.getElementById('filterRoleTable')?.value   ?? '',
    status: document.getElementById('filterStatusTable')?.value ?? '',
    search: document.querySelector('.dt-search input')?.value?.trim() ?? '',
  });
  for (const [k, v] of [...params]) { if (!v) params.delete(k); }

  const urls = {
    pdf  : '{{ route("admin.users.export.pdf") }}',
    excel: '{{ route("admin.users.export.excel") }}',
    csv  : '{{ route("admin.users.export.csv") }}',
  };
  showToast('info', `Preparando ${formato.toUpperCase()}… se descargará en breve.`);
  window.open(urls[formato] + (params.toString() ? '?' + params.toString() : ''), '_blank');
}
</script>
@endsection
