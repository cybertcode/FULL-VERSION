@php
  $configData = Helper::appClasses();
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Mi cuenta')

@section('content')
<div class="container-xxl container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-6">
    <div>
      <h4 class="mb-1">Hola, {{ auth('customer')->user()->name }} 👋</h4>
      <p class="mb-0 text-body-secondary">{{ auth('customer')->user()->email }}</p>
    </div>
    <form action="{{ route('cuenta.logout') }}" method="POST">
      @csrf
      <button type="submit" class="btn btn-label-secondary">
        <i class="icon-base ti tabler-logout me-1"></i> Cerrar sesión
      </button>
    </form>
  </div>

  <div class="card">
    <div class="card-body">
      <p class="mb-0">Este es tu espacio personal. Aquí verás tu información y actividad, separado por completo del panel administrativo.</p>
    </div>
  </div>
</div>
@endsection
