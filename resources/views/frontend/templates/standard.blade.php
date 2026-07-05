@extends('layouts/blankLayout')

@section('title', $page->seo_title ?? $page->title)

@section('content')
<div class="container py-8" style="max-width: 860px;">
  <h1 class="mb-6">{{ $page->title }}</h1>
  <div class="page-content">
    {!! $page->content['body'] ?? '' !!}
  </div>
</div>
@endsection
