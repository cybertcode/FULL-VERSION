@php
  $configData = Helper::appClasses();
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', setting('site_name', 'Inicio'))

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
@endsection

@section('content')
<div class="misc-wrapper text-center py-12 px-4">

  <h1 class="mb-3 fw-bold">{{ setting('site_name', 'Mi Sistema') }}</h1>
  <p class="mb-6 text-body-secondary">
    {{ setting('site_description', 'Bienvenido al sistema institucional.') }}
  </p>

  <div class="d-flex flex-wrap gap-3 justify-content-center mb-10">
    @auth
      <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-layout-dashboard me-2"></i>Ir al panel
      </a>
    @else
      <a href="{{ route('login') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-login me-2"></i>Iniciar sesión
      </a>
    @endauth
  </div>

</div>
@endsection
