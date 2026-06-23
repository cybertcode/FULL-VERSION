{{--
  Componente: <x-table-legend :items="[...]" />

  Cada item: ['icon' => 'tabler-xxx', 'color' => 'primary|warning|...', 'text' => 'HTML permitido']
--}}
@props(['items' => []])

@if(count($items))
<div class="card border-0 mt-6" style="background:rgba(var(--bs-secondary-rgb),.04)">
  <div class="card-body py-3 px-4">
    <p class="mb-3 d-flex align-items-center gap-2" style="font-size:.68rem;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--bs-secondary-color)">
      <i class="icon-base ti tabler-info-circle icon-14px"></i> Guía rápida
    </p>
    <div class="d-flex flex-wrap gap-4">
      @foreach($items as $item)
      <div class="d-flex align-items-start gap-2" style="max-width:320px">
        <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0 bg-label-{{ $item['color'] ?? 'secondary' }}"
              style="width:30px;height:30px;margin-top:1px">
          <i class="icon-base ti {{ $item['icon'] ?? 'tabler-point' }} icon-14px"></i>
        </span>
        <span class="text-muted small lh-sm">{!! $item['text'] !!}</span>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endif
