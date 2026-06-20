@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', '403 - Sin autorización')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
@endsection

@section('content')
<div class="container-xxl container-p-y">
  <div class="misc-wrapper">
    <h1 class="mb-2 mx-2" style="line-height: 6rem; font-size: 6rem;">403</h1>
    <h4 class="mb-2 mx-2">Sin autorización 🔐</h4>
    <p class="mb-6 mx-2">No tienes permiso para acceder a esta página.</p>
    <a href="{{ url('/') }}" class="btn btn-primary">Volver al inicio</a>
    <div class="mt-12">
      <img src="{{ asset('assets/img/illustrations/page-misc-you-are-not-authorized.png') }}" alt="403" width="170" class="img-fluid" />
    </div>
  </div>
</div>
<div class="container-fluid misc-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}" height="355" alt="bg"
    data-app-light-img="illustrations/bg-shape-image-light.png"
    data-app-dark-img="illustrations/bg-shape-image-dark.png" />
</div>
@endsection
