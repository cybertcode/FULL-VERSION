@php
use Illuminate\Support\Facades\Auth;
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Verificar correo electrónico')

@section('page-style')
@vite('resources/assets/vendor/scss/pages/page-auth.scss')
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover">
  <!-- Logo -->
  <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
    <span class="app-brand-logo demo">@include('_partials.macros')</span>
    <span class="app-brand-text demo text-heading fw-bold">{{ setting('site_name', config('variables.templateName')) }}</span>
  </a>
  <!-- /Logo -->
  <div class="authentication-inner row m-0">
    <!-- Ilustración izquierda -->
    <div class="d-none d-xl-flex col-xl-8 p-0">
      <div class="auth-cover-bg d-flex justify-content-center align-items-center">
        <img
          src="{{ asset('assets/img/illustrations/auth-verify-email-illustration-' . $configData['theme'] . '.png') }}"
          alt="verificar-correo" class="my-5 auth-illustration"
          data-app-light-img="illustrations/auth-verify-email-illustration-light.png"
          data-app-dark-img="illustrations/auth-verify-email-illustration-dark.png" />
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}"
          alt="" class="platform-bg"
          data-app-light-img="illustrations/bg-shape-image-light.png"
          data-app-dark-img="illustrations/bg-shape-image-dark.png" />
      </div>
    </div>
    <!-- /Ilustración izquierda -->

    <!-- Verificar correo -->
    <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-6 p-sm-12">
      <div class="w-px-400 mx-auto mt-12 pt-5">
        <h4 class="mb-1">Verifica tu correo ✉️</h4>
        <p class="mb-0">
          Hemos enviado un enlace de activación a:
          <span class="fw-medium text-heading">{{ Auth::user()->email }}</span>
        </p>
        <p class="mb-6 text-body-secondary small">
          Por favor revisa tu bandeja de entrada y haz clic en el enlace para continuar.
        </p>

        @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-4 rounded" role="alert">
          <div class="d-flex align-items-center">
            <i class="icon-base ti tabler-circle-check me-2"></i>
            <span>Se ha enviado un nuevo enlace de verificación a tu correo.</span>
          </div>
        </div>
        @endif

        <div class="d-flex flex-column gap-3">
          <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-100 btn btn-primary">
              <i class="icon-base ti tabler-send me-2"></i>
              Reenviar correo de verificación
            </button>
          </form>

          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-100 btn btn-outline-danger">
              <i class="icon-base ti tabler-logout me-2"></i>
              Cerrar sesión
            </button>
          </form>
        </div>

        <p class="text-center text-body-secondary small mt-6">
          ¿No recibiste el correo? Revisa tu carpeta de spam o solicita un nuevo enlace.
        </p>
      </div>
    </div>
    <!-- /Verificar correo -->
  </div>
</div>
@endsection
