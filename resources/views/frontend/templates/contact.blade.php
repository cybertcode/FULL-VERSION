@extends('layouts/blankLayout')

@section('title', $page->seo_title ?? $page->title)

@section('content')
<div class="container py-8" style="max-width: 640px;">
  <h1 class="mb-6">{{ $page->title }}</h1>

  @if (! empty($page->content['intro_text']))
    <div class="page-content mb-6">
      {!! $page->content['intro_text'] !!}
    </div>
  @endif

  @if (! empty($page->content['contact_email']))
    <p>
      <i class="icon-base ti tabler-mail me-1"></i>
      <a href="mailto:{{ $page->content['contact_email'] }}">{{ $page->content['contact_email'] }}</a>
    </p>
  @endif
</div>
@endsection
