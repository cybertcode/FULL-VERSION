@extends('admin/layouts/master')

@section('title', 'Editar página: '.$page->title)

@section('admin-vendor-style')
  @vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('admin-vendor-script')
  @vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('admin-content')

<x-breadcrumb title="Editar página" :items="[
    ['label' => 'Páginas', 'url' => route('admin.pages.index')],
    ['label' => $page->title],
]" />

@include('admin.pages.partials.form', ['page' => $page, 'action' => route('admin.pages.update', $page), 'method' => 'PUT'])

@endsection

@section('admin-page-script')
  @include('admin.pages.partials.form-script')
@endsection
