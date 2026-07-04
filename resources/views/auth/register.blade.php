@php
use Illuminate\Support\Facades\Route;
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
$captchaEnabled = setting('captcha_enabled', false);
$captchaSiteKey = config('services.recaptcha.site_key', setting('recaptcha_site_key', ''));
$googleEnabled = setting('social_google_enabled', false);
$githubEnabled = setting('social_github_enabled', false);
$facebookEnabled = setting('social_facebook_enabled', false);
@endphp

@extends('layouts/blankLayout')

@section('title', 'Crear cuenta')

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
          <div class="app-brand justify-content-center mb-6">
            <a href="{{ url('/') }}" class="app-brand-link">
              <span class="app-brand-logo demo">@include('_partials.macros')</span>
              <span class="app-brand-text demo text-heading fw-bold">{{ setting('site_name', config('variables.templateName')) }}</span>
            </a>
          </div>

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

            {{-- reCAPTCHA v2 — solo si está habilitado en Settings --}}
            @if($captchaEnabled && $captchaSiteKey)
            <div class="mb-6">
              <div class="g-recaptcha" data-sitekey="{{ $captchaSiteKey }}"></div>
              @error('g-recaptcha-response')
              <div class="text-danger small mt-1">{{ $message }}</div>
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

          @if($googleEnabled || $githubEnabled || $facebookEnabled)
          <div class="divider my-6">
            <div class="divider-text">o regístrate con</div>
          </div>

          <div class="d-flex justify-content-center gap-2">
            @if($googleEnabled)
            <a href="{{ route('social.redirect', 'google') }}" class="btn btn-icon rounded-circle btn-text-google-plus" title="Registrarme con Google">
              <i class="icon-base ti tabler-brand-google-filled icon-20px"></i>
            </a>
            @endif
            @if($githubEnabled)
            <a href="{{ route('social.redirect', 'github') }}" class="btn btn-icon rounded-circle btn-text-github" title="Registrarme con GitHub">
              <i class="icon-base ti tabler-brand-github-filled icon-20px"></i>
            </a>
            @endif
            @if($facebookEnabled)
            <a href="{{ route('social.redirect', 'facebook') }}" class="btn btn-icon rounded-circle btn-text-facebook" title="Registrarme con Facebook">
              <i class="icon-base ti tabler-brand-facebook-filled icon-20px"></i>
            </a>
            @endif
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
