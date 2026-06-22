@extends('admin/layouts/master')

@section('title', 'Usuarios')

@section('admin-vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
  ])
@endsection

@section('admin-vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/select2/select2.js',
  ])
@endsection

@section('admin-content')

<x-breadcrumb :items="[['label' => 'Usuarios']]" />

{{-- ─── Stat Cards (igual que app-user-list) ─────────────────────────────── --}}
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['total'] }}</h4>
            </div>
            <small class="mb-0">Todos los usuarios</small>
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
          <div class="content-left">
            <span class="text-heading">Activos</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['active'] }}</h4>
            </div>
            <small class="mb-0">Usuarios activos</small>
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
          <div class="content-left">
            <span class="text-heading">Inactivos</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['inactive'] }}</h4>
            </div>
            <small class="mb-0">Usuarios inactivos</small>
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
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Bloqueados</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['banned'] }}</h4>
            </div>
            <small class="mb-0">Usuarios bloqueados</small>
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
</div>

{{-- ─── Tabla de usuarios (igual que app-user-list) ───────────────────────── --}}
<div class="card">
  <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Filtros</h5>
    <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
      <div class="col-md-4 user_role">
        <select id="filterRole" class="form-select text-capitalize">
          <option value="">Todos los roles</option>
          @foreach ($roles as $role)
            <option value="{{ $role }}">{{ $role }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4 user_status">
        <select id="filterStatus" class="form-select">
          <option value="">Todos los estados</option>
          @foreach ($statuses as $status)
            <option value="{{ $status->value }}">{{ $status->label() }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4 d-flex justify-content-md-end">
        @can('users.create')
          <a href="{{ route('admin.users.create') }}"
             class="btn btn-primary">
            <i class="icon-base ti tabler-plus me-0 me-sm-1 icon-16px"></i>
            <span class="d-none d-sm-inline-block">Nuevo Usuario</span>
          </a>
        @endcan
      </div>
    </div>
  </div>
  <div class="card-datatable">
    <table class="datatables-users table">
      <thead class="border-top">
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

@endsection

@section('admin-page-script')
<script>
'use strict';

document.addEventListener('DOMContentLoaded', function () {

  const dtUsersTable = document.querySelector('.datatables-users');

  if (dtUsersTable) {
    const dt = new DataTable(dtUsersTable, {
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
          // Control responsive
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 2,
          targets: 0,
          render: () => ''
        },
        {
          // Checkbox selección
          targets: 1,
          orderable: false,
          searchable: false,
          responsivePriority: 4,
          render: () => '<input type="checkbox" class="dt-checkboxes form-check-input">'
        },
        {
          // Avatar + Nombre + Email
          targets: 2,
          responsivePriority: 1,
          render: function (data, type, full) {
            const name  = full.name;
            const email = full.email;
            const url   = full.avatar_url;
            const deletedClass = full.deleted_at ? ' opacity-50' : '';

            let avatar;
            if (url) {
              avatar = `<img src="${url}" alt="${name}" class="rounded-circle" width="34" height="34">`;
            } else {
              const initials = (name.match(/\b\w/g) || []).slice(0, 2).join('').toUpperCase();
              const colors   = ['primary','success','danger','warning','info'];
              const color    = colors[name.charCodeAt(0) % colors.length];
              avatar = `<span class="avatar-initial rounded-circle bg-label-${color}">${initials}</span>`;
            }

            return `
              <div class="d-flex justify-content-start align-items-center user-name${deletedClass}">
                <div class="avatar-wrapper">
                  <div class="avatar avatar-sm me-4">${avatar}</div>
                </div>
                <div class="d-flex flex-column">
                  <span class="fw-medium text-heading text-truncate">${name}</span>
                  <small>${email}</small>
                </div>
              </div>`;
          }
        },
        {
          // Rol
          targets: 3,
          render: function (data, type, full) {
            return `<span class="text-truncate d-flex align-items-center text-heading">${full.role}</span>`;
          }
        },
        {
          // Estado badge
          targets: 4,
          render: function (data, type, full) {
            if (!full.status) return '—';
            return `<span class="badge ${full.status_class}">${full.status_label}</span>`;
          }
        },
        {
          // Acciones
          targets: -1,
          title: 'Acciones',
          searchable: false,
          orderable: false,
          render: function (data, type, full) {
            if (full.deleted_at) {
              @can('users.restore')
              return `
                <form action="${full.restore_url}" method="POST" class="d-inline">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <button type="submit" class="btn btn-icon btn-text-success rounded-pill waves-effect" title="Restaurar">
                    <i class="icon-base ti tabler-restore icon-22px"></i>
                  </button>
                </form>`;
              @endcan
              return '';
            }

            let html = '<div class="d-flex align-items-center gap-1">';

            // Ver perfil
            if (full.show_url) {
              html += `<a href="${full.show_url}" class="btn btn-icon btn-text-secondary rounded-pill waves-effect" title="Ver perfil">
                         <i class="icon-base ti tabler-eye icon-22px"></i>
                       </a>`;
            }

            @can('users.edit')
            if (full.edit_url) {
              html += `<a href="${full.edit_url}" class="btn btn-icon btn-text-secondary rounded-pill waves-effect" title="Editar">
                         <i class="icon-base ti tabler-pencil icon-22px"></i>
                       </a>`;
            }
            @endcan

            @can('users.edit')
            if (full.reset_password_url) {
              html += `<button type="button"
                  class="btn btn-icon btn-text-secondary rounded-pill waves-effect"
                  title="Resetear contraseña"
                  data-reset-url="${full.reset_password_url}"
                  data-user-name="${full.name.replace(/"/g, '&quot;')}"
                  data-bs-toggle="tooltip">
                  <i class="icon-base ti tabler-key icon-22px"></i>
                </button>`;
            }
            @endcan

            @can('users.delete')
            if (full.delete_url) {
              html += `
                <form id="del-user-${full.id}" action="${full.delete_url}" method="POST" class="d-inline">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" name="_method" value="DELETE">
                </form>
                <button type="button"
                  class="btn btn-icon btn-text-secondary rounded-pill waves-effect"
                  title="Eliminar"
                  onclick="confirmDelete('del-user-${full.id}', '${full.name.replace(/'/g, "\\'")}')">
                  <i class="icon-base ti tabler-trash icon-22px"></i>
                </button>`;
            }
            @endcan

            html += '</div>';
            return html;
          }
        }
      ],
      select: {
        style: 'multi',
        selector: 'td:nth-child(2)'
      },
      order: [[2, 'asc']],
      responsive: {
        details: {
          display: DataTable.Responsive.display.modal({
            header: row => 'Detalles de ' + row.data().name
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
            div.innerHTML = `<table class="table"><tbody>${data}</tbody></table>`;
            return div;
          }
        }
      },
      layout: {
        topStart: {
          rowClass: 'row m-3 my-0 justify-content-between',
          features: [{ pageLength: { menu: [10, 25, 50, 100], text: '_MENU_' } }]
        },
        topEnd: {
          features: [
            { search: { placeholder: 'Buscar usuario', text: '_INPUT_' } }
          ]
        },
        bottomStart: {
          rowClass: 'row mx-3 justify-content-between',
          features: ['info']
        },
        bottomEnd: 'paging'
      },
      language: {
        search: '',
        lengthMenu: '_MENU_',
        info: 'Mostrando _START_ a _END_ de _TOTAL_ usuarios',
        infoEmpty: 'No hay usuarios',
        zeroRecords: 'No se encontraron usuarios',
        paginate: {
          next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
          previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>'
        }
      }
    });

    // Filtro por Rol
    document.getElementById('filterRole')?.addEventListener('change', function () {
      dt.column(3).search(this.value).draw();
    });

    // Filtro por Estado
    document.getElementById('filterStatus')?.addEventListener('change', function () {
      dt.column(4).search(this.value).draw();
    });

    // Reset password — delegación de evento en la tabla
    dtUsersTable.addEventListener('click', function (e) {
      const btn = e.target.closest('[data-reset-url]');
      if (!btn) return;
      const name = btn.dataset.userName;
      const url  = btn.dataset.resetUrl;
      confirmAction({
        title      : `¿Resetear contraseña?`,
        text       : `La contraseña de "${name}" será reemplazada por su DNI (o una temporal si no tiene DNI registrado).`,
        confirmText: 'Sí, resetear',
        cancelText : 'Cancelar',
        isDanger   : true,
        onConfirm  : () => {
          fetch(url, {
            method : 'POST',
            headers: {
              'X-CSRF-TOKEN' : '{{ csrf_token() }}',
              'Accept'       : 'application/json',
              'Content-Type' : 'application/json',
            }
          })
          .then(r => r.json())
          .then(data => showToast('success', data.message ?? 'Contraseña restablecida.'))
          .catch(() => showToast('error', 'No se pudo restablecer la contraseña.'));
        }
      });
    });

    // Ajustar clases de layout (igual que plantilla)
    setTimeout(() => {
      [
        { selector: '.dt-buttons .btn', remove: 'btn-secondary' },
        { selector: '.dt-search .form-control', remove: 'form-control-sm' },
        { selector: '.dt-length .form-select', remove: 'form-select-sm' },
        { selector: '.dt-length', add: 'mb-md-6 mb-0' },
        { selector: '.dt-layout-start', add: 'ps-3 mt-0' },
        { selector: '.dt-layout-end', remove: 'justify-content-between', add: 'justify-content-md-between justify-content-center d-flex flex-wrap gap-4 mt-0 mb-md-0 mb-6' },
        { selector: '.dt-layout-table', remove: 'row mt-2' },
        { selector: '.dt-layout-full', remove: 'col-md col-12', add: 'table-responsive' }
      ].forEach(({ selector, remove, add }) => {
        document.querySelectorAll(selector).forEach(el => {
          if (remove) remove.split(' ').forEach(c => el.classList.remove(c));
          if (add)    add.split(' ').forEach(c => el.classList.add(c));
        });
      });
    }, 100);
  }

});
</script>
@endsection
