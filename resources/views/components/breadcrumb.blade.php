@props([
    'title',
    'items' => [],
])

<div class="d-flex align-items-center justify-content-between mb-6">
  <div>
    <h4 class="mb-1">{{ $title }}</h4>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb breadcrumb-custom-icon mb-0">
        <li class="breadcrumb-item">
          <a href="{{ url('/') }}">Inicio</a>
          @if(count($items))
            <i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
          @endif
        </li>
        @foreach($items as $item)
          @if(!$loop->last)
            <li class="breadcrumb-item">
              <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
              <i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
            </li>
          @else
            <li class="breadcrumb-item active">{{ $item['label'] }}</li>
          @endif
        @endforeach
      </ol>
    </nav>
  </div>
  @isset($actions)
    <div class="d-flex gap-2">{{ $actions }}</div>
  @endisset
</div>
