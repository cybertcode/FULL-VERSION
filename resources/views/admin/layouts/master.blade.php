@extends('layouts/layoutMaster')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
  @yield('admin-vendor-style')
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
  @yield('admin-vendor-script')
@endsection

@section('page-script')
  {{-- Flash messages del servidor → showToast / Swal (definidos en x-ui-globals) --}}
  <x-flash-messages />

  {{-- DataTable: idioma español global — se aplica a TODOS los DataTables del panel --}}
  <script>
  'use strict';
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof DataTable === 'undefined') return;
    DataTable.defaults.language = {
      search:           '',
      searchPlaceholder:'Buscar…',
      lengthMenu:       '_MENU_',
      info:             'Mostrando _START_–_END_ de _TOTAL_ registros',
      infoEmpty:        'Sin registros disponibles',
      infoFiltered:     '(filtrado de _MAX_ registros en total)',
      zeroRecords:      'No se encontraron resultados',
      emptyTable:       'No hay datos en la tabla',
      loadingRecords:   'Cargando…',
      processing:       '<div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Procesando…',
      select: {
        rows: { _: '%d filas seleccionadas', 0: '', 1: '1 fila seleccionada' }
      },
      paginate: {
        first:    '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
        last:     '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>',
        next:     '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>'
      },
      aria: {
        sortAscending:  ': activar para ordenar de forma ascendente',
        sortDescending: ': activar para ordenar de forma descendente',
        paginate: {
          first:    'Primera',
          last:     'Última',
          next:     'Siguiente',
          previous: 'Anterior'
        }
      }
    };
  });
  </script>

  @yield('admin-page-script')
@endsection

@section('content')
  @if (session('impersonator_id'))
    <div class="alert alert-warning d-flex align-items-center justify-content-between mb-4" role="alert">
      <span>
        <i class="ti tabler-user-shield me-1"></i>
        Estás navegando como <strong>{{ auth()->user()->name }}</strong>.
      </span>
      <form method="POST" action="{{ route('admin.impersonate.leave') }}" class="m-0">
        @csrf
        <button type="submit" class="btn btn-sm btn-warning">Volver a mi sesión</button>
      </form>
    </div>
  @endif

  @yield('admin-content')
@endsection
