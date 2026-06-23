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
<div class="authentication-wrapper authentication-cover">
  <!-- Logo -->
  <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
    <span class="app-brand-logo demo">@include('_partials.macros')</span>
    <span class="app-brand-text demo text-heading fw-bold">{{ setting('site_name', config('variables.templateName')) }}</span>
  </a>
  <!-- /Logo -->
  <div class="authentication-inner row m-0">
    <!-- Ilustración izquierda -->
    <div class="d-none d-xl-flex col-xl-8 p-0">
      <div class="auth-cover-bg d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/auth-two-step-illustration-' . $configData['theme'] . '.png') }}"
          alt="dos-pasos" class="my-5 auth-illustration"
          data-app-light-img="illustrations/auth-two-step-illustration-light.png"
          data-app-dark-img="illustrations/auth-two-step-illustration-dark.png" />
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}"
          alt="" class="platform-bg"
          data-app-light-img="illustrations/bg-shape-image-light.png"
          data-app-dark-img="illustrations/bg-shape-image-dark.png" />
      </div>
    </div>
    <!-- /Ilustración izquierda -->

    <!-- Verificación dos pasos -->
    <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-6 p-sm-12">
      <div class="w-px-400 mx-auto mt-12 pt-5">
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

          <div class="text-center mt-4">
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
    <!-- /Verificación dos pasos -->
  </div>
</div>
@endsection
