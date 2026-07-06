@php
  $configData = Helper::appClasses();
  $pageConfigs = ['myLayout' => 'front'];
@endphp

@extends('layouts/layoutMaster')

@section('title', $page->seo_title ?? $page->title)

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/front-page-landing.scss'])
@endsection

@section('content')
<!-- Mini-hero: Start -->
<section class="section-py landing-hero position-relative">
  <img src="{{ asset('assets/img/front-pages/backgrounds/hero-bg.png') }}" alt="hero background"
    class="position-absolute top-0 start-50 translate-middle-x object-fit-cover w-100 h-100" />
  <div class="container">
    <div class="hero-text-box text-center position-relative">
      <h1 class="text-primary hero-title display-6 fw-extrabold">Nuestro equipo</h1>
    </div>
  </div>
</section>
<!-- Mini-hero: End -->

<div class="container py-8" style="max-width: 960px;">

  @include('frontend.partials.page-breadcrumb', ['page' => $page])

  {{-- ==================================================================
       CONTENIDO DE "Nuestro equipo" — edita libremente debajo de esta línea
       ================================================================== --}}

  <p>Escribe aquí el contenido de esta página.</p>

</div>
@endsection
