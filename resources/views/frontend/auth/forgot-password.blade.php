@php
  $configData = Helper::appClasses();
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Recuperar contraseña')

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

          <h4 class="mb-1">¿Olvidaste tu contraseña? 🔒</h4>
          <p class="mb-6">Escribe tu correo y te enviaremos un enlace para restablecerla</p>

          @if(session('status'))
          <div class="alert alert-success mb-4 rounded" role="alert">{{ session('status') }}</div>
          @endif

          <form class="mb-6" action="{{ route('cuenta.password.email') }}" method="POST">
            @csrf

            <div class="mb-6">
              <label for="customer-forgot-email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror"
                id="customer-forgot-email" name="email"
                placeholder="tu@correo.com" autofocus value="{{ old('email') }}" />
              @error('email')
              <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
              @enderror
            </div>

            <button class="btn btn-primary d-grid w-100 mb-6" type="submit">Enviar enlace de recuperación</button>

            <div class="text-center">
              <a href="{{ route('cuenta.login') }}" class="d-flex align-items-center justify-content-center">
                <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl me-1"></i>
                Volver a iniciar sesión
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
