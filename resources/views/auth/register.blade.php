@php
use Illuminate\Support\Facades\Route;
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Crear cuenta')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover">
  <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
    <span class="app-brand-logo demo">@include('_partials.macros')</span>
    <span class="app-brand-text demo text-heading fw-bold">{{ setting('site_name', config('variables.templateName')) }}</span>
  </a>

  <div class="authentication-inner row m-0">
    <div class="d-none d-xl-flex col-xl-8 p-0">
      <div class="auth-cover-bg d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/auth-register-illustration-' . $configData['theme'] . '.png') }}"
          alt="registro" class="my-5 auth-illustration"
          data-app-light-img="illustrations/auth-register-illustration-light.png"
          data-app-dark-img="illustrations/auth-register-illustration-dark.png" />
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}"
          alt="" class="platform-bg"
          data-app-light-img="illustrations/bg-shape-image-light.png"
          data-app-dark-img="illustrations/bg-shape-image-dark.png" />
      </div>
    </div>

    <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
      <div class="w-px-400 mx-auto mt-12 pt-5">
        <h4 class="mb-1">Crear una cuenta 🚀</h4>
        <p class="mb-6">Completa los datos para registrarte en el sistema.</p>

        <form id="formAuthentication" class="mb-6" action="{{ route('register') }}" method="POST">
          @csrf

          <div class="mb-5">
            <label for="name" class="form-label">Nombre completo</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
              id="name" name="name" placeholder="Juan García"
              autofocus value="{{ old('name') }}" />
            @error('name')
              <span class="invalid-feedback"><span class="fw-medium">{{ $message }}</span></span>
            @enderror
          </div>

          <div class="mb-5">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
              id="email" name="email" placeholder="juan@ejemplo.com"
              value="{{ old('email') }}" />
            @error('email')
              <span class="invalid-feedback"><span class="fw-medium">{{ $message }}</span></span>
            @enderror
          </div>

          <div class="mb-5 form-password-toggle">
            <label class="form-label" for="password">Contraseña</label>
            <div class="input-group input-group-merge @error('password') is-invalid @enderror">
              <input type="password" id="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password" placeholder="Mínimo 8 caracteres" />
              <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
            </div>
            @error('password')
              <span class="invalid-feedback"><span class="fw-medium">{{ $message }}</span></span>
            @enderror
          </div>

          <div class="mb-5 form-password-toggle">
            <label class="form-label" for="password-confirm">Confirmar contraseña</label>
            <div class="input-group input-group-merge">
              <input type="password" id="password-confirm" class="form-control"
                name="password_confirmation" placeholder="Repite tu contraseña" />
              <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
            </div>
          </div>

          @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
          <div class="mb-6 mt-8">
            <div class="form-check mb-8 ms-2 @error('terms') is-invalid @enderror">
              <input class="form-check-input @error('terms') is-invalid @enderror"
                type="checkbox" id="terms" name="terms" />
              <label class="form-check-label" for="terms">
                Acepto la
                <a href="{{ setting('privacy_url', route('policy.show')) }}" target="_blank">política de privacidad</a>
                y los
                <a href="{{ setting('terms_url', route('terms.show')) }}" target="_blank">términos de uso</a>
              </label>
            </div>
            @error('terms')
              <div class="invalid-feedback"><span class="fw-medium">{{ $message }}</span></div>
            @enderror
          </div>
          @endif

          <button type="submit" class="btn btn-primary d-grid w-100">Crear cuenta</button>
        </form>

        <p class="text-center">
          <span>¿Ya tienes cuenta?</span>
          @if (Route::has('login'))
            <a href="{{ route('login') }}"> Inicia sesión</a>
          @endif
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
