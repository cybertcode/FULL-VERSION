@php
  $configData = Helper::appClasses();
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Iniciar sesión')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6">
      <div class="card">
        <div class="card-body">
          <div class="app-brand justify-content-center mb-6">
            <a href="{{ route('home') }}" class="app-brand-link">
              <span class="app-brand-logo demo">@include('_partials.macros')</span>
              <span class="app-brand-text demo text-heading fw-bold">{{ setting('site_name', config('variables.templateName')) }}</span>
            </a>
          </div>

          <h4 class="mb-1">Bienvenido 👋</h4>
          <p class="mb-6">Inicia sesión en tu cuenta para continuar</p>

          @if(session('status'))
          <div class="alert alert-success mb-4 rounded" role="alert">{{ session('status') }}</div>
          @endif

          <form class="mb-6" action="{{ route('cuenta.login') }}" method="POST">
            @csrf

            <div class="mb-6">
              <label for="customer-login-email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror"
                id="customer-login-email" name="email"
                placeholder="tu@correo.com" autofocus value="{{ old('email') }}" />
              @error('email')
              <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
              @enderror
            </div>

            <div class="mb-6 form-password-toggle">
              <label class="form-label d-flex justify-content-between" for="customer-login-password">
                Contraseña
                <a href="{{ route('cuenta.password.request') }}" class="small">¿Olvidaste tu contraseña?</a>
              </label>
              <div class="input-group input-group-merge @error('password') is-invalid @enderror">
                <input type="password" id="customer-login-password" class="form-control @error('password') is-invalid @enderror"
                  name="password"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="customer-login-password" />
                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
              </div>
              @error('password')
              <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
              @enderror
            </div>

            <div class="mb-6 ms-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="customer-remember-me" name="remember"
                  {{ old('remember') ? 'checked' : '' }} />
                <label class="form-check-label" for="customer-remember-me">Recordarme</label>
              </div>
            </div>

            <button class="btn btn-primary d-grid w-100" type="submit">Iniciar sesión</button>
          </form>

          <p class="text-center">
            <span>¿No tienes cuenta?</span>
            <a href="{{ route('cuenta.register') }}">Crear cuenta</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
