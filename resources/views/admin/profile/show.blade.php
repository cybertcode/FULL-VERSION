@extends('admin/layouts/master')

@section('title', 'Mi Perfil')

@section('admin-vendor-style')
  @vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/scss/pages/page-profile.scss',
  ])
@endsection

@section('admin-vendor-script')
  @vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  ])
@endsection

@section('admin-content')
@php
  $perfil = $user->perfil;
  $tab    = request('tab', 'perfil');

  $campos = ['dni','apellido_paterno','apellido_materno','nombres','cargo','area',
             'regimen_laboral','telefono_celular','email_institucional','bio'];
  $llenos = $perfil ? collect($campos)->filter(fn($c) => !empty($perfil->$c))->count() : 0;
  $pct    = (int) round($llenos / count($campos) * 100);
  $colorPct = $pct >= 80 ? 'success' : ($pct >= 40 ? 'warning' : 'danger');
@endphp

{{-- ══════════════════════════════════════════════════════════
     HEADER — banner + avatar + nombre
══════════════════════════════════════════════════════════ --}}
<div class="row">
  <div class="col-12">
    <div class="card mb-6">
      <div class="user-profile-header-banner">
        <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner" class="rounded-top w-100" style="height:150px; object-fit:cover;" />
      </div>
      <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-4">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto ms-sm-6">
          <div class="position-relative d-inline-block">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
              class="d-block rounded user-profile-img border border-3 border-white"
              style="width:90px; height:90px; object-fit:cover;" />
            <span class="badge bg-{{ $user->status?->badgeClass() ?? 'secondary' }} position-absolute bottom-0 end-0 rounded-pill" style="font-size:.55rem; padding:.25rem .45rem;">
              {{ $user->status?->label() ?? '—' }}
            </span>
          </div>
        </div>
        <div class="flex-grow-1 mt-3 mt-lg-4 mx-5">
          <div class="d-flex align-items-md-end align-items-center justify-content-md-between flex-md-row flex-column gap-3">
            <div>
              <h5 class="mb-1">{{ $user->name }}</h5>
              <div class="d-flex flex-wrap gap-3 justify-content-sm-start justify-content-center">
                @if($perfil?->cargo)
                  <span class="text-muted small d-flex align-items-center gap-1">
                    <i class="icon-base ti tabler-briefcase icon-xs"></i> {{ $perfil->cargo }}
                  </span>
                @endif
                @if($perfil?->area)
                  <span class="text-muted small d-flex align-items-center gap-1">
                    <i class="icon-base ti tabler-building icon-xs"></i> {{ $perfil->area }}
                  </span>
                @endif
                @if($perfil?->departamento)
                  <span class="text-muted small d-flex align-items-center gap-1">
                    <i class="icon-base ti tabler-map-pin icon-xs"></i> {{ $perfil->departamento }}
                  </span>
                @endif
                <span class="text-muted small d-flex align-items-center gap-1">
                  <i class="icon-base ti tabler-calendar icon-xs"></i> Desde {{ formatDate($user->created_at) }}
                </span>
              </div>
            </div>
            <div class="d-flex flex-column align-items-md-end gap-1">
              <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">Perfil completado</span>
                <span class="badge bg-label-{{ $colorPct }} small">{{ $pct }}%</span>
              </div>
              <div class="progress" style="width:140px; height:5px;">
                <div class="progress-bar bg-{{ $colorPct }}" style="width:{{ $pct }}%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Tabs de navegación --}}
      <div class="px-5 pb-0">
        <ul class="nav nav-pills border-0 gap-1 mb-0 pb-0">
          <li class="nav-item">
            <a href="?tab=perfil" class="nav-link rounded-bottom-0 {{ $tab === 'perfil' ? 'active' : '' }}">
              <i class="icon-base ti tabler-user icon-sm me-1"></i> Mi Perfil
            </a>
          </li>
          <li class="nav-item">
            <a href="?tab=seguridad" class="nav-link rounded-bottom-0 {{ $tab === 'seguridad' ? 'active' : '' }}">
              <i class="icon-base ti tabler-shield-lock icon-sm me-1"></i> Seguridad
            </a>
          </li>
          <li class="nav-item">
            <a href="?tab=sesiones" class="nav-link rounded-bottom-0 {{ $tab === 'sesiones' ? 'active' : '' }}">
              <i class="icon-base ti tabler-devices icon-sm me-1"></i> Sesiones
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     TAB: MI PERFIL
══════════════════════════════════════════════════════════ --}}
@if($tab === 'perfil')
<form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="row g-6">

  {{-- Columna principal --}}
  <div class="col-xl-8 col-lg-7">

    {{-- Datos personales --}}
    <div class="card mb-6">
      <div class="card-header d-flex align-items-center gap-2">
        <i class="icon-base ti tabler-id-badge-2 text-primary"></i>
        <h5 class="card-title mb-0">Datos personales</h5>
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

    {{-- Datos laborales --}}
    <div class="card mb-6">
      <div class="card-header d-flex align-items-center gap-2">
        <i class="icon-base ti tabler-briefcase text-primary"></i>
        <h5 class="card-title mb-0">Datos laborales</h5>
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
            <label class="form-label" for="regimen_laboral">Régimen laboral</label>
            <select id="regimen_laboral" name="perfil[regimen_laboral]"
              class="form-select select2 @error('perfil.regimen_laboral') is-invalid @enderror"
              data-placeholder="Seleccionar">
              <option value=""></option>
              @foreach (['CAS','D. Leg. 276','D. Leg. 728','SPE','Comisionado'] as $reg)
                <option value="{{ $reg }}" @selected(old('perfil.regimen_laboral', $perfil?->regimen_laboral) === $reg)>{{ $reg }}</option>
              @endforeach
            </select>
            @error('perfil.regimen_laboral')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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

    {{-- Contacto --}}
    <div class="card mb-6">
      <div class="card-header d-flex align-items-center gap-2">
        <i class="icon-base ti tabler-address-book text-primary"></i>
        <h5 class="card-title mb-0">Contacto</h5>
      </div>
      <div class="card-body">
        <div class="row g-5">

          <div class="col-md-4">
            <label class="form-label" for="telefono_celular">Celular</label>
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

          <div class="col-12">
            <label class="form-label" for="bio">Presentación / Biografía</label>
            <textarea id="bio" name="perfil[bio]" rows="3"
              class="form-control @error('perfil.bio') is-invalid @enderror"
              placeholder="Breve descripción profesional...">{{ old('perfil.bio', $perfil?->bio) }}</textarea>
            <div class="form-text text-end"><span id="bio-count">{{ strlen($perfil?->bio ?? '') }}</span>/1000</div>
            @error('perfil.bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

        </div>
      </div>
    </div>

    <div class="d-flex gap-3 justify-content-end mb-6">
      <button type="reset" class="btn btn-label-secondary">Descartar</button>
      <button type="submit" class="btn btn-primary">
        <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar cambios
      </button>
    </div>

  </div>

  {{-- Sidebar --}}
  <div class="col-xl-4 col-lg-5">

    {{-- Foto --}}
    <div class="card mb-6">
      <div class="card-body text-center py-5">
        <div class="position-relative d-inline-block mb-4">
          <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
            id="avatar-preview"
            class="rounded-circle object-fit-cover border border-2"
            style="width:96px; height:96px;" />
          <label for="avatar"
            class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle d-flex align-items-center justify-content-center"
            style="width:30px; height:30px; padding:0;" title="Cambiar foto">
            <i class="icon-base ti tabler-camera" style="font-size:.85rem;"></i>
            <input type="file" id="avatar" name="avatar" hidden accept="image/png,image/jpeg,image/webp" />
          </label>
        </div>
        <h6 class="mb-0">{{ $user->name }}</h6>
        <p class="text-muted small mb-1">{{ $user->email }}</p>
        <span class="badge bg-label-primary">{{ $user->roles->first()?->name ?? 'Sin rol' }}</span>
        @error('avatar')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- Acceso al sistema --}}
    <div class="card mb-6">
      <div class="card-header d-flex align-items-center gap-2">
        <i class="icon-base ti tabler-lock text-primary"></i>
        <h6 class="card-title mb-0">Acceso al sistema</h6>
      </div>
      <div class="card-body">
        <div class="mb-4">
          <label class="form-label">Correo electrónico</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-mail"></i></span>
            <input type="email" class="form-control bg-body-secondary" value="{{ $user->email }}" disabled />
          </div>
          <div class="form-text">Para cambiar el correo, contacta al administrador.</div>
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
          <div class="form-text">Solo letras, números, guiones y puntos.</div>
        </div>

        <div>
          <label class="form-label" for="phone">Teléfono personal</label>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="icon-base ti tabler-phone"></i></span>
            <input type="text" id="phone" name="phone"
              class="form-control @error('phone') is-invalid @enderror"
              value="{{ old('phone', $user->phone) }}" />
          </div>
          @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>

    {{-- Info cuenta --}}
    <div class="card">
      <div class="card-body">
        <p class="text-uppercase text-muted small fw-semibold mb-3">Información de cuenta</p>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted small">Registrado</span>
          <span class="small fw-medium">{{ formatDate($user->created_at) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted small">Última actualización</span>
          <span class="small fw-medium">{{ formatDate($user->updated_at) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span class="text-muted small">Estado</span>
          {!! statusBadge($user->status) !!}
        </div>
        <hr class="my-3">
        <p class="small mb-1 d-flex justify-content-between">
          <span class="text-muted">Perfil completado</span>
          <span class="fw-semibold text-{{ $colorPct }}">{{ $pct }}%</span>
        </p>
        <div class="progress mb-1" style="height:6px;">
          <div class="progress-bar bg-{{ $colorPct }}" style="width:{{ $pct }}%"></div>
        </div>
        <p class="text-muted small mb-0">{{ $llenos }} de {{ count($campos) }} campos completados</p>
      </div>
    </div>

  </div>
</div>
</form>
@endif

{{-- ══════════════════════════════════════════════════════════
     TAB: SEGURIDAD
══════════════════════════════════════════════════════════ --}}
@if($tab === 'seguridad')
<div class="row g-6">
  <div class="col-xl-8 col-lg-7">

    {{-- Cambiar contraseña (Jetstream Livewire) --}}
    <div class="card mb-6">
      <div class="card-header d-flex align-items-center gap-2">
        <i class="icon-base ti tabler-key text-primary"></i>
        <h5 class="card-title mb-0">Actualizar contraseña</h5>
      </div>
      <div class="card-body">
        <p class="text-muted small mb-4">Usa una contraseña larga y aleatoria para mantener tu cuenta segura.</p>
        @livewire('profile.update-password-form')
      </div>
    </div>

    {{-- 2FA (Jetstream Livewire) --}}
    <div class="card">
      <div class="card-header d-flex align-items-center gap-2">
        <i class="icon-base ti tabler-shield-check text-primary"></i>
        <h5 class="card-title mb-0">Autenticación de dos factores</h5>
      </div>
      <div class="card-body">
        <p class="text-muted small mb-4">Agrega una capa extra de seguridad mediante un código de tu aplicación autenticadora.</p>
        @livewire('profile.two-factor-authentication-form')
      </div>
    </div>

  </div>

  <div class="col-xl-4 col-lg-5">
    <div class="card">
      <div class="card-body text-center py-5">
        <div class="avatar avatar-xl mb-3">
          <span class="avatar-initial rounded-circle bg-label-warning">
            <i class="icon-base ti tabler-shield icon-lg"></i>
          </span>
        </div>
        <h6 class="mb-1">Seguridad de cuenta</h6>
        <p class="text-muted small mb-3">Protege tu cuenta con una contraseña fuerte y la autenticación de dos factores.</p>
        <div class="text-start">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="icon-base ti tabler-check text-success icon-sm"></i>
            <span class="small">Correo verificado</span>
          </div>
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="icon-base ti tabler-{{ auth()->user()->two_factor_confirmed_at ? 'check text-success' : 'x text-danger' }} icon-sm"></i>
            <span class="small">2FA {{ auth()->user()->two_factor_confirmed_at ? 'habilitado' : 'deshabilitado' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════
     TAB: SESIONES
══════════════════════════════════════════════════════════ --}}
@if($tab === 'sesiones')
<div class="row g-6">
  <div class="col-xl-8 col-lg-7">

    {{-- Sesiones del navegador --}}
    <div class="card mb-6">
      <div class="card-header d-flex align-items-center gap-2">
        <i class="icon-base ti tabler-devices text-primary"></i>
        <h5 class="card-title mb-0">Sesiones activas</h5>
      </div>
      <div class="card-body">
        <p class="text-muted small mb-4">Administra y cierra tus sesiones en otros navegadores y dispositivos.</p>
        @livewire('profile.logout-other-browser-sessions-form')
      </div>
    </div>

    {{-- Eliminar cuenta --}}
    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
    <div class="card border-danger">
      <div class="card-header d-flex align-items-center gap-2 border-bottom border-danger">
        <i class="icon-base ti tabler-alert-triangle text-danger"></i>
        <h5 class="card-title mb-0 text-danger">Zona de peligro</h5>
      </div>
      <div class="card-body">
        <p class="text-muted small mb-4">Una vez que elimines tu cuenta, todos sus datos serán eliminados permanentemente. Descarga cualquier información importante antes de proceder.</p>
        @livewire('profile.delete-user-form')
      </div>
    </div>
    @endif

  </div>

  <div class="col-xl-4 col-lg-5">
    <div class="card">
      <div class="card-body text-center py-5">
        <div class="avatar avatar-xl mb-3">
          <span class="avatar-initial rounded-circle bg-label-info">
            <i class="icon-base ti tabler-devices icon-lg"></i>
          </span>
        </div>
        <h6 class="mb-1">Control de sesiones</h6>
        <p class="text-muted small">Si detectas actividad sospechosa en tu cuenta, cierra todas las otras sesiones y cambia tu contraseña inmediatamente.</p>
      </div>
    </div>
  </div>
</div>
@endif

@endsection

@section('admin-page-script')
<script>
window.addEventListener('load', function () {
  // Select2
  const s2Els = document.querySelectorAll('.select2');
  if (s2Els.length && typeof $ !== 'undefined') {
    $('#regimen_laboral').select2({ dropdownParent: $('body') });
  }

  // Flatpickr
  document.querySelectorAll('.flatpickr-date').forEach(el => {
    flatpickr(el, { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y', allowInput: true });
  });

  // Preview avatar
  const avatarInput = document.getElementById('avatar');
  if (avatarInput) {
    avatarInput.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatar-preview').src = e.target.result;
        reader.readAsDataURL(file);
      }
    });
  }

  // Contador bio
  const bioEl = document.getElementById('bio');
  const bioCount = document.getElementById('bio-count');
  if (bioEl && bioCount) {
    bioEl.addEventListener('input', () => bioCount.textContent = bioEl.value.length);
  }
});
</script>
@endsection
