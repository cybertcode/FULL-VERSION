@php($depth = $depth ?? 0)
@foreach ($nodes as $node)
  @continue(! $node->is_active)
  <li class="nav-item @if ($node->children->isNotEmpty()) dropdown @endif">
    <a class="{{ $depth > 0 ? 'dropdown-item' : 'nav-link fw-medium' }} {{ $node->children->isNotEmpty() ? 'dropdown-toggle' : '' }}"
      href="{{ $node->resolvedUrl() }}" target="{{ $node->target }}"
      @if ($node->children->isNotEmpty()) data-bs-toggle="dropdown" aria-expanded="false" @endif>
      @if ($node->icon)<i class="icon-base ti tabler-{{ $node->icon }} icon-16px me-1"></i>@endif
      {{ $node->label }}
    </a>
    @if ($node->children->isNotEmpty())
      <ul class="dropdown-menu">
        @include('frontend.partials.menu-nodes', ['nodes' => $node->children, 'depth' => $depth + 1])
      </ul>
    @endif
  </li>
@endforeach
