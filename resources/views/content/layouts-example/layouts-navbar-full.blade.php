@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Navbar Full - Layouts')

@section('content')

<!-- Layout Demo -->
<div class="layout-demo-wrapper">
  <div class="layout-demo-placeholder">
    <img src="{{asset('assets/img/layouts/layout-content-navbar-'.$configData['theme'].'.png')}}" class="img-fluid"
      alt="Layout navbar full"
      data-app-light-img="layouts/layout-content-navbar-light.png"
      data-app-dark-img="layouts/layout-content-navbar-dark.png">
  </div>
  <div class="layout-demo-info">
    <h4>Layout Navbar Full</h4>
    <p>Full width navbar layout.</p>
  </div>
</div>
<!--/ Layout Demo -->
@endsection
