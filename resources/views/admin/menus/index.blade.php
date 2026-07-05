@extends('admin/layouts/master')

@section('title', 'Menús')

@section('admin-content')

<x-breadcrumb title="Menús" :items="[['label' => 'Menús']]" />

<p class="mb-6 text-body-secondary">Administra los menús de navegación del sitio público. Cada menú se ubica en una zona del frontend (encabezado, pie de página, etc.) y sus ítems se ordenan arrastrando.</p>

<div class="row g-6">
  @can('menus.create')
    <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card h-100">
        <div class="row h-100">
          <div class="col-sm-5">
            <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-4">
              <i class="ti tabler-list-plus text-primary" style="font-size:5rem;opacity:.85;"></i>
            </div>
          </div>
          <div class="col-sm-7">
            <div class="card-body text-sm-end text-center ps-sm-0">
              <button data-bs-target="#createMenuModal" data-bs-toggle="modal"
                      class="btn btn-sm btn-primary mb-4 text-nowrap">
                Crear Menú
              </button>
              <p class="mb-0">Agrega un nuevo menú<br>de navegación.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endcan

  @foreach ($menus as $menu)
    <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
              <h5 class="mb-1">{{ $menu->name }}</h5>
              <span class="badge bg-label-secondary">{{ $menu->slug }}</span>
              @if ($menu->location)
                <span class="badge bg-label-info">{{ $menu->location }}</span>
              @endif
            </div>
            <div class="d-flex align-items-center gap-1">
              @can('menus.edit')
                <a href="{{ route('admin.menus.edit', $menu) }}"
                   class="btn btn-icon btn-text-secondary rounded-pill waves-effect"
                   data-bs-toggle="tooltip" title="Editar ítems">
                  <i class="icon-base ti tabler-pencil icon-md"></i>
                </a>
              @endcan
              @can('menus.delete')
                <a href="javascript:void(0);"
                   class="btn btn-icon btn-text-secondary rounded-pill waves-effect"
                   data-bs-toggle="tooltip" title="Eliminar menú"
                   onclick="confirmDeleteUrl('{{ route('admin.menus.destroy', $menu) }}', '{{ addslashes($menu->name) }}')">
                  <i class="icon-base ti tabler-trash icon-md text-danger"></i>
                </a>
              @endcan
            </div>
          </div>

          <span class="text-muted small">
            <i class="icon-base ti tabler-list icon-xs me-1"></i>
            {{ $menu->all_items_count }} {{ $menu->all_items_count === 1 ? 'ítem' : 'ítems' }}
          </span>
        </div>
      </div>
    </div>
  @endforeach
</div>

@can('menus.create')
  <div class="modal fade" id="createMenuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('admin.menus.store') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Nuevo menú</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-4">
              <label class="form-label">Nombre</label>
              <input type="text" name="name" class="form-control" required maxlength="100" placeholder="Ej. Menú principal">
            </div>
            <div class="mb-4">
              <label class="form-label">Identificador (slug)</label>
              <input type="text" name="slug" class="form-control" required maxlength="100" placeholder="Ej. header-principal">
            </div>
            <div class="mb-0">
              <label class="form-label">Ubicación</label>
              <input type="text" name="location" class="form-control" maxlength="50" placeholder="Ej. header, footer, sidebar">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Crear menú</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endcan

@endsection
