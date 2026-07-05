@foreach ($nodes as $node)
  @continue(! $node->is_active)
  <li class="nav-item @if ($node->children->isNotEmpty()) dropdown @endif">
    <a class="nav-link" href="{{ $node->resolvedUrl() }}" target="{{ $node->target }}">
      @if ($node->icon)<i class="ti tabler-{{ $node->icon }} me-1"></i>@endif
      {{ $node->label }}
    </a>
    @if ($node->children->isNotEmpty())
      <ul class="dropdown-menu">
        @include('frontend.partials.menu-nodes', ['nodes' => $node->children])
      </ul>
    @endif
  </li>
@endforeach
