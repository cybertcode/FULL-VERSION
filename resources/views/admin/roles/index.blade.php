@extends('admin/layouts/master')

@section('title', 'Roles y Permisos')

@section('admin-content')

<x-breadcrumb :items="[['label' => 'Roles y Permisos']]" />

<h4 class="mb-1">Lista de Roles</h4>
<p class="mb-6 text-muted">
  Cada rol agrupa un conjunto de permisos. Los usuarios heredan los permisos del rol que tienen asignado.
</p>

{{-- Cards de roles --}}
<div class="row g-6 mb-6">
  @foreach ($roles as $role)
    <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
              <h5 class="mb-0">{{ $role->name }}</h5>
              <small class="text-muted">{{ $role->users_count }} {{ $role->users_count === 1 ? 'usuario' : 'usuarios' }}</small>
            </div>
            <div class="d-flex gap-2">
              @if ($role->name !== 'Super-Admin')
                @can('roles.edit')
                  <button type="button"
                    class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                    title="Editar permisos"
                    data-bs-toggle="modal"
                    data-bs-target="#modal-edit-role-{{ $role->id }}">
                    <i class="icon-base ti tabler-pencil icon-sm"></i>
                  </button>
                @endcan
                @can('roles.delete')
                  @if ($role->users_count === 0 && ! in_array($role->name, ['admin', 'user']))
                    <form id="del-role-{{ $role->id }}"
                      action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline">
                      @csrf @method('DELETE')
                    </form>
                    <button type="button"
                      class="btn btn-icon btn-sm btn-text-danger rounded-pill"
                      title="Eliminar"
                      onclick="confirmDelete('del-role-{{ $role->id }}', '{{ addslashes($role->name) }}')">
                      <i class="icon-base ti tabler-trash icon-sm"></i>
                    </button>
                  @endif
                @endcan
              @else
                <span class="badge bg-label-warning">Sistema</span>
              @endif
            </div>
          </div>

          {{-- Permisos del rol --}}
          @if ($role->name === 'Super-Admin')
            <p class="text-muted small mb-0">
              <i class="icon-base ti tabler-shield-check text-success me-1"></i>
              Acceso total al sistema (bypass automático).
            </p>
          @else
            <div class="d-flex flex-wrap gap-1">
              @forelse ($role->permissions->take(8) as $permission)
                <span class="badge bg-label-primary text-truncate" style="max-width:120px" title="{{ $permission->name }}">
                  {{ $permission->name }}
                </span>
              @empty
                <span class="text-muted small">Sin permisos asignados.</span>
              @endforelse
              @if ($role->permissions->count() > 8)
                <span class="badge bg-label-secondary">+{{ $role->permissions->count() - 8 }} más</span>
              @endif
            </div>
          @endif
        </div>
      </div>
    </div>
  @endforeach

  {{-- Card agregar nuevo rol --}}
  @can('roles.create')
    <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card h-100 border-dashed" style="border: 2px dashed var(--bs-border-color);">
        <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-6">
          <div class="avatar avatar-lg mb-3">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="icon-base ti tabler-plus icon-26px"></i>
            </span>
          </div>
          <h5 class="mb-1">Nuevo Rol</h5>
          <p class="text-muted small mb-4">Crea un rol personalizado con permisos específicos.</p>
          <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-create-role">
            Crear rol
          </button>
        </div>
      </div>
    </div>
  @endcan
</div>

