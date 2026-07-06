<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Inicio</a></li>
    @foreach ($page->breadcrumbTrail() as $ancestor)
      <li class="breadcrumb-item"><a href="{{ $ancestor['url'] }}">{{ $ancestor['label'] }}</a></li>
    @endforeach
    <li class="breadcrumb-item active" aria-current="page">{{ $page->title }}</li>
  </ol>
</nav>
