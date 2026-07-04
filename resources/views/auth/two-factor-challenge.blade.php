@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Verificación en dos pasos')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6">
      <div class="card">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-6">
            <a href="{{ url('/') }}" class="app-brand-link">
              <span class="app-brand-logo demo">@include('_partials.macros')</span>
              <span class="app-brand-text demo text-heading fw-bold">{{ setting('site_name', config('variables.templateName')) }}</span>
            </a>
          </div>
          <!-- /Logo -->
          <h4 class="mb-1">Verificación en dos pasos 💬</h4>

          <div x-data="{ recovery: false }">

            <p class="mb-6" x-show="! recovery">
              Ingresa el código de 6 dígitos generado por tu aplicación de autenticación.
            </p>
            <p class="mb-6" x-show="recovery">
              Ingresa uno de tus códigos de recuperación de emergencia.
            </p>

            @if ($errors->any())
            <div class="alert alert-danger mb-4 rounded" role="alert">
              <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('two-factor.login') }}">
              @csrf

              {{-- Código TOTP --}}
              <div class="mb-6" x-show="! recovery">
                <label for="code" class="form-label">Código de autenticación</label>
                <input
                  type="text"
                  id="code"
                  name="code"
                  class="form-control @error('code') is-invalid @enderror"
                  inputmode="numeric"
                  autocomplete="one-time-code"
                  x-ref="code"
                  autofocus
                  placeholder="000000" />
                @error('code')
                <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
                @enderror
              </div>

              <div class="mb-6 ms-2" x-show="! recovery">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="remember_device" name="remember_device" value="1">
                  <label class="form-check-label" for="remember_device">Recordar este dispositivo por 30 días</label>
                </div>
              </div>

              {{-- Código de recuperación --}}
              <div class="mb-6" x-show="recovery" x-cloak>
                <label for="recovery_code" class="form-label">Código de recuperación</label>
                <input
                  type="text"
                  id="recovery_code"
                  name="recovery_code"
                  class="form-control @error('recovery_code') is-invalid @enderror"
                  autocomplete="one-time-code"
                  x-ref="recovery_code"
                  placeholder="xxxx-xxxx-xxxx" />
                @error('recovery_code')
                <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
                @enderror
              </div>

              <div class="d-flex flex-column gap-2">
                <button type="submit" class="btn btn-primary d-grid w-100">Verificar e ingresar</button>

                <div x-show="! recovery">
                  <button
                    type="button"
                    class="btn btn-outline-secondary d-grid w-100"
                    x-on:click="recovery = true; $nextTick(() => { $refs.recovery_code.focus() })">
                    Usar código de recuperación
                  </button>
                </div>
                <div x-show="recovery" x-cloak>
                  <button
                    type="button"
                    class="btn btn-outline-secondary d-grid w-100"
                    x-on:click="recovery = false; $nextTick(() => { $refs.code.focus() })">
                    Usar código de autenticación
                  </button>
                </div>
              </div>
            </form>

            <p class="text-center text-body-secondary small mt-4 mb-2">
              ¿Perdiste tu dispositivo y tus códigos de recuperación? Contacta al administrador del sistema para restablecer tu verificación en dos pasos.
            </p>

            <div class="text-center">
              <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link text-body-secondary p-0 small">
                  Cerrar sesión
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
