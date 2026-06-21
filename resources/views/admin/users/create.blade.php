@extends('admin/layouts/master')

@section('title', 'Nuevo Usuario')

@section('admin-vendor-style')
  @vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('admin-vendor-script')
  @vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('admin-content')

<x-breadcrumb :items="[
  ['label' => 'Usuarios', 'url' => route('admin.users.index')],
  ['label' => 'Nuevo usuario'],
]" />

<div class="row">
  <div class="col-xl-9 col-lg-8">
    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      {{-- Información personal --}}
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Información personal</h5>
        </div>
        <div class="card-body">
          <div class="row g-5">
            <div class="col-md-6">
              <label class="form-label" for="name">Nombre completo <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge @error('name') is-invalid @enderror">
                <span class="input-group-text"><i class="icon-base ti tabler-user"></i></span>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                  id="name" name="name" value="{{ old('name') }}" placeholder="Juan Pérez" required />
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
                  id="email" name="email" value="{{ old('email') }}" placeholder="juan@ejemplo.com" required />
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
                  id="phone" name="phone" value="{{ old('phone') }}" placeholder="+51 999 999 999" />
              </div>
              @error('phone')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label" for="avatar">Foto de perfil</label>
              <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                id="avatar" name="avatar" accept="image/jpg,image/jpeg,image/png,image/webp" />
              @error('avatar')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      </div>

      {{-- Seguridad --}}
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="card-title mb-0">Seguridad</h5>
        </div>
        <div class="card-body">
          <div class="row g-5">
            <div class="col-md-6">
              <label class="form-label" for="password">Contraseña <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge @error('password') is-invalid @enderror">
                <span class="input-group-text"><i class="icon-base ti tabler-lock"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                  id="password" name="password" placeholder="Mínimo 8 caracteres" required />
                <span class="input-group-text cursor-pointer" id="toggle-password">
                  <i class="icon-base ti tabler-eye-off"></i>
                </span>
              </div>
              @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label" for="password_confirmation">Confirmar contraseña <span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="icon-base ti tabler-lock-check"></i></span>
                <input type="password" class="form-control"
                  id="password_confirmation" name="password_confirmation" placeholder="Repite la contraseña" required />
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Botones --}}
      <div class="d-flex gap-3 justify-content-end mb-6">
        <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
          <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar usuario
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
            data-placeholder="Seleccionar rol">
            <option value=""></option>
            @foreach ($roles as $role)
              <option value="{{ $role }}" @selected(old('role') === $role)>{{ ucfirst($role) }}</option>
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
              <option value="{{ $status->value }}" @selected(old('status', 'active') === $status->value)>
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
  </div>
</div>

@endsection

@section('admin-page-script')
<script>
  window.addEventListener('load', function () {
    $('#role').select2({ dropdownParent: $('body'), placeholder: 'Seleccionar rol' });

    document.getElementById('toggle-password').addEventListener('click', function () {
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
