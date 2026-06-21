@extends('admin/layouts/master')

@section('title', 'Editar Usuario')

@section('admin-vendor-style')
  @vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('admin-vendor-script')
  @vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('admin-content')

<x-breadcrumb :items="[
  ['label' => 'Usuarios', 'url' => route('admin.users.index')],
  ['label' => 'Editar: ' . $user->name],
]" />

<div class="row">
  <div class="col-xl-9 col-lg-8">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')

      {{-- Información personal --}}
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Información personal</h5>
        </div>
        <div class="card-body">
          <div class="row g-5">

            {{-- Avatar actual --}}
            @if ($user->avatar || $user->profile_photo_url)
              <div class="col-12">
                <div class="d-flex align-items-center gap-4">
                  <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                    class="rounded-circle" width="80" height="80" style="object-fit:cover;">
                  <div>
                    <p class="mb-1 fw-medium">Foto actual</p>
                    <small class="text-muted">Sube una nueva imagen para reemplazarla.</small>
                  </div>
                </div>
              </div>
            @endif

            <div class="col-md-6">
              <label class="form-label" for="name">Nombre completo <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge @error('name') is-invalid @enderror">
                <span class="input-group-text"><i class="icon-base ti tabler-user"></i></span>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                  id="name" name="name" value="{{ old('name', $user->name) }}" required />
              </div>
              @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label" for="email">Correo electrónico <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge @error('email') is-invalid @enderror">
                <span class="input-group-text"><i class="icon-base ti tabler-mail"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                  id="email" name="email" value="{{ old('email', $user->email) }}" required />
              </div>
              @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label" for="phone">Teléfono</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="icon-base ti tabler-phone"></i></span>
                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                  id="phone" name="phone" value="{{ old('phone', $user->phone) }}" />
              </div>
              @error('phone')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label" for="avatar">Nueva foto de perfil</label>
              <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                id="avatar" name="avatar" accept="image/jpg,image/jpeg,image/png,image/webp" />
              @error('avatar')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>

      {{-- Contraseña (opcional en edición) --}}
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Cambiar contraseña <span class="text-muted fs-6 fw-normal">(opcional)</span></h5>
        </div>
        <div class="card-body">
          <div class="row g-5">
            <div class="col-md-6">
              <label class="form-label" for="password">Nueva contraseña</label>
              <div class="input-group input-group-merge @error('password') is-invalid @enderror">
                <span class="input-group-text"><i class="icon-base ti tabler-lock"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                  id="password" name="password" placeholder="Dejar en blanco para no cambiar" />
                <span class="input-group-text cursor-pointer" id="toggle-password">
                  <i class="icon-base ti tabler-eye-off"></i>
                </span>
              </div>
              @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="icon-base ti tabler-lock-check"></i></span>
                <input type="password" class="form-control"
                  id="password_confirmation" name="password_confirmation" />
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Botones --}}
      <div class="d-flex gap-3 justify-content-end mb-6">
        <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
          <i class="icon-base ti tabler-device-floppy me-1"></i> Actualizar usuario
        </button>
      </div>
    </form>
  </div>

  {{-- Sidebar: rol y estado --}}
  <div class="col-xl-3 col-lg-4">
    <div class="card mb-6">
      <div class="card-header">
        <h5 class="card-title mb-0">Rol y estado</h5>
      </div>
      <div class="card-body">
        <div class="mb-5">
          <label class="form-label" for="role">Rol <span class="text-danger">*</span></label>
          <select id="role" name="role" class="form-select select2 @error('role') is-invalid @enderror"
            data-placeholder="Seleccionar rol" form="edit-form">
            <option value=""></option>
            @foreach ($roles as $role)
              <option value="{{ $role }}"
                @selected(old('role', $user->roles->first()?->name) === $role)>
                {{ ucfirst($role) }}
              </option>
            @endforeach
          </select>
          @error('role')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        <div>
          <label class="form-label" for="status">Estado <span class="text-danger">*</span></label>
          <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
            @foreach ($statuses as $status)
              <option value="{{ $status->value }}"
                @selected(old('status', $user->status?->value) === $status->value)>
                {{ $status->label() }}
              </option>
            @endforeach
          </select>
          @error('status')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>
      </div>
    </div>

    {{-- Info del usuario --}}
    <div class="card">
      <div class="card-body">
        <p class="mb-1 text-muted small">Registrado</p>
        <p class="mb-3">{{ formatDateTime($user->created_at) }}</p>
        <p class="mb-1 text-muted small">Última actualización</p>
        <p class="mb-0">{{ formatDateTime($user->updated_at) }}</p>
      </div>
    </div>
  </div>
</div>

@endsection

@section('admin-page-script')
<script>
  window.addEventListener('load', function () {
    // Select2 necesita que el select esté dentro del form o lo bindeamos al body
    const roleSelect = document.getElementById('role');
    if (roleSelect) {
      roleSelect.setAttribute('form', '');
      $(roleSelect).select2({ dropdownParent: $('body'), placeholder: 'Seleccionar rol' });
      roleSelect.closest('form') || document.querySelector('form');
    }

    $('#role').select2({ dropdownParent: $('body'), placeholder: 'Seleccionar rol' });

    document.getElementById('toggle-password')?.addEventListener('click', function () {
      const input = document.getElementById('password');
      const icon  = this.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('tabler-eye-off', 'tabler-eye');
      } else {
        input.type = 'password';
        icon.classList.replace('tabler-eye', 'tabler-eye-off');
      }
    });
  });
</script>
@endsection
