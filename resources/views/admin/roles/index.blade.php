@extends('admin/layouts/master')

@section('title', 'Roles y Permisos')

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

<x-breadcrumb :items="[['label' => 'Roles y Permisos']]" />

<h4 class="mb-1">Lista de Roles</h4>
<p class="mb-6">
  Un rol otorga acceso a menús y funciones predefinidas. Según el rol asignado,<br>
  el usuario tendrá acceso solo a lo que necesita.
</p>

{{-- ─── Role cards (copiado de app-access-roles.blade.php) ──────────────── --}}
<div class="row g-6">

  @foreach ($roles as $role)
    <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="fw-normal mb-0 text-body">
              Total {{ $role->users_count }} {{ $role->users_count === 1 ? 'usuario' : 'usuarios' }}
            </h6>
            {{-- Avatar group de los primeros usuarios del rol --}}
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
                        $initials = strtoupper(substr($u->name, 0, 1));
                        $colors   = ['primary','success','danger','warning','info'];
                        $color    = $colors[ord($u->name[0]) % count($colors)];
                      @endphp
                      <span class="avatar-initial rounded-circle bg-label-{{ $color }}">{{ $initials }}</span>
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
              @if ($role->name !== 'Super-Admin')
                @can('roles.edit')
                  <a href="javascript:;"
                     data-bs-toggle="modal"
                     data-bs-target="#addRoleModal"
                     class="role-edit-modal"
                     data-role-id="{{ $role->id }}"
                     data-role-name="{{ $role->name }}"
                     data-role-permissions="{{ $role->permissions->pluck('name')->toJson() }}"
                     data-system-role="{{ in_array($role->name, ['admin', 'user', 'editor']) ? '1' : '0' }}">
                    <span>Editar Rol</span>
                  </a>
                @endcan
              @else
                <span class="text-muted small">Acceso total al sistema</span>
              @endif
            </div>

            @can('roles.delete')
              @if (
                $role->name !== 'Super-Admin' &&
                $role->users_count === 0 &&
                !in_array($role->name, ['admin', 'user', 'editor'])
              )
                <form id="del-role-{{ $role->id }}"
                      action="{{ route('admin.roles.destroy', $role) }}"
                      method="POST" class="d-inline">
                  @csrf @method('DELETE')
                </form>
                <a href="javascript:void(0);"
                   onclick="confirmDelete('del-role-{{ $role->id }}', '{{ addslashes($role->name) }}')">
                  <i class="icon-base ti tabler-copy icon-md text-heading"></i>
                </a>
              @endif
            @endcan
          </div>
        </div>
      </div>
    </div>
  @endforeach

  {{-- Card: Agregar nuevo rol (copiado exacto de app-access-roles.blade.php) --}}
  @can('roles.create')
    <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card h-100">
        <div class="row h-100">
          <div class="col-sm-5">
            <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-4">
              <img src="{{ asset('assets/img/illustrations/add-new-roles.png') }}"
                   class="img-fluid" alt="Agregar rol" width="83" />
            </div>
          </div>
          <div class="col-sm-7">
            <div class="card-body text-sm-end text-center ps-sm-0">
              <button data-bs-target="#addRoleModal"
                      data-bs-toggle="modal"
                      class="btn btn-sm btn-primary mb-4 text-nowrap add-new-role">
                Agregar Nuevo Rol
              </button>
              <p class="mb-0">
                Agrega un nuevo rol,<br>si aún no existe.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endcan

  {{-- ─── Tabla usuarios con roles (copiado de app-access-roles.blade.php) ── --}}
  <div class="col-12">
    <h4 class="mt-6 mb-1">Usuarios y sus roles</h4>
    <p class="mb-0">Encuentra todos los usuarios del sistema y sus roles asignados.</p>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-datatable">
        <table class="datatables-roles table border-top">
          <thead>
            <tr>
              <th></th>
              <th></th>
              <th>Usuario</th>
              <th>Rol</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- ─── Modal Crear / Editar Rol (copiado de modal-add-role.blade.php) ──── --}}
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
            <label class="form-label" for="modalRoleName">Nombre del Rol</label>
            <input type="text" id="modalRoleName" name="name"
                   class="form-control" placeholder="Ingresa un nombre para el rol"
                   maxlength="100" />
            <div id="roleNameAlert" class="alert alert-info py-2 mt-2 d-none">
              <i class="icon-base ti tabler-info-circle me-1"></i>
              El nombre de los roles del sistema no puede modificarse.
            </div>
          </div>

          <div class="col-12">
            <h5 class="mb-1">Permisos del Rol</h5>
            <p class="text-body-secondary small mb-4">Marca los permisos que tendrá este rol.</p>
            @php
              $moduleConfig = [
                'users'       => ['label' => 'Usuarios',               'icon' => 'tabler-users'],
                'roles'       => ['label' => 'Roles',                   'icon' => 'tabler-shield-lock'],
                'settings'    => ['label' => 'Configuración',           'icon' => 'tabler-settings'],
                'activitylog' => ['label' => 'Registro de Actividad',   'icon' => 'tabler-list-check'],
                'dashboard'   => ['label' => 'Dashboard',               'icon' => 'tabler-layout-dashboard'],
              ];
            @endphp
            <div class="table-responsive">
              <table class="table table-flush-spacing">
                <tbody>
                  {{-- Fila: Seleccionar Todo --}}
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
                  {{-- Filas por módulo --}}
                  @foreach ($permissionsGrouped as $module => $perms)
                    @php
                      $cfg   = $moduleConfig[$module] ?? ['label' => ucfirst($module), 'icon' => 'tabler-circle'];
                      $ids   = $perms->pluck('id')->map(fn($id) => "perm-$id")->implode(' ');
                    @endphp
                    <tr>
                      <td class="text-nowrap align-middle" style="width:220px">
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
                              <label class="form-check-label small"
                                     for="perm-{{ $permission->id }}">
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
                    data-bs-dismiss="modal" aria-label="Close">
              Cancelar
            </button>
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

  // ── DataTable usuarios con roles ──────────────────────────────────────────
  const dtRolesTable = document.querySelector('.datatables-roles');
  if (dtRolesTable) {
    const dt = new DataTable(dtRolesTable, {
      ajax: {
        url: '{{ route('admin.users.data') }}',
        dataSrc: 'data'
      },
      columns: [
        { data: null },
        { data: null, orderable: false, searchable: false },
        { data: 'name' },
        { data: 'role' },
        { data: 'status_label' },
        { data: null, orderable: false, searchable: false }
      ],
      columnDefs: [
        {
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 5,
          targets: 0,
          render: () => ''
        },
        {
          targets: 1,
          orderable: false,
          searchable: false,
          responsivePriority: 3,
          render: () => '<input type="checkbox" class="dt-checkboxes form-check-input">'
        },
        {
          targets: 2,
          responsivePriority: 1,
          render: function (data, type, full) {
            const name  = full.name;
            const email = full.email;
            const url   = full.avatar_url;
            let avatar;
            if (url) {
              avatar = `<img src="${url}" alt="${name}" class="rounded-circle">`;
            } else {
              const initials = (name.match(/\b\w/g) || []).slice(0, 2).join('').toUpperCase();
              avatar = `<span class="avatar-initial rounded-circle bg-label-primary">${initials}</span>`;
            }
            return `
              <div class="d-flex justify-content-left align-items-center role-name">
                <div class="avatar-wrapper">
                  <div class="avatar avatar-sm me-3">${avatar}</div>
                </div>
                <div class="d-flex flex-column">
                  <span class="fw-medium text-heading">${name}</span>
                  <small>@${email}</small>
                </div>
              </div>`;
          }
        },
        {
          targets: 3,
          render: (data, type, full) =>
            `<span class="text-truncate d-flex align-items-center">${full.role}</span>`
        },
        {
          targets: 4,
          render: (data, type, full) =>
            full.status
              ? `<span class="badge ${full.status_class}">${full.status_label}</span>`
              : '—'
        },
        {
          targets: -1,
          title: 'Acciones',
          searchable: false,
          orderable: false,
          render: function (data, type, full) {
            if (!full.edit_url) return '';
            return `
              <div class="d-flex align-items-center">
                <a href="${full.edit_url}"
                   class="btn btn-icon btn-text-secondary rounded-pill waves-effect"
                   title="Editar usuario">
                  <i class="icon-base ti tabler-pencil icon-md"></i>
                </a>
              </div>`;
          }
        }
      ],
      select: {
        style: 'multi',
        selector: 'td:nth-child(2)'
      },
      order: [[2, 'asc']],
      layout: {
        topStart: {
          rowClass: 'row my-md-0 me-3 ms-0 justify-content-between',
          features: [{ pageLength: { menu: [10, 25, 50, 100], text: '_MENU_' } }]
        },
        topEnd: {
          features: [{ search: { placeholder: 'Buscar usuario', text: '_INPUT_' } }]
        },
        bottomStart: {
          rowClass: 'row mx-3 justify-content-between',
          features: ['info']
        },
        bottomEnd: 'paging'
      },
      language: {
        info: 'Mostrando _START_ a _END_ de _TOTAL_ usuarios',
        infoEmpty: 'Sin usuarios',
        zeroRecords: 'Sin resultados',
        paginate: {
          next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
          previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>'
        }
      }
    });

    // Ajustar clases (igual que app-access-roles.js)
    setTimeout(() => {
      [
        { s: '.dt-buttons .btn', r: 'btn-secondary' },
        { s: '.dt-buttons.btn-group .btn-group', r: 'btn-group' },
        { s: '.dt-buttons.btn-group', r: 'btn-group', a: 'd-flex' },
        { s: '.dt-search .form-control', r: 'form-control-sm' },
        { s: '.dt-length .form-select', r: 'form-select-sm' },
        { s: '.dt-length', a: 'mb-md-6 mb-0' },
        { s: '.dt-layout-start', a: 'ps-3 mt-0' },
        { s: '.dt-layout-end', r: 'justify-content-between', a: 'justify-content-md-between justify-content-center d-flex flex-wrap gap-4 mt-0 mb-md-0 mb-6' },
        { s: '.dt-layout-table', r: 'row mt-2' },
        { s: '.dt-layout-full', r: 'col-md col-12', a: 'table-responsive' }
      ].forEach(({ s, r, a }) => {
        document.querySelectorAll(s).forEach(el => {
          if (r) r.split(' ').forEach(c => el.classList.remove(c));
          if (a) a.split(' ').forEach(c => el.classList.add(c));
        });
      });
    }, 100);
  }

  // ── Modal lógica (igual que modal-add-role.js + app-access-roles.js) ─────
  const selectAll    = document.getElementById('selectAll');
  const permBoxes    = document.querySelectorAll('.perm-checkbox');
  const roleForm     = document.getElementById('roleForm');
  const roleTitle    = document.querySelector('.role-title');
  const nameInput    = document.getElementById('modalRoleName');
  const nameAlert    = document.getElementById('roleNameAlert');
  const addNewBtn    = document.querySelector('.add-new-role');
  const editLinks    = document.querySelectorAll('.role-edit-modal');

  // Seleccionar todo
  if (selectAll) {
    selectAll.addEventListener('change', () => {
      permBoxes.forEach(cb => cb.checked = selectAll.checked);
    });
    permBoxes.forEach(cb => {
      cb.addEventListener('change', () => {
        selectAll.checked = Array.from(permBoxes).every(c => c.checked);
        selectAll.indeterminate = !selectAll.checked && Array.from(permBoxes).some(c => c.checked);
      });
    });
  }

  // Botón "Agregar Nuevo Rol"
  if (addNewBtn) {
    addNewBtn.addEventListener('click', () => {
      roleTitle.textContent = 'Agregar Nuevo Rol';
      document.getElementById('formMethod').value = 'POST';
      roleForm.action = '{{ route('admin.roles.store') }}';
      nameInput.value = '';
      nameInput.removeAttribute('readonly');
      nameAlert.classList.add('d-none');
      permBoxes.forEach(cb => cb.checked = false);
      if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }
    });
  }

  // Links "Editar Rol" en cada card
  editLinks.forEach(link => {
    link.addEventListener('click', function () {
      const roleId      = this.dataset.roleId;
      const roleName    = this.dataset.roleName;
      const rolePerms   = JSON.parse(this.dataset.rolePermissions || '[]');
      const isSystem    = this.dataset.systemRole === '1';

      roleTitle.textContent = 'Editar Rol';
      document.getElementById('formMethod').value = 'PUT';
      roleForm.action = `/admin/roles/${roleId}`;

      nameInput.value = roleName;
      if (isSystem) {
        nameInput.setAttribute('readonly', true);
        nameAlert.classList.remove('d-none');
      } else {
        nameInput.removeAttribute('readonly');
        nameAlert.classList.add('d-none');
      }

      permBoxes.forEach(cb => cb.checked = rolePerms.includes(cb.value));

      if (selectAll) {
        selectAll.checked = Array.from(permBoxes).every(c => c.checked);
        selectAll.indeterminate = !selectAll.checked && Array.from(permBoxes).some(c => c.checked);
      }
    });
  });

});
</script>
@endsection