{{-- Modal: Crear rol --}}
@can('roles.create')
  <div class="modal fade" id="modal-create-role" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <form action="{{ route('admin.roles.store') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title">Crear nuevo rol</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-5">
              <label class="form-label" for="create-role-name">Nombre del rol <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="create-role-name" name="name"
                placeholder="Ej: moderador" required maxlength="100" />
            </div>

            <h6 class="mb-3">Permisos</h6>
            @foreach ($permissionsGrouped as $module => $perms)
              <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <label class="fw-medium text-capitalize">{{ $module }}</label>
                  <div class="form-check">
                    <input class="form-check-input module-check-all"
                      type="checkbox" id="create-all-{{ $module }}"
                      data-module="{{ $module }}" data-form="create">
                    <label class="form-check-label small text-muted" for="create-all-{{ $module }}">
                      Todos
                    </label>
                  </div>
                </div>
                <div class="row g-3">
                  @foreach ($perms as $permission)
                    <div class="col-sm-6 col-md-4">
                      <div class="form-check">
                        <input class="form-check-input perm-create-{{ $module }}"
                          type="checkbox" name="permissions[]"
                          value="{{ $permission->name }}"
                          id="create-perm-{{ $permission->id }}">
                        <label class="form-check-label" for="create-perm-{{ $permission->id }}">
                          <code class="small">{{ str()->after($permission->name, '.') }}</code>
                        </label>
                      </div>
                    </div>
                  @endforeach
                </div>
                <hr class="mt-3">
              </div>
            @endforeach
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Crear rol</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endcan

{{-- Modales: Editar roles --}}
@can('roles.edit')
  @foreach ($roles->where('name', '!=', 'Super-Admin') as $role)
    <div class="modal fade" id="modal-edit-role-{{ $role->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @csrf @method('PUT')
            <div class="modal-header">
              <h5 class="modal-title">Editar rol: <strong>{{ $role->name }}</strong></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              @if (! in_array($role->name, ['admin', 'user', 'editor']))
                <div class="mb-5">
                  <label class="form-label">Nombre del rol</label>
                  <input type="text" class="form-control" name="name"
                    value="{{ $role->name }}" required maxlength="100" />
                </div>
              @else
                <input type="hidden" name="name" value="{{ $role->name }}" />
                <div class="alert alert-info py-2 mb-4">
                  <i class="icon-base ti tabler-info-circle me-1"></i>
                  El nombre de los roles del sistema no puede modificarse.
                </div>
              @endif

              <h6 class="mb-3">Permisos</h6>
              @foreach ($permissionsGrouped as $module => $perms)
                <div class="mb-4">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <label class="fw-medium text-capitalize">{{ $module }}</label>
                    <div class="form-check">
                      <input class="form-check-input module-check-all"
                        type="checkbox" id="edit-all-{{ $role->id }}-{{ $module }}"
                        data-module="{{ $role->id }}-{{ $module }}" data-form="edit-{{ $role->id }}">
                      <label class="form-check-label small text-muted" for="edit-all-{{ $role->id }}-{{ $module }}">
                        Todos
                      </label>
                    </div>
                  </div>
                  <div class="row g-3">
                    @foreach ($perms as $permission)
                      <div class="col-sm-6 col-md-4">
                        <div class="form-check">
                          <input class="form-check-input perm-edit-{{ $role->id }}-{{ $module }}"
                            type="checkbox" name="permissions[]"
                            value="{{ $permission->name }}"
                            id="edit-perm-{{ $role->id }}-{{ $permission->id }}"
                            @checked($role->permissions->contains('name', $permission->name))>
                          <label class="form-check-label" for="edit-perm-{{ $role->id }}-{{ $permission->id }}">
                            <code class="small">{{ str()->after($permission->name, '.') }}</code>
                          </label>
                        </div>
                      </div>
                    @endforeach
                  </div>
                  <hr class="mt-3">
                </div>
              @endforeach
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endforeach
@endcan

@endsection

@section('admin-page-script')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // "Todos" por módulo — marca/desmarca todos los checkboxes del módulo
    document.querySelectorAll('.module-check-all').forEach(function (masterCheck) {
      const module = masterCheck.dataset.module;
      const form   = masterCheck.dataset.form;
      const checkboxClass = form.startsWith('edit-')
        ? `.perm-edit-${module}`
        : `.perm-create-${module}`;

      masterCheck.addEventListener('change', function () {
        document.querySelectorAll(checkboxClass).forEach(function (cb) {
          cb.checked = masterCheck.checked;
        });
      });

      // Sincronizar estado inicial del "Todos"
      const children = document.querySelectorAll(checkboxClass);
      const allChecked = children.length > 0 && Array.from(children).every(cb => cb.checked);
      masterCheck.checked = allChecked;
    });
  });
</script>
@endsection
