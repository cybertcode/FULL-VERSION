@php
  use Illuminate\Support\Facades\Storage;
  $configData = Helper::appClasses();
  $customizerHidden = 'customizer-hide';

  $socials = collect([
      'social_facebook'  => 'tabler-brand-facebook',
      'social_instagram' => 'tabler-brand-instagram',
      'social_twitter'   => 'tabler-brand-twitter',
      'social_linkedin'  => 'tabler-brand-linkedin',
      'social_youtube'   => 'tabler-brand-youtube',
      'social_tiktok'    => 'tabler-brand-tiktok',
  ])->map(fn ($icon, $key) => ['url' => setting($key), 'icon' => $icon])
    ->filter(fn ($s) => filled($s['url']));
@endphp

@extends('layouts/blankLayout')

@section('title', setting('site_name', 'Inicio'))

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
<style>
  .landing-wrapper { min-height: 100vh; display: flex; flex-direction: column; }
  .landing-hero    { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem 1.5rem; text-align: center; }
  .landing-logo    { max-height: 90px; max-width: 280px; object-fit: contain; }
  .landing-footer  { padding: 1.5rem; text-align: center; border-top: 1px solid var(--bs-border-color); }
</style>
@endsection

@section('content')
<div class="landing-wrapper">

  <div class="landing-hero">
    @if(setting('site_logo'))
      <img src="{{ Storage::url(setting('site_logo')) }}" alt="{{ setting('site_name') }}" class="landing-logo mb-6">
    @else
      <span class="avatar avatar-xl mb-6">
        <span class="avatar-initial rounded-circle bg-label-primary">
          <i class="icon-base ti tabler-building icon-32px"></i>
        </span>
      </span>
    @endif

    <h1 class="mb-3 fw-bold display-5">{{ setting('site_name', 'Mi Sistema') }}</h1>
    <p class="mb-8 text-body-secondary fs-5" style="max-width: 620px;">
      {{ setting('site_description', 'Bienvenido al sistema institucional.') }}
    </p>

    <div class="d-flex flex-wrap gap-3 justify-content-center">
      @auth
        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg">
          <i class="icon-base ti tabler-layout-dashboard me-2"></i>Ir al panel
        </a>
      @else
        @auth('customer')
          <a href="{{ route('cuenta.dashboard') }}" class="btn btn-primary btn-lg">
            <i class="icon-base ti tabler-user me-2"></i>Ir a mi cuenta
          </a>
        @else
          <a href="{{ route('cuenta.login') }}" class="btn btn-primary btn-lg">
            <i class="icon-base ti tabler-login me-2"></i>Iniciar sesión
          </a>
        @endauth
      @endauth
      @if(setting('company_website'))
        <a href="{{ setting('company_website') }}" target="_blank" rel="noopener" class="btn btn-label-secondary btn-lg">
          <i class="icon-base ti tabler-world me-2"></i>Sitio web
        </a>
      @endif
    </div>

    @if($socials->isNotEmpty())
      <div class="d-flex gap-2 justify-content-center mt-8">
        @foreach($socials as $social)
          <a href="{{ $social['url'] }}" target="_blank" rel="noopener" class="btn btn-icon btn-text-secondary rounded-pill">
            <i class="icon-base ti {{ $social['icon'] }} icon-22px"></i>
          </a>
        @endforeach
      </div>
    @endif
  </div>

  <footer class="landing-footer">
    <small class="text-body-secondary">
      &copy; {{ date('Y') }} {{ setting('company_name', setting('site_name', config('app.name'))) }}. Todos los derechos reservados.
      @if(setting('company_email'))
        &nbsp;·&nbsp; <a href="mailto:{{ setting('company_email') }}" class="text-body-secondary">{{ setting('company_email') }}</a>
      @endif
    </small>
  </footer>

</div>
@endsection
