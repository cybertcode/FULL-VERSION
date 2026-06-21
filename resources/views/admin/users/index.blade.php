@extends('admin/layouts/master')

@section('title', 'Usuarios')

@section('admin-vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
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

{{-- Breadcrumb --}}
<x-breadcrumb :items="[['label' => 'Usuarios']]" />

{{-- Stats cards --}}
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
            <small class="mb-0">Usuarios registrados</small>
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
            <small class="mb-0">Sin acceso al sistema</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="icon-base ti tabler-user-off icon-26px"></i>
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
            <span class="text-heading">Baneados</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">{{ $stats['banned'] }}</h4>
            </div>
            <small class="mb-0">Acceso bloqueado</small>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-danger">
              <i class="icon-base ti tabler-user-x icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Tabla --}}
<div class="card">
  <div class="card-header border-bottom">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <h5 class="card-title mb-0">Usuarios</h5>
      @can('users.create')
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
          <i class="icon-base ti tabler-plus me-1"></i> Nuevo usuario
        </a>
      @endcan
    </div>
    {{-- Filtros --}}
    <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
      <div class="col-md-4">
        <select id="filter-role" class="form-select select2" data-placeholder="Filtrar por rol">
          <option value="">Todos los roles</option>
          @foreach ($roles as $role)
            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <select id="filter-status" class="form-select select2" data-placeholder="Filtrar por estado">
          <option value="">Todos los estados</option>
          @foreach ($statuses as $status)
            <option value="{{ $status->value }}">{{ $status->label() }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <input type="search" id="filter-search" class="form-control" placeholder="Buscar por nombre o email...">
      </div>
    </div>
  </div>

  <div class="card-datatable table-responsive">
    <table class="table" id="users-table">
      <thead>
        <tr>
          <th>Usuario</th>
          <th>Teléfono</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Creado</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($users as $user)
          <tr>
            <td>
              <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-sm">
                  <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle" />
                </div>
                <div>
                  <span class="fw-medium d-block">{{ $user->name }}</span>
                  <small class="text-muted">{{ $user->email }}</small>
                </div>
              </div>
            </td>
            <td>{{ $user->phone ?? '—' }}</td>
            <td>
              @foreach ($user->roles as $role)
                <span class="badge bg-label-primary">{{ $role->name }}</span>
              @endforeach
            </td>
            <td>{!! statusBadge($user->status) !!}</td>
            <td>{{ formatDate($user->created_at) }}</td>
            <td class="text-center">
              <div class="d-flex align-items-center justify-content-center gap-1">
                @if ($user->trashed())
                  @can('users.restore')
                    <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-icon btn-sm btn-text-success rounded-pill"
                        title="Restaurar">
                        <i class="icon-base ti tabler-restore icon-sm"></i>
                      </button>
                    </form>
                  @endcan
                @else
                  @can('users.edit')
                    <a href="{{ route('admin.users.edit', $user) }}"
                      class="btn btn-icon btn-sm btn-text-secondary rounded-pill" title="Editar">
                      <i class="icon-base ti tabler-pencil icon-sm"></i>
                    </a>
                  @endcan
                  @can('users.delete')
                    @if ($user->id !== auth()->id())
                      <form id="del-user-{{ $user->id }}"
                        action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                      </form>
                      <button type="button" class="btn btn-icon btn-sm btn-text-danger rounded-pill"
                        title="Eliminar"
                        onclick="confirmDelete('del-user-{{ $user->id }}', '{{ addslashes($user->name) }}')">
                        <i class="icon-base ti tabler-trash icon-sm"></i>
                      </button>
                    @endif
                  @endcan
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">No hay usuarios registrados.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($users->hasPages())
    <div class="card-footer d-flex justify-content-end">
      {{ $users->links() }}
    </div>
  @endif
</div>

@endsection

@section('admin-page-script')
<script>
  window.addEventListener('load', function () {
    // Select2 filtros
    $('#filter-role, #filter-status').select2({ dropdownParent: $('body') });

    // Filtro de búsqueda en tabla (lado cliente — simple)
    const searchInput = document.getElementById('filter-search');
    const filterRole  = document.getElementById('filter-role');
    const filterStatus = document.getElementById('filter-status');

    function applyFilters() {
      const search = searchInput.value.toLowerCase();
      const role   = filterRole.value.toLowerCase();
      const status = filterStatus.value.toLowerCase();

      document.querySelectorAll('#users-table tbody tr').forEach(function (row) {
        const text   = row.textContent.toLowerCase();
        const roleEl = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() ?? '';
        const statusEl = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() ?? '';

        const matchSearch = !search || text.includes(search);
        const matchRole   = !role   || roleEl.includes(role);
        const matchStatus = !status || statusEl.includes(status);

        row.style.display = (matchSearch && matchRole && matchStatus) ? '' : 'none';
      });
    }

    searchInput.addEventListener('input', applyFilters);
    filterRole.addEventListener('change', applyFilters);
    filterStatus.addEventListener('change', applyFilters);
  });
</script>
@endsection
