@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Mantenimiento')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
@endsection

@section('content')
<div class="misc-wrapper">
  <h2 class="mb-2 mx-2">Sitio en Mantenimiento 🛠️</h2>
  <p class="mb-6 mx-2">{{ $message ?? 'El sistema se encuentra en mantenimiento. Vuelve pronto.' }}</p>
  <div class="d-flex justify-content-center mt-8">
    <img src="{{ asset('assets/img/illustrations/page-misc-under-maintenance.png') }}"
      alt="maintenance" class="img-fluid"
      data-app-light-img="illustrations/page-misc-under-maintenance.png"
      data-app-dark-img="illustrations/page-misc-under-maintenance.png"
      style="max-width:500px;">
  </div>
</div>
@endsection
