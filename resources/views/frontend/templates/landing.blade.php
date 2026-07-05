@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts/blankLayout')

@section('title', $page->seo_title ?? $page->title)

@section('content')
<div class="landing-wrapper">
  <div class="text-center py-8 px-4" style="background: var(--bs-body-tertiary-bg);">
    @if (! empty($page->content['hero_image']))
      <img src="{{ Storage::url($page->content['hero_image']) }}" alt="" class="mb-6" style="max-height:280px; max-width:100%; object-fit:contain;">
    @endif
    <h1 class="mb-3 fw-bold display-5">{{ $page->content['hero_title'] ?? $page->title }}</h1>
    @if (! empty($page->content['hero_subtitle']))
      <p class="fs-5 text-body-secondary" style="max-width:640px; margin:0 auto;">{{ $page->content['hero_subtitle'] }}</p>
    @endif
  </div>

  <div class="container py-8" style="max-width: 860px;">
    <div class="page-content">
      {!! $page->content['body'] ?? '' !!}
    </div>
  </div>
</div>
@endsection
