@extends('admin/layouts/master')

@section('title', 'Editar Usuario')

@section('admin-vendor-style')
  @vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  ])
@endsection

@section('admin-vendor-script')
  @vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  ])
@endsection

@section('admin-content')

<x-breadcrumb :items="[
  ['label' => 'Usuarios', 'url' => route('admin.users.index')],
  ['label' => 'Editar: ' . $user->name],
]" />

@php $perfil = $user->perfil; @endphp

<form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="row g-6">

  {{-- ══════════════════════════════════════════════
       COLUMNA PRINCIPAL
  ══════════════════════════════════════════════ --}}
  <div class="col-xl-8 col-lg-7">

    {{-- ── Identidad ────────────────────────────── --}}
    <div class="card mb-6">
      <div class="card-header">
        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
          <i class="icon-base ti tabler-id-badge-2 text-primary"></i> Datos personales
        </h5>
      </div>
      <div class="card-body">
        <div class="row g-5">

          <div class="col-md-4">
            <label class="form-label" for="apellido_paterno">Apellido paterno</label>
            <input type="text" id="apellido_paterno" name="perfil[apellido_paterno]"
              class="form-control @error('perfil.apellido_paterno') is-invalid @enderror"
              value="{{ old('perfil.apellido_paterno', $perfil?->apellido_paterno) }}"
              placeholder="García" />
            @error('perfil.apellido_paterno')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-4">
            <label class="form-label" for="apellido_materno">Apellido materno</label>
            <input type="text" id="apellido_materno" name="perfil[apellido_materno]"
              class="form-control @error('perfil.apellido_materno') is-invalid @enderror"
              value="{{ old('perfil.apellido_materno', $perfil?->apellido_materno) }}"
              placeholder="López" />
            @error('perfil.apellido_materno')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-4">
            <label class="form-label" for="nombres">Nombres</label>
            <input type="text" id="nombres" name="perfil[nombres]"
              class="form-control @error('perfil.nombres') is-invalid @enderror"
              value="{{ old('perfil.nombres', $perfil?->nombres) }}"
              placeholder="Juan Carlos" />
            @error('perfil.nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-4">
            <label class="form-label" for="dni">DNI</label>
            <div class="input-group input-group-merge @error('perfil.dni') is-invalid @enderror">
              <span class="input-group-text"><i class="icon-base ti tabler-id"></i></span>
              <input type="text" id="dni" name="perfil[dni]"
                class="form-control @error('perfil.dni') is-invalid @enderror"
                value="{{ old('perfil.dni', $perfil?->dni) }}"
                placeholder="12345678" maxlength="8" inputmode="numeric" />
            </div>
            @error('perfil.dni')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            <div class="form-text">Se usará como contraseña al resetear.</div>
          </div>

          <div class="col-md-4">
            <label class="form-label" for="fecha_nacimiento">Fecha de nacimiento</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="icon-base ti tabler-calendar"></i></span>
              <input type="text" id="fecha_nacimiento" name="perfil[fecha_nacimiento]"
                class="form-control flatpickr-date @error('perfil.fecha_nacimiento') is-invalid @enderror"
                value="{{ old('perfil.fecha_nacimiento', $perfil?->fecha_nacimiento?->format('Y-m-d')) }}"
                placeholder="DD/MM/AAAA" />
            </div>
            @error('perfil.fecha_nacimiento')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-2">
            <label class="form-label" for="sexo">Sexo</label>
            <select id="sexo" name="perfil[sexo]"
              class="form-select @error('perfil.sexo') is-invalid @enderror">
              <option value="">—</option>
              <option value="M" @selected(old('perfil.sexo', $perfil?->sexo) === 'M')>Masculino</option>
              <option value="F" @selected(old('perfil.sexo', $perfil?->sexo) === 'F')>Femenino</option>
            </select>
            @error('perfil.sexo')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-2">
            <label class="form-label" for="nacionalidad">Nacionalidad</label>
            <input type="text" id="nacionalidad" name="perfil[nacionalidad]"
              class="form-control @error('perfil.nacionalidad') is-invalid @enderror"
              value="{{ old('perfil.nacionalidad', $perfil?->nacionalidad ?? 'Peruana') }}" />
            @error('perfil.nacionalidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

        </div>
      </div>
    </div>

    {{-- ── Datos laborales ──────────────────────── --}}
    <div class="card mb-6">
      <div class="card-header">
        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
          <i class="icon-base ti tabler-briefcase text-primary"></i> Datos laborales
        </h5>
      </div>
      <div class="card-body">
        <div class="row g-5">

          <div class="col-md-6">
            <label class="form-label" for="cargo">Cargo</label>
            <input type="text" id="cargo" name="perfil[cargo]"
              class="form-control @error('perfil.cargo') is-invalid @enderror"
              value="{{ old('perfil.cargo', $perfil?->cargo) }}"
              placeholder="Especialista en Gestión Pedagógica" />
            @error('perfil.cargo')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="area">Área / Unidad</label>
            <input type="text" id="area" name="perfil[area]"
              class="form-control @error('perfil.area') is-invalid @enderror"
              value="{{ old('perfil.area', $perfil?->area) }}"
              placeholder="Área de Gestión Institucional" />
            @error('perfil.area')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-4">
            <label class="form-label" for="fecha_ingreso">Fecha de ingreso</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="icon-base ti tabler-calendar-check"></i></span>
              <input type="text" id="fecha_ingreso" name="perfil[fecha_ingreso]"
                class="form-control flatpickr-date @error('perfil.fecha_ingreso') is-invalid @enderror"
                value="{{ old('perfil.fecha_ingreso', $perfil?->fecha_ingreso?->format('Y-m-d')) }}"
                placeholder="DD/MM/AAAA" />
            </div>
            @error('perfil.fecha_ingreso')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-4">
            <label class="form-label" for="codigo_empleado">Código de empleado</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="icon-base ti tabler-hash"></i></span>
              <input type="text" id="codigo_empleado" name="perfil[codigo_empleado]"
                class="form-control @error('perfil.codigo_empleado') is-invalid @enderror"
                value="{{ old('perfil.codigo_empleado', $perfil?->codigo_empleado) }}"
                placeholder="EMP-0001" />
            </div>
            @error('perfil.codigo_empleado')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

        </div>
      </div>
    </div>

    {{-- ── Contacto institucional ───────────────── --}}
    <div class="card mb-6">
      <div class="card-header">
        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
          <i class="icon-base ti tabler-address-book text-primary"></i> Contacto institucional
        </h5>
      </div>
      <div class="card-body">
        <div class="row g-5">

          <div class="col-md-4">
            <label class="form-label" for="telefono_celular">Celular institucional</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="icon-base ti tabler-device-mobile"></i></span>
              <input type="text" id="telefono_celular" name="perfil[telefono_celular]"
                class="form-control @error('perfil.telefono_celular') is-invalid @enderror"
                value="{{ old('perfil.telefono_celular', $perfil?->telefono_celular) }}"
                placeholder="987 654 321" />
            </div>
            @error('perfil.telefono_celular')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-4">
            <label class="form-label" for="telefono_fijo">Teléfono fijo</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="icon-base ti tabler-phone"></i></span>
              <input type="text" id="telefono_fijo" name="perfil[telefono_fijo]"
                class="form-control @error('perfil.telefono_fijo') is-invalid @enderror"
                value="{{ old('perfil.telefono_fijo', $perfil?->telefono_fijo) }}"
                placeholder="(01) 123-4567" />
            </div>
            @error('perfil.telefono_fijo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-2">
            <label class="form-label" for="anexo">Anexo</label>
            <input type="text" id="anexo" name="perfil[anexo]"
              class="form-control @error('perfil.anexo') is-invalid @enderror"
              value="{{ old('perfil.anexo', $perfil?->anexo) }}"
              placeholder="201" maxlength="10" />
            @error('perfil.anexo')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="email_institucional">Correo institucional</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="icon-base ti tabler-mail-forward"></i></span>
              <input type="email" id="email_institucional" name="perfil[email_institucional]"
                class="form-control @error('perfil.email_institucional') is-invalid @enderror"
                value="{{ old('perfil.email_institucional', $perfil?->email_institucional) }}"
                placeholder="j.perez@empresa.com" />
            </div>
            @error('perfil.email_institucional')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="linkedin">LinkedIn</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="icon-base ti tabler-brand-linkedin"></i></span>
              <input type="url" id="linkedin" name="perfil[linkedin]"
                class="form-control @error('perfil.linkedin') is-invalid @enderror"
                value="{{ old('perfil.linkedin', $perfil?->linkedin) }}"
                placeholder="https://linkedin.com/in/usuario" />
            </div>
            @error('perfil.linkedin')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

          {{-- Ubicación dentro del bloque contacto (menos cards = menos ruido) --}}
          <div class="col-12"><hr class="my-1"></div>

          <div class="col-md-5">
            <label class="form-label" for="direccion">Dirección</label>
            <input type="text" id="direccion" name="perfil[direccion]"
              class="form-control @error('perfil.direccion') is-invalid @enderror"
              value="{{ old('perfil.direccion', $perfil?->direccion) }}"
              placeholder="Av. Principal 123, Urb. Los Jardines" />
            @error('perfil.direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-2">
            <label class="form-label" for="ubigeo">Ubigeo</label>
            <input type="text" id="ubigeo" name="perfil[ubigeo]"
              class="form-control @error('perfil.ubigeo') is-invalid @enderror"
              value="{{ old('perfil.ubigeo', $perfil?->ubigeo) }}"
              placeholder="150101" maxlength="6" />
            @error('perfil.ubigeo')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-2">
            <label class="form-label" for="distrito">Distrito</label>
            <input type="text" id="distrito" name="perfil[distrito]"
              class="form-control @error('perfil.distrito') is-invalid @enderror"
              value="{{ old('perfil.distrito', $perfil?->distrito) }}"
              placeholder="Lima" />
            @error('perfil.distrito')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-3">
            <label class="form-label" for="departamento">Departamento</label>
            <select id="departamento" name="perfil[departamento]"
              class="form-select select2 @error('perfil.departamento') is-invalid @enderror"
              data-placeholder="Seleccionar">
              <option value=""></option>
              @foreach (['Amazonas','Áncash','Apurímac','Arequipa','Ayacucho','Cajamarca','Callao','Cusco','Huancavelica','Huánuco','Ica','Junín','La Libertad','Lambayeque','Lima','Loreto','Madre de Dios','Moquegua','Pasco','Piura','Puno','San Martín','Tacna','Tumbes','Ucayali'] as $dep)
                <option value="{{ $dep }}" @selected(old('perfil.departamento', $perfil?->departamento) === $dep)>{{ $dep }}</option>
              @endforeach
            </select>
            @error('perfil.departamento')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          </div>

          {{-- Bio --}}
          <div class="col-12">
            <label class="form-label" for="bio">Presentación / Biografía</label>
            <textarea id="bio" name="perfil[bio]" rows="3"
              class="form-control @error('perfil.bio') is-invalid @enderror"
              placeholder="Breve descripción profesional...">{{ old('perfil.bio', $perfil?->bio) }}</textarea>
            @error('perfil.bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

        </div>
      </div>
    </div>

    {{-- ── Botones ──────────────────────────────── --}}
    <div class="d-flex gap-3 justify-content-end mb-6">
      <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary">Cancelar</a>
      <button type="submit" class="btn btn-primary">
        <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar cambios
      </button>
    </div>

  </div>

  {{-- ══════════════════════════════════════════════
       SIDEBAR
  ══════════════════════════════════════════════ --}}
  <div class="col-xl-4 col-lg-5">

    {{-- Foto de perfil --}}
    <div class="card mb-6">
      <div class="card-body text-center py-5">
        <div class="position-relative d-inline-block mb-4">
          <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
            id="avatar-preview"
            class="rounded-circle object-fit-cover border"
            style="width:100px; height:100px;" />
          <label for="avatar"
            class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle d-flex align-items-center justify-content-center"
            style="width:32px; height:32px; padding:0;" title="Cambiar foto">
            <i class="icon-base ti tabler-camera icon-sm"></i>
            <input type="file" id="avatar" name="avatar" hidden accept="image/png,image/jpeg,image/webp" />
          </label>
        </div>
        <h6 class="mb-0">{{ $user->name }}</h6>
        <p class="text-muted small mb-0">{{ $user->email }}</p>
        @error('avatar')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- Email del sistema + teléfono --}}
    <div class="card mb-6">
      <div class="card-header">
        <h6 class="card-title mb-0 d-flex align-items-center gap-2">
          <i class="icon-base ti tabler-lock text-primary"></i> Acceso al sistema
        </h6>
      </div>
      <div class="card-body">
        <div class="mb-4">
          <label class="form-label" for="email">Correo electrónico <span class="text-danger">*</span></label>
          <div class="input-group input-group-merge @error('email') is-invalid @enderror">
            <span class="input-group-text"><i class="icon-base ti tabler-mail"></i></span>
            <input type="email" id="email" name="email"
              class="form-control @error('email') is-invalid @enderror"
              value="{{ old('email', $user->email) }}" required />
          </div>
          @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
          <label class="form-label" for="username">Usuario <span class="text-muted fw-normal">(opcional)</span></label>
          <div class="input-group input-group-merge @error('username') is-invalid @enderror">
            <span class="input-group-text"><i class="icon-base ti tabler-at"></i></span>
            <input type="text" id="username" name="username"
              class="form-control @error('username') is-invalid @enderror"
              value="{{ old('username', $user->username) }}"
              placeholder="juan.garcia" autocomplete="off" />
          </div>
          @error('username')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          <div class="form-text">Solo letras, números, guiones y puntos. Único en el sistema.</div>
        </div>

        <div class="mb-4">
          <label class="form-label" for="phone">Teléfono personal</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-phone"></i></span>
            <input type="text" id="phone" name="phone"
              class="form-control @error('phone') is-invalid @enderror"
              value="{{ old('phone', $user->phone) }}" />
          </div>
          @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
          <label class="form-label" for="password">Nueva contraseña <span class="text-muted fw-normal">(opcional)</span></label>
          <div class="input-group input-group-merge @error('password') is-invalid @enderror">
            <span class="input-group-text"><i class="icon-base ti tabler-lock"></i></span>
            <input type="password" id="password" name="password"
              class="form-control @error('password') is-invalid @enderror"
              placeholder="Dejar en blanco para no cambiar" />
            <span class="input-group-text cursor-pointer" id="toggle-password">
              <i class="icon-base ti tabler-eye-off"></i>
            </span>
          </div>
          @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div>
          <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-lock-check"></i></span>
            <input type="password" id="password_confirmation" name="password_confirmation"
              class="form-control" />
          </div>
        </div>
      </div>
    </div>

    {{-- Rol y estado --}}
    <div class="card mb-6">
      <div class="card-header">
        <h6 class="card-title mb-0 d-flex align-items-center gap-2">
          <i class="icon-base ti tabler-shield text-primary"></i> Rol y estado
        </h6>
      </div>
      <div class="card-body">
        <div class="mb-4">
          <label class="form-label" for="role">Rol <span class="text-danger">*</span></label>
          <select id="role" name="role"
            class="form-select select2 @error('role') is-invalid @enderror"
            data-placeholder="Seleccionar rol">
            <option value=""></option>
            @foreach ($roles as $roleName)
              <option value="{{ $roleName }}" @selected(old('role', $user->roles->first()?->name) === $roleName)>
                {{ ucfirst($roleName) }}
              </option>
            @endforeach
          </select>
          @error('role')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div>
          <label class="form-label" for="status">Estado <span class="text-danger">*</span></label>
          <select id="status" name="status"
            class="form-select @error('status') is-invalid @enderror">
            @foreach ($statuses as $st)
              <option value="{{ $st->value }}" @selected(old('status', $user->status?->value) === $st->value)>
                {{ $st->label() }}
              </option>
            @endforeach
          </select>
          @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>

    {{-- Metadatos + completitud --}}
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted small">Registrado</span>
          <span class="small fw-medium">{{ formatDateTime($user->created_at) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-4">
          <span class="text-muted small">Actualizado</span>
          <span class="small fw-medium">{{ formatDateTime($user->updated_at) }}</span>
        </div>
        @if ($perfil)
          @php
            $campos = ['dni','apellido_paterno','apellido_materno','cargo','area',
                       'telefono_celular','email_institucional',
                       'departamento','bio'];
            $llenos = collect($campos)->filter(fn($c) => !empty($perfil->$c))->count();
            $pct    = (int) round($llenos / count($campos) * 100);
            $color  = $pct >= 80 ? 'success' : ($pct >= 40 ? 'warning' : 'danger');
          @endphp
          <p class="small mb-1 d-flex justify-content-between">
            <span class="text-muted">Perfil completado</span>
            <span class="fw-medium">{{ $pct }}%</span>
          </p>
          <div class="progress" style="height:6px">
            <div class="progress-bar bg-{{ $color }}" style="width:{{ $pct }}%"></div>
          </div>
          <p class="text-muted small mt-1 mb-0">{{ $llenos }} de {{ count($campos) }} campos</p>
        @endif
      </div>
    </div>

  </div>
</div>

</form>
@endsection

@section('admin-page-script')
<script>
window.addEventListener('load', function () {
  $('#role, #departamento').select2({ dropdownParent: $('body') });

  document.querySelectorAll('.flatpickr-date').forEach(el => {
    flatpickr(el, { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y', allowInput: true });
  });

  document.getElementById('avatar').addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = e => document.getElementById('avatar-preview').src = e.target.result;
      reader.readAsDataURL(file);
    }
  });

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
