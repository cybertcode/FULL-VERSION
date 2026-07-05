@extends('admin/layouts/master')

@section('title', 'Páginas')

@section('admin-content')

<x-breadcrumb title="Páginas" :items="[['label' => 'Páginas']]" />

{{-- ─── Stat cards ─────────────────────────────────────────────────────────── --}}
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-3">
    <a href="{{ route('admin.pages.index') }}" class="text-decoration-none">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading">Total</span>
              <h4 class="mb-0 my-1">{{ $stats['total'] }}</h4>
              <small class="mb-0 text-muted">Todas las páginas</small>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-primary">
                <i class="icon-base ti tabler-files icon-26px"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-sm-6 col-xl-3">
    <a href="{{ route('admin.pages.index', ['status' => 'published']) }}" class="text-decoration-none">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading">Publicadas</span>
              <h4 class="mb-0 my-1">{{ $stats['published'] }}</h4>
              <small class="mb-0 text-muted">Visibles en el sitio</small>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-success">
                <i class="icon-base ti tabler-world icon-26px"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-sm-6 col-xl-3">
    <a href="{{ route('admin.pages.index', ['status' => 'draft']) }}" class="text-decoration-none">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading">Borradores</span>
              <h4 class="mb-0 my-1">{{ $stats['draft'] }}</h4>
              <small class="mb-0 text-muted">Sin publicar</small>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-secondary">
                <i class="icon-base ti tabler-file-pencil icon-26px"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>
  <div class="col-sm-6 col-xl-3">
    <a href="{{ route('admin.pages.index', ['solo_deleted' => 1]) }}" class="text-decoration-none">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading">Papelera</span>
              <h4 class="mb-0 my-1">{{ $stats['trashed'] }}</h4>
              <small class="mb-0 text-muted">Eliminadas</small>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-danger">
                <i class="icon-base ti tabler-trash icon-26px"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
      <input type="text" name="search" class="form-control" placeholder="Buscar página..." value="{{ request('search') }}" style="max-width:220px">
      <select name="status" class="form-select" style="max-width:170px" @if(request('solo_deleted')) disabled @endif>
        <option value="">Todos los estados</option>
        @foreach ($statuses as $status)
          <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
        @endforeach
      </select>
      <select name="template" class="form-select" style="max-width:200px">
        <option value="">Todas las plantillas</option>
        @foreach ($templates as $template)
          <option value="{{ $template->value }}" @selected(request('template') === $template->value)>{{ $template->label() }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-outline-secondary">
        <i class="icon-base ti tabler-filter"></i>
      </button>
      @if (request('solo_deleted'))
        <input type="hidden" name="solo_deleted" value="1">
        <a href="{{ route('admin.pages.index') }}" class="btn btn-label-secondary">Salir de la papelera</a>
      @endif
    </form>

    @can('pages.create')
      <a href="{{ route('admin.pages.create') }}" class="btn btn-primary waves-effect waves-light">
        <i class="icon-base ti tabler-plus me-1"></i> Nueva página
      </a>
    @endcan
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Título</th>
          <th>Plantilla</th>
          <th>Estado</th>
          <th>Autor</th>
          <th>Fecha</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($pages as $page)
          <tr>
            <td>
              <span style="padding-left: {{ $page->depth * 1.5 }}rem">
                @if ($page->depth > 0)
                  <i class="icon-base ti tabler-corner-down-right text-muted me-1"></i>
                @endif
                <span class="fw-medium">{{ $page->title }}</span>
              </span>
              <div class="text-muted small" style="padding-left: {{ $page->depth * 1.5 }}rem">/{{ $page->slug }}</div>
            </td>
            <td>{{ $page->template->label() }}</td>
            <td><span class="badge {{ $page->status->badgeClass() }}">{{ $page->status->label() }}</span></td>
            <td>{{ $page->creator?->name ?? '—' }}</td>
            <td>{{ $page->created_at->format('d/m/Y') }}</td>
            <td class="text-end">
              @if ($page->trashed())
                @can('pages.restore')
                  <form id="restore-page-{{ $page->id }}" action="{{ route('admin.pages.restore', $page->id) }}" method="POST" class="d-inline">
                    @csrf
                  </form>
                  <button type="button" class="btn btn-icon btn-text-secondary rounded-pill waves-effect"
                          data-bs-toggle="tooltip" title="Restaurar"
                          onclick="confirmAction({title:'¿Restaurar página?', text:'«sa {{ addslashes($page->title) }}» volverá a estar disponible.', confirmText:'Restaurar', onConfirm:()=>document.getElementById('restore-page-{{ $page->id }}').submit()})">
                    <i class="icon-base ti tabler-refresh icon-md"></i>
                  </button>
                @endcan
                @can('pages.forceDelete')
                  <a href="javascript:void(0);"
                     class="btn btn-icon btn-text-secondary rounded-pill waves-effect"
                     data-bs-toggle="tooltip" title="Eliminar permanentemente"
                     onclick="confirmDeleteUrl('{{ route('admin.pages.force-delete', $page->id) }}', '{{ addslashes($page->title) }}')">
                    <i class="icon-base ti tabler-trash-x icon-md text-danger"></i>
                  </a>
                @endcan
              @else
                @can('pages.edit')
                  <a href="{{ route('admin.pages.edit', $page) }}"
                     class="btn btn-icon btn-text-secondary rounded-pill waves-effect"
                     data-bs-toggle="tooltip" title="Editar">
                    <i class="icon-base ti tabler-pencil icon-md"></i>
                  </a>
                @endcan
                @can('pages.delete')
                  <a href="javascript:void(0);"
                     class="btn btn-icon btn-text-secondary rounded-pill waves-effect"
                     data-bs-toggle="tooltip" title="Mover a la papelera"
                     onclick="confirmDeleteUrl('{{ route('admin.pages.destroy', $page) }}', '{{ addslashes($page->title) }}')">
                    <i class="icon-base ti tabler-trash icon-md text-danger"></i>
                  </a>
                @endcan
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-6">
              No hay páginas {{ request('solo_deleted') ? 'en la papelera' : 'todavía' }}.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($pages->hasPages())
    <div class="card-footer">
      {{ $pages->links() }}
    </div>
  @endif
</div>

@endsection
