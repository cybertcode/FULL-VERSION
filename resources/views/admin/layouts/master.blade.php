@extends('layouts/layoutMaster')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
  @yield('admin-vendor-style')
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
  @yield('admin-vendor-script')
@endsection

@section('page-script')
  <x-flash-messages />
  @yield('admin-page-script')
@endsection

@section('content')
  @yield('admin-content')
@endsection
