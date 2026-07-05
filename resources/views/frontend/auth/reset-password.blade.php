@php
  $configData = Helper::appClasses();
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Restablecer contraseña')

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

          <h4 class="mb-1">Restablecer contraseña 🔑</h4>
          <p class="mb-6">Ingresa tu nueva contraseña</p>

          <form class="mb-6" action="{{ route('cuenta.password.store') }}" method="POST">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-6">
              <label for="customer-reset-email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror"
                id="customer-reset-email" name="email"
                placeholder="tu@correo.com" value="{{ old('email', $email) }}" />
              @error('email')
              <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
              @enderror
            </div>

            <div class="mb-6 form-password-toggle">
              <label class="form-label" for="customer-reset-password">Nueva contraseña</label>
              <div class="input-group input-group-merge @error('password') is-invalid @enderror">
                <input type="password" id="customer-reset-password" class="form-control @error('password') is-invalid @enderror"
                  name="password"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="customer-reset-password" />
                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
              </div>
              @error('password')
              <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
              @enderror
            </div>

            <div class="mb-6 form-password-toggle">
              <label class="form-label" for="customer-reset-password-confirm">Confirmar contraseña</label>
              <div class="input-group input-group-merge">
                <input type="password" id="customer-reset-password-confirm" class="form-control"
                  name="password_confirmation"
                  placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="customer-reset-password-confirm" />
                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
              </div>
            </div>

            <button class="btn btn-primary d-grid w-100" type="submit">Restablecer contraseña</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
