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

{{-- ─── Role cards ─────────────────────────────────────────────────────────── --}}
<div class="row g-6">

  @foreach ($roles as $role)
    <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card">
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
                        $color  = $colors[ord($u->name[0]) % count($colors)];
                        $initials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', $u->name), 0, 2)));
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
              @if ($role->name !== 'Super-Admin' && $role->users_count === 0 && !in_array($role->name, ['admin', 'user', 'editor']))
                <form id="del-role-{{ $role->id }}"
                      action="{{ route('admin.roles.destroy', $role) }}"
                      method="POST" class="d-inline">
                  @csrf @method('DELETE')
                </form>
                <a href="javascript:void(0);"
                   onclick="confirmDelete('del-role-{{ $role->id }}', '{{ addslashes($role->name) }}')">
                  <i class="icon-base ti tabler-trash icon-md text-danger"></i>
                </a>
              @endif
            @endcan
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
              <img src="{{ asset('assets/img/illustrations/add-new-roles.png') }}"
                   class="img-fluid" alt="Agregar rol" width="83" />
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

  {{-- ─── Tabla usuarios con roles ─────────────────────────────────────────── --}}
  <div class="col-12">
    <h4 class="mt-6 mb-1">Usuarios y sus roles</h4>
    <p class="mb-0">Todos los usuarios del sistema y sus roles asignados.</p>
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

  const dtRolesTable = document.querySelector('.datatables-roles');
  if (!dtRolesTable) return;

  const roleIconMap = {
    'Super-Admin': '<span class="me-2"><i class="icon-base ti tabler-crown icon-22px text-primary"></i></span>',
    'admin':       '<span class="me-2"><i class="icon-base ti tabler-shield icon-22px text-warning"></i></span>',
    'editor':      '<span class="me-2"><i class="icon-base ti tabler-edit icon-22px text-info"></i></span>',
    'user':        '<span class="me-2"><i class="icon-base ti tabler-user icon-22px text-success"></i></span>',
  };

  const exportFormat = {
    format: {
      body: function (inner) {
        if (!inner || inner.indexOf('<') === -1) return inner;
        const doc = new DOMParser().parseFromString(inner, 'text/html');
        const nameEl = doc.querySelector('.role-name .fw-medium');
        if (nameEl) return nameEl.textContent.trim();
        return (doc.body.textContent || '').trim();
      }
    }
  };

  const dt = new DataTable(dtRolesTable, {
    ajax: { url: '{{ route('admin.users.data') }}', dataSrc: 'data' },
    columns: [
      { data: 'id' },
      { data: 'id', orderable: false, render: DataTable.render.select() },
      { data: 'name' },
      { data: 'role' },
      { data: 'status_label' },
      { data: 'id', orderable: false, searchable: false }
    ],
    columnDefs: [
      {
        className: 'control',
        orderable: false,
        searchable: false,
        responsivePriority: 5,
        targets: 0,
        render: () => ''
      },
      {
        targets: 1,
        orderable: false,
        searchable: false,
        responsivePriority: 3,
        checkboxes: { selectAllRender: '<input type="checkbox" class="form-check-input">' },
        render: () => '<input type="checkbox" class="dt-checkboxes form-check-input">'
      },
      {
        targets: 2,
        responsivePriority: 1,
        render: function (data, type, full) {
          const url = full.avatar_url;
          let avatar = url
            ? `<img src="${url}" alt="${full.name}" class="rounded-circle">`
            : `<span class="avatar-initial rounded-circle bg-label-primary">${(full.name.match(/\b\w/g)||[]).slice(0,2).join('').toUpperCase()}</span>`;
          return `
            <div class="d-flex justify-content-left align-items-center role-name">
              <div class="avatar-wrapper">
                <div class="avatar avatar-sm me-3">${avatar}</div>
              </div>
              <div class="d-flex flex-column">
                <span class="fw-medium text-heading">${full.name}</span>
                <small>@${full.email}</small>
              </div>
            </div>`;
        }
      },
      {
        targets: 3,
        render: (data, type, full) => {
          const icon = roleIconMap[full.role] || '<span class="me-2"><i class="icon-base ti tabler-circle icon-22px text-secondary"></i></span>';
          return `<span class="text-truncate d-flex align-items-center">${icon}${full.role}</span>`;
        }
      },
      {
        targets: 4,
        render: (data, type, full) =>
          full.status ? `<span class="badge ${full.status_class}">${full.status_label}</span>` : '—'
      },
      {
        targets: -1,
        title: 'Acciones',
        searchable: false,
        orderable: false,
        render: function (data, type, full) {
          if (full.restore_url) {
            // Usuario eliminado — solo restaurar
            return `
              <div class="d-flex align-items-center">
                <a href="javascript:;" class="btn btn-icon btn-text-secondary rounded-pill waves-effect restore-record"
                   data-url="${full.restore_url}" data-name="${full.name}" title="Restaurar">
                  <i class="icon-base ti tabler-refresh icon-md"></i>
                </a>
              </div>`;
          }
          return `
            <div class="d-flex align-items-center">
              <a href="javascript:;" class="btn btn-icon btn-text-secondary rounded-pill waves-effect delete-record"
                 data-url="${full.delete_url ?? ''}" data-name="${full.name}" title="Eliminar">
                <i class="icon-base ti tabler-trash icon-md"></i>
              </a>
              <a href="${full.edit_url ?? '#'}" class="btn btn-icon btn-text-secondary rounded-pill waves-effect" title="Editar">
                <i class="icon-base ti tabler-pencil icon-md"></i>
              </a>
              <a href="javascript:;" class="btn btn-icon btn-text-secondary rounded-pill waves-effect reset-password-record"
                 data-url="${full.reset_password_url ?? ''}" data-name="${full.name}" title="Resetear contraseña">
                <i class="icon-base ti tabler-key icon-md"></i>
              </a>
            </div>`;
        }
      }
    ],
    select: { style: 'multi', selector: 'td:nth-child(2)' },
    order: [[2, 'asc']],
    layout: {
      topStart: {
        rowClass: 'row my-md-0 me-3 ms-0 justify-content-between',
        features: [{ pageLength: { menu: [10, 25, 50, 100], text: '_MENU_' } }]
      },
      topEnd: {
        features: [
          { search: { placeholder: 'Buscar usuario', text: '_INPUT_' } },
          {
            buttons: [
              {
                extend: 'collection',
                className: 'btn btn-label-secondary dropdown-toggle me-4 waves-effect waves-light',
                text: '<span class="d-flex align-items-center gap-1"><i class="icon-base ti tabler-upload icon-xs"></i><span class="d-none d-sm-inline-block">Exportar</span></span>',
                buttons: [
                  {
                    extend: 'print',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-printer me-2"></i>Imprimir</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4], ...exportFormat },
                    customize: function (win) {
                      win.document.body.style.color = config.colors.headingColor;
                      win.document.body.style.borderColor = config.colors.borderColor;
                      win.document.body.style.backgroundColor = config.colors.bodyBg;
                      const table = win.document.body.querySelector('table');
                      table.classList.add('compact');
                      table.style.color = 'inherit';
                    }
                  },
                  {
                    extend: 'csv',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file me-2"></i>CSV</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4], ...exportFormat }
                  },
                  {
                    extend: 'excel',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-export me-2"></i>Excel</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4], ...exportFormat }
                  },
                  {
                    extend: 'pdf',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-file-text me-2"></i>PDF</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4], ...exportFormat }
                  },
                  {
                    extend: 'copy',
                    text: '<span class="d-flex align-items-center"><i class="icon-base ti tabler-copy me-2"></i>Copiar</span>',
                    className: 'dropdown-item',
                    exportOptions: { columns: [2, 3, 4], ...exportFormat }
                  }
                ]
              },
              {
                text: '<i class="icon-base ti tabler-plus me-0 me-sm-1 icon-16px"></i><span class="d-none d-sm-inline-block">Agregar Usuario</span>',
                className: 'btn btn-primary rounded-2 waves-effect waves-light',
                attr: { onclick: `window.location='{{ route('admin.users.create') }}'` }
              }
            ]
          }
        ]
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
        next:     '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>'
      }
    },
    responsive: {
      details: {
        display: DataTable.Responsive.display.modal({
          header: row => 'Detalle de ' + row.data()['name']
        }),
        type: 'column',
        renderer: function (api, rowIdx, columns) {
          const data = columns
            .filter(col => col.title !== '')
            .map(col => `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}"><td>${col.title}:</td><td>${col.data}</td></tr>`)
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

  // Eliminar registro
  dtRolesTable.addEventListener('click', function (e) {
    const btn = e.target.closest('.delete-record');
    if (!btn) return;
    confirmDeleteUrl(btn.dataset.url, btn.dataset.name);
  });

  // Resetear contraseña
  dtRolesTable.addEventListener('click', function (e) {
    const btn = e.target.closest('.reset-password-record');
    if (!btn) return;
    confirmAction({
      title: '¿Resetear contraseña?',
      text: `Se generará una contraseña aleatoria para "${btn.dataset.name}" y se enviará por correo.`,
      confirmText: 'Sí, resetear',
      isDanger: false,
      onConfirm: () => {
        fetch(btn.dataset.url, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        }).then(r => r.json()).then(() => {
          showToast('success', `Contraseña de ${btn.dataset.name} restablecida y enviada por correo.`);
        }).catch(() => showToast('error', 'Ocurrió un error al resetear la contraseña.'));
      }
    });
  });

  // Restaurar registro eliminado
  dtRolesTable.addEventListener('click', function (e) {
    const btn = e.target.closest('.restore-record');
    if (!btn) return;
    confirmAction({
      title: '¿Restaurar usuario?',
      text: `"${btn.dataset.name}" volverá a estar activo.`,
      confirmText: 'Sí, restaurar',
      isDanger: false,
      onConfirm: () => {
        fetch(btn.dataset.url, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        }).then(() => location.reload());
      }
    });
  });

  // Ajustar clases — copiado exacto de app-access-roles.js
  setTimeout(() => {
    [
      { s: '.dt-buttons .btn',              r: 'btn-secondary' },
      { s: '.dt-buttons.btn-group .btn-group', r: 'btn-group' },
      { s: '.dt-buttons.btn-group',         r: 'btn-group', a: 'd-flex' },
      { s: '.dt-search .form-control',      r: 'form-control-sm' },
      { s: '.dt-length .form-select',       r: 'form-select-sm' },
      { s: '.dt-length',                    a: 'mb-md-6 mb-0' },
      { s: '.dt-layout-start',              a: 'ps-3 mt-0' },
      { s: '.dt-layout-end',                r: 'justify-content-between', a: 'justify-content-md-between justify-content-center d-flex flex-wrap gap-4 mt-0 mb-md-0 mb-6' },
      { s: '.dt-layout-table',              r: 'row mt-2' },
      { s: '.dt-layout-full',               r: 'col-md col-12', a: 'table-responsive' }
    ].forEach(({ s, r, a }) => {
      document.querySelectorAll(s).forEach(el => {
        if (r) r.split(' ').forEach(c => el.classList.remove(c));
        if (a) a.split(' ').forEach(c => el.classList.add(c));
      });
    });
  }, 100);

  // ── Modal lógica ────────────────────────────────────────────────────────────
  const selectAll  = document.getElementById('selectAll');
  const permBoxes  = document.querySelectorAll('.perm-checkbox');
  const roleForm   = document.getElementById('roleForm');
  const roleTitle  = document.querySelector('.role-title');
  const nameInput  = document.getElementById('modalRoleName');
  const nameAlert  = document.getElementById('roleNameAlert');
  const addNewBtn  = document.querySelector('.add-new-role');

  if (selectAll) {
    selectAll.addEventListener('change', () => permBoxes.forEach(cb => cb.checked = selectAll.checked));
    permBoxes.forEach(cb => {
      cb.addEventListener('change', () => {
        selectAll.checked = Array.from(permBoxes).every(c => c.checked);
        selectAll.indeterminate = !selectAll.checked && Array.from(permBoxes).some(c => c.checked);
      });
    });
  }

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

  document.querySelectorAll('.role-edit-modal').forEach(link => {
    link.addEventListener('click', function () {
      const rolePerms = JSON.parse(this.dataset.rolePermissions || '[]');
      const isSystem  = this.dataset.systemRole === '1';

      roleTitle.textContent = 'Editar Rol';
      document.getElementById('formMethod').value = 'PUT';
      roleForm.action = `/admin/roles/${this.dataset.roleId}`;
      nameInput.value = this.dataset.roleName;

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
