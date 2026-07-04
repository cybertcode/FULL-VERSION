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
    </div>
  </div>
</div>
@endsection
