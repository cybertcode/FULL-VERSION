@extends('admin/layouts/master')

@section('title', 'Nueva página')

@section('admin-vendor-style')
  @vite([
    'resources/assets/vendor/libs/quill/typography.scss',
    'resources/assets/vendor/libs/quill/editor.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
  ])
@endsection

@section('admin-vendor-script')
  @vite([
    'resources/assets/vendor/libs/quill/quill.js',
    'resources/assets/vendor/libs/select2/select2.js',
  ])
@endsection

@section('admin-content')

<x-breadcrumb title="Nueva página" :items="[
    ['label' => 'Páginas', 'url' => route('admin.pages.index')],
    ['label' => 'Nueva página'],
]" />

@include('admin.pages.partials.form', ['page' => null, 'action' => route('admin.pages.store'), 'method' => 'POST'])

@endsection

@section('admin-page-script')
  @include('admin.pages.partials.form-script')
@endsection
