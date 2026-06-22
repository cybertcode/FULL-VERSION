@extends('admin/layouts/master')

@section('title', 'Perfil de ' . $user->name)

@section('admin-content')

<x-breadcrumb :items="[
  ['label' => 'Usuarios', 'url' => route('admin.users.index')],
  ['label' => $user->name],
]" />

@php
  $perfil = $user->perfil;
  $rol    = $user->roles->first()?->name ?? '—';
@endphp

{{-- Banner + cabecera de perfil --}}
<div class="row">
  <div class="col-12">
    <div class="card mb-6">
      {{-- Banner --}}
      <div class="user-profile-header-banner">
        <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="banner"
          class="rounded-top w-100" style="max-height:200px; object-fit:cover;" />
      </div>

      {{-- Avatar + nombre + acciones --}}
      <div class="card-body">
        <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
          <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
              class="d-block h-auto ms-0 ms-sm-4 rounded-3 user-profile-img"
              style="width:100px; height:100px; object-fit:cover; margin-top:-50px; border: 4px solid var(--bs-card-bg);" />
          </div>
          <div class="flex-grow-1 mt-3 mt-sm-1 ms-0 ms-sm-4">
            <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
              <div>
                <h4 class="mb-1">{{ $user->name }}</h4>
                <div class="d-flex flex-wrap gap-3">
                  @if ($perfil?->cargo)
                    <span class="d-flex align-items-center gap-1 text-muted">
                      <i class="icon-base ti tabler-briefcase icon-sm"></i>
                      <span>{{ $perfil->cargo }}</span>
                    </span>
                  @endif
                  @if ($perfil?->area)
                    <span class="d-flex align-items-center gap-1 text-muted">
                      <i class="icon-base ti tabler-building icon-sm"></i>
                      <span>{{ $perfil->area }}</span>
                    </span>
                  @endif
                  @if ($perfil?->departamento)
                    <span class="d-flex align-items-center gap-1 text-muted">
                      <i class="icon-base ti tabler-map-pin icon-sm"></i>
                      <span>{{ $perfil->departamento }}, Perú</span>
                    </span>
                  @endif
                  <span class="d-flex align-items-center gap-1 text-muted">
                    <i class="icon-base ti tabler-calendar icon-sm"></i>
                    <span>Registrado {{ $user->created_at->translatedFormat('F Y') }}</span>
                  </span>
                </div>
              </div>
              <div class="d-flex gap-2">
                @can('update', $user)
                  <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                    <i class="icon-base ti tabler-pencil me-1"></i> Editar
                  </a>
                @endcan
                <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary">
                  <i class="icon-base ti tabler-arrow-left me-1"></i> Volver
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  {{-- Columna izquierda: About --}}
  <div class="col-xl-4 col-lg-5 col-md-5">

    {{-- About --}}
    <div class="card mb-6">
      <div class="card-body">
        <h5 class="pb-4 border-bottom">Acerca de</h5>
        <dl class="row mt-4 g-3">
          <dt class="col-sm-5 text-muted fw-normal">
            <i class="icon-base ti tabler-user icon-sm me-1"></i> Nombre completo
          </dt>
          <dd class="col-sm-7 fw-medium">{{ $user->name }}</dd>

          @if ($perfil?->apellido_paterno || $perfil?->apellido_materno)
            <dt class="col-sm-5 text-muted fw-normal">
              <i class="icon-base ti tabler-id-badge-2 icon-sm me-1"></i> Apellidos
            </dt>
            <dd class="col-sm-7 fw-medium">
              {{ trim($perfil->apellido_paterno . ' ' . $perfil->apellido_materno) }}
            </dd>
          @endif

          @if ($perfil?->dni)
            <dt class="col-sm-5 text-muted fw-normal">
              <i class="icon-base ti tabler-id icon-sm me-1"></i> DNI
            </dt>
            <dd class="col-sm-7 fw-medium">{{ $perfil->dni }}</dd>
          @endif

          <dt class="col-sm-5 text-muted fw-normal">
            <i class="icon-base ti tabler-shield icon-sm me-1"></i> Rol
          </dt>
          <dd class="col-sm-7">
            <span class="badge bg-label-primary">{{ ucfirst($rol) }}</span>
          </dd>

          <dt class="col-sm-5 text-muted fw-normal">
            <i class="icon-base ti tabler-activity icon-sm me-1"></i> Estado
          </dt>
          <dd class="col-sm-7">{!! statusBadge($user->status) !!}</dd>

          @if ($perfil?->sexo)
            <dt class="col-sm-5 text-muted fw-normal">
              <i class="icon-base ti tabler-gender-bigender icon-sm me-1"></i> Sexo
            </dt>
            <dd class="col-sm-7 fw-medium">{{ $perfil->sexo === 'M' ? 'Masculino' : 'Femenino' }}</dd>
          @endif

          @if ($perfil?->fecha_nacimiento)
            <dt class="col-sm-5 text-muted fw-normal">
              <i class="icon-base ti tabler-cake icon-sm me-1"></i> Nacimiento
            </dt>
            <dd class="col-sm-7 fw-medium">{{ $perfil->fecha_nacimiento->translatedFormat('d F Y') }}</dd>
          @endif

          @if ($perfil?->nacionalidad)
            <dt class="col-sm-5 text-muted fw-normal">
              <i class="icon-base ti tabler-flag icon-sm me-1"></i> Nacionalidad
            </dt>
            <dd class="col-sm-7 fw-medium">{{ $perfil->nacionalidad }}</dd>
          @endif
        </dl>
      </div>
    </div>

    {{-- Datos laborales --}}
    @if ($perfil && ($perfil->cargo || $perfil->area || $perfil->fecha_ingreso || $perfil->codigo_empleado))
    <div class="card mb-6">
      <div class="card-body">
        <h5 class="pb-4 border-bottom">
          <i class="icon-base ti tabler-briefcase me-2 text-primary"></i>Datos laborales
        </h5>
        <dl class="row mt-4 g-3">
          @if ($perfil->cargo)
            <dt class="col-sm-5 text-muted fw-normal">Cargo</dt>
            <dd class="col-sm-7 fw-medium">{{ $perfil->cargo }}</dd>
          @endif
          @if ($perfil->area)
            <dt class="col-sm-5 text-muted fw-normal">Área / Unidad</dt>
            <dd class="col-sm-7 fw-medium">{{ $perfil->area }}</dd>
          @endif
          @if ($perfil->fecha_ingreso)
            <dt class="col-sm-5 text-muted fw-normal">Ingreso</dt>
            <dd class="col-sm-7 fw-medium">{{ $perfil->fecha_ingreso->translatedFormat('d F Y') }}</dd>
          @endif
          @if ($perfil->codigo_empleado)
            <dt class="col-sm-5 text-muted fw-normal">Código</dt>
            <dd class="col-sm-7"><code>{{ $perfil->codigo_empleado }}</code></dd>
          @endif
        </dl>
      </div>
    </div>
    @endif

    {{-- Contacto --}}
    <div class="card mb-6">
      <div class="card-body">
        <h5 class="pb-4 border-bottom">
          <i class="icon-base ti tabler-address-book me-2 text-primary"></i>Contacto
        </h5>
        <ul class="list-unstyled mt-4 mb-0">
          <li class="d-flex align-items-center mb-4">
            <i class="icon-base ti tabler-mail me-2 text-muted"></i>
            <span class="fw-medium me-2">Email:</span>
            <a href="mailto:{{ $user->email }}" class="text-truncate">{{ $user->email }}</a>
          </li>
          @if ($user->phone)
            <li class="d-flex align-items-center mb-4">
              <i class="icon-base ti tabler-phone me-2 text-muted"></i>
              <span class="fw-medium me-2">Teléfono:</span>
              <span>{{ $user->phone }}</span>
            </li>
          @endif
          @if ($perfil?->email_institucional)
            <li class="d-flex align-items-center mb-4">
              <i class="icon-base ti tabler-mail-forward me-2 text-muted"></i>
              <span class="fw-medium me-2">Institucional:</span>
              <a href="mailto:{{ $perfil->email_institucional }}" class="text-truncate">{{ $perfil->email_institucional }}</a>
            </li>
          @endif
          @if ($perfil?->telefono_celular)
            <li class="d-flex align-items-center mb-4">
              <i class="icon-base ti tabler-device-mobile me-2 text-muted"></i>
              <span class="fw-medium me-2">Celular:</span>
              <span>{{ $perfil->telefono_celular }}</span>
            </li>
          @endif
          @if ($perfil?->telefono_fijo)
            <li class="d-flex align-items-center mb-4">
              <i class="icon-base ti tabler-phone me-2 text-muted"></i>
              <span class="fw-medium me-2">Fijo:</span>
              <span>{{ $perfil->telefono_fijo }}</span>
              @if ($perfil->anexo)
                <span class="ms-1 text-muted">Anexo {{ $perfil->anexo }}</span>
              @endif
            </li>
          @endif
          @if ($perfil?->linkedin)
            <li class="d-flex align-items-center mb-4">
              <i class="icon-base ti tabler-brand-linkedin me-2 text-muted"></i>
              <span class="fw-medium me-2">LinkedIn:</span>
              <a href="{{ $perfil->linkedin }}" target="_blank" rel="noopener" class="text-truncate">
                Ver perfil <i class="icon-base ti tabler-external-link icon-sm"></i>
              </a>
            </li>
          @endif
          @if ($perfil?->departamento || $perfil?->provincia || $perfil?->distrito)
            <li class="d-flex align-items-center">
              <i class="icon-base ti tabler-map-pin me-2 text-muted"></i>
              <span class="fw-medium me-2">Ubicación:</span>
              <span>{{ collect([$perfil->distrito, $perfil->provincia, $perfil->departamento])->filter()->implode(', ') }}</span>
            </li>
          @endif
        </ul>
      </div>
    </div>

  </div>

  {{-- Columna derecha: Bio + actividad --}}
  <div class="col-xl-8 col-lg-7 col-md-7">

    {{-- Biografía --}}
    @if ($perfil?->bio)
    <div class="card mb-6">
      <div class="card-body">
        <h5 class="pb-4 border-bottom">
          <i class="icon-base ti tabler-file-description me-2 text-primary"></i>Presentación
        </h5>
        <p class="mt-4 mb-0">{{ $perfil->bio }}</p>
      </div>
    </div>
    @endif

    {{-- Resumen estadístico --}}
    <div class="row g-6 mb-6">
      <div class="col-sm-4">
        <div class="card text-center">
          <div class="card-body">
            <div class="avatar avatar-lg mx-auto mb-3">
              <div class="avatar-initial rounded-circle bg-label-primary">
                <i class="icon-base ti tabler-shield-check icon-lg"></i>
              </div>
            </div>
            <h4 class="mb-1">{{ ucfirst($rol) }}</h4>
            <span class="text-muted">Rol del sistema</span>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card text-center">
          <div class="card-body">
            <div class="avatar avatar-lg mx-auto mb-3">
              <div class="avatar-initial rounded-circle bg-label-success">
                <i class="icon-base ti tabler-calendar-check icon-lg"></i>
              </div>
            </div>
            <h4 class="mb-1">{{ $user->created_at->diffForHumans(['parts' => 1, 'short' => true]) }}</h4>
            <span class="text-muted">En el sistema</span>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card text-center">
          <div class="card-body">
            <div class="avatar avatar-lg mx-auto mb-3">
              @php
                $campos = ['dni','apellido_paterno','cargo','area','telefono_celular','email_institucional','departamento','bio'];
                $llenos = $perfil ? collect($campos)->filter(fn($c) => !empty($perfil->$c))->count() : 0;
                $pct    = count($campos) > 0 ? (int) round($llenos / count($campos) * 100) : 0;
                $colorPct = $pct >= 80 ? 'success' : ($pct >= 40 ? 'warning' : 'danger');
              @endphp
              <div class="avatar-initial rounded-circle bg-label-{{ $colorPct }}">
                <i class="icon-base ti tabler-user-check icon-lg"></i>
              </div>
            </div>
            <h4 class="mb-1">{{ $pct }}%</h4>
            <span class="text-muted">Perfil completado</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Datos de cuenta --}}
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          <i class="icon-base ti tabler-info-circle me-2 text-primary"></i>Datos de cuenta
        </h5>
        @can('update', $user)
          <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-label-primary">
            <i class="icon-base ti tabler-pencil me-1"></i> Editar
          </a>
        @endcan
      </div>
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6">
            <p class="text-muted small mb-1">Nombre de usuario</p>
            <p class="fw-medium mb-0">{{ $user->name }}</p>
          </div>
          <div class="col-md-6">
            <p class="text-muted small mb-1">Correo electrónico</p>
            <p class="fw-medium mb-0">{{ $user->email }}</p>
          </div>
          <div class="col-md-6">
            <p class="text-muted small mb-1">Estado de cuenta</p>
            <p class="mb-0">{!! statusBadge($user->status) !!}</p>
          </div>
          <div class="col-md-6">
            <p class="text-muted small mb-1">Email verificado</p>
            @if ($user->email_verified_at)
              <p class="fw-medium mb-0 text-success">
                <i class="icon-base ti tabler-circle-check me-1"></i>
                {{ formatDate($user->email_verified_at) }}
              </p>
            @else
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="text-warning fw-medium">
                  <i class="icon-base ti tabler-circle-x me-1"></i>Sin verificar
                </span>
                @can('users.edit')
                <button type="button" class="btn btn-xs btn-label-success py-1 px-2"
                  id="btnVerifyEmail"
                  data-verify-url="{{ route('admin.users.verify-email', $user) }}"
                  title="Marcar como verificado sin enviar email">
                  <i class="icon-base ti tabler-mail-check icon-14px me-1"></i>Verificar
                </button>
                <button type="button" class="btn btn-xs btn-label-info py-1 px-2"
                  id="btnResendVerify"
                  data-resend-url="{{ route('admin.users.resend-verification', $user) }}"
                  title="Enviar correo de verificación al usuario">
                  <i class="icon-base ti tabler-send icon-14px me-1"></i>Reenviar
                </button>
                @endcan
              </div>
            @endif
          </div>
          <div class="col-md-6">
            <p class="text-muted small mb-1">Último acceso</p>
            <p class="fw-medium mb-0">
              @if ($user->last_login_at)
                <span title="{{ formatDateTime($user->last_login_at) }}">
                  <i class="icon-base ti tabler-clock me-1 text-muted"></i>
                  {{ $user->last_login_at->diffForHumans() }}
                </span>
                @if ($user->last_login_ip)
                  <br><small class="text-muted"><i class="icon-base ti tabler-network icon-12px me-1"></i>{{ $user->last_login_ip }}</small>
                @endif
              @else
                <span class="text-muted fst-italic">Nunca ha ingresado</span>
              @endif
            </p>
          </div>
          <div class="col-md-6">
            <p class="text-muted small mb-1">Registrado</p>
            <p class="fw-medium mb-0">{{ formatDateTime($user->created_at) }}</p>
          </div>
          <div class="col-md-6">
            <p class="text-muted small mb-1">Última actualización</p>
            <p class="fw-medium mb-0">{{ formatDateTime($user->updated_at) }}</p>
          </div>
          @if ($perfil?->ubigeo)
            <div class="col-md-6">
              <p class="text-muted small mb-1">Ubigeo INEI</p>
              <p class="fw-medium mb-0"><code>{{ $perfil->ubigeo }}</code></p>
            </div>
          @endif
          @if ($perfil?->direccion)
            <div class="col-12">
              <p class="text-muted small mb-1">Dirección</p>
              <p class="fw-medium mb-0">{{ $perfil->direccion }}</p>
            </div>
          @endif
        </div>
      </div>
    </div>

  </div>
</div>

@endsection

@section('admin-page-script')
<script>
'use strict';
document.addEventListener('DOMContentLoaded', function () {

  function jsonPost(url) {
    return fetch(url, {
      method : 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    }).then(r => r.json());
  }

  // Verificar email manualmente
  document.getElementById('btnVerifyEmail')?.addEventListener('click', function () {
    confirmAction({
      title      : '¿Verificar email?',
      text       : 'Se marcará el email como verificado sin enviar ningún correo.',
      confirmText: 'Sí, verificar',
      cancelText : 'Cancelar',
      isDanger   : false,
      onConfirm  : () => jsonPost(this.dataset.verifyUrl)
        .then(d => { showToast('success', d.message ?? 'Email verificado.'); setTimeout(() => location.reload(), 1200); })
        .catch(() => showToast('error', 'No se pudo verificar el email.')),
    });
  });

  // Reenviar correo de verificación
  document.getElementById('btnResendVerify')?.addEventListener('click', function () {
    jsonPost(this.dataset.resendUrl)
      .then(d => showToast('success', d.message ?? 'Correo enviado.'))
      .catch(() => showToast('error', 'No se pudo enviar el correo.'));
  });

});
</script>
@endsection
