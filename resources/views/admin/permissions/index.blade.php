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

<!-- Permissions Table -->
<div class="card">
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
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Permissions Table -->

@endsection

@section('admin-page-script')
<script>
'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const dtEl = document.querySelector('.datatables-permissions');
  if (!dtEl) return;

  const dt = new DataTable(dtEl, {
    ajax: {
      url: '{{ route('admin.permissions.data') }}',
      dataSrc: 'data'
    },
    columns: [
      { data: 'id' },
      { data: 'id' },
      { data: 'label' },
      { data: 'module' },
      { data: 'roles' },
      { data: 'created_at' },
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
        render: (data, type, full) =>
          `<span class="badge bg-label-secondary">${full.module}</span>`
      },
      {
        targets: 4,
        orderable: false,
        render: (data, type, full) => {
          if (!full.roles || full.roles.length === 0) return '<span class="text-muted">—</span>';
          return full.roles.map(r =>
            `<span class="badge bg-label-primary me-1">${r}</span>`
          ).join('');
        }
      },
      {
        targets: 5,
        orderable: false,
        render: (data, type, full) =>
          `<span class="text-nowrap">${full.created_at ?? '—'}</span>`
      },
    ],
    order: [[2, 'asc']],
    layout: {
      topStart: {
        rowClass: 'row m-3 my-0 justify-content-between',
        features: [{ pageLength: { menu: [10, 25, 50, 100], text: 'Mostrar _MENU_' } }]
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
        next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
      },
      info: 'Mostrando _START_ a _END_ de _TOTAL_ permisos',
      search: 'Buscar:',
      lengthMenu: 'Mostrar _MENU_',
      zeroRecords: 'No se encontraron permisos',
      emptyTable: 'No hay permisos registrados',
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
});
</script>
@endsection
