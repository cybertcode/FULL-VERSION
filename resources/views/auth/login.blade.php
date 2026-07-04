@php
use Illuminate\Support\Facades\Route;
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
$captchaEnabled = setting('captcha_enabled', false);
$captchaSiteKey = config('services.recaptcha.site_key', setting('captcha_site_key', ''));
@endphp

@extends('layouts/blankLayout')

@section('title', 'Iniciar sesión')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@if($captchaEnabled && $captchaSiteKey)
@section('vendor-script')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection
@endif

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
          <h4 class="mb-1">Bienvenido a {{ setting('site_name', config('variables.templateName')) }}! 👋</h4>
          <p class="mb-6">Inicia sesión en tu cuenta para continuar</p>

          @if(session('status'))
          <div class="alert alert-success mb-4 rounded" role="alert">{{ session('status') }}</div>
          @endif

          <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="POST">
            @csrf

          <div class="mb-6">
            <label for="login-email" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
              id="login-email" name="email"
              placeholder="tu@correo.com" autofocus value="{{ old('email') }}" />
            @error('email')
            <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
            @enderror
          </div>

          <div class="mb-6 form-password-toggle">
            <label class="form-label d-flex justify-content-between" for="login-password">
              Contraseña
              @if(Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="small">¿Olvidaste tu contraseña?</a>
              @endif
            </label>
            <div class="input-group input-group-merge @error('password') is-invalid @enderror">
              <input type="password" id="login-password" class="form-control @error('password') is-invalid @enderror"
                name="password"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                aria-describedby="password" />
              <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
            </div>
            @error('password')
            <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
            @enderror
          </div>

          <div class="mb-6 ms-2">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember-me" name="remember"
                {{ old('remember') ? 'checked' : '' }} />
              <label class="form-check-label" for="remember-me">Recordarme</label>
            </div>
          </div>

          {{-- reCAPTCHA v2 — solo si está habilitado en Settings --}}
          @if($captchaEnabled && $captchaSiteKey)
          <div class="mb-6">
            <div class="g-recaptcha" data-sitekey="{{ $captchaSiteKey }}"></div>
            @error('g-recaptcha-response')
            <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>
          @endif

            <button class="btn btn-primary d-grid w-100" type="submit">Iniciar sesión</button>
          </form>

          @if(Route::has('register'))
          <p class="text-center">
            <span>¿No tienes cuenta?</span>
            <a href="{{ route('register') }}">Crear cuenta</a>
          </p>
          @endif

          <div class="divider my-6">
            <div class="divider-text">o</div>
          </div>

          <div class="d-flex justify-content-center">
            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-facebook me-1_5">
              <i class="icon-base ti tabler-brand-facebook-filled icon-20px"></i>
            </a>
            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-twitter me-1_5">
              <i class="icon-base ti tabler-brand-twitter-filled icon-20px"></i>
            </a>
            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-github me-1_5">
              <i class="icon-base ti tabler-brand-github-filled icon-20px"></i>
            </a>
            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-google-plus">
              <i class="icon-base ti tabler-brand-google-filled icon-20px"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
