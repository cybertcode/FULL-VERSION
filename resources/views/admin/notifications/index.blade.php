@extends('admin/layouts/master')

@section('title', 'Notificaciones')

@section('admin-content')

<x-breadcrumb title="Mis Notificaciones" :items="[['label' => 'Notificaciones']]">
  <x-slot name="actions">
    @if($unreadCount > 0)
      <form method="POST" action="{{ route('admin.notifications.read-all') }}">
        @csrf
        <button type="submit" class="btn btn-label-primary">
          <i class="icon-base ti tabler-mail-opened icon-sm me-1"></i> Marcar todas como leídas
        </button>
      </form>
    @endif
  </x-slot>
</x-breadcrumb>

<div class="card">
  <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="d-flex align-items-center gap-2">
      <h6 class="mb-0">
        {{ $unreadCount > 0 ? $unreadCount . ' sin leer' : 'Todo leído' }}
      </h6>
      @if($unreadCount > 0)<span class="badge rounded-pill bg-danger">{{ $unreadCount }}</span>@endif
    </div>
    <div class="btn-group" role="group">
      <a href="{{ route('admin.notifications.index') }}"
        class="btn btn-sm {{ request('filtro') !== 'no-leidas' ? 'btn-primary' : 'btn-label-secondary' }}">Todas</a>
      <a href="{{ route('admin.notifications.index', ['filtro' => 'no-leidas']) }}"
        class="btn btn-sm {{ request('filtro') === 'no-leidas' ? 'btn-primary' : 'btn-label-secondary' }}">No leídas</a>
    </div>
  </div>

  <ul class="list-group list-group-flush">
    @forelse($notifications as $notification)
    <li class="list-group-item d-flex align-items-start gap-3 py-4 {{ $notification->read_at ? '' : 'bg-lighter' }}">
      <div class="avatar flex-shrink-0">
        <span class="avatar-initial rounded-circle bg-label-{{ $notification->data['color'] ?? 'primary' }}">
          <i class="icon-base ti {{ $notification->data['icon'] ?? 'tabler-bell' }} icon-22px"></i>
        </span>
      </div>
      <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2 mb-1">
          <h6 class="mb-0 {{ $notification->read_at ? '' : 'fw-semibold' }}">{{ $notification->data['title'] ?? 'Notificación' }}</h6>
          @unless($notification->read_at)<span class="badge badge-dot bg-primary"></span>@endunless
        </div>
        <p class="mb-1">{{ $notification->data['message'] ?? '' }}</p>
        <small class="text-body-secondary" title="{{ $notification->created_at->format('d/m/Y H:i:s') }}">
          {{ $notification->created_at->diffForHumans() }}
        </small>
      </div>
      <div class="d-flex gap-1 flex-shrink-0">
        @if(($notification->data['url'] ?? null) || !$notification->read_at)
          <a href="{{ route('admin.notifications.read', $notification->id) }}"
            class="btn btn-sm btn-icon btn-text-secondary rounded-pill"
            title="{{ ($notification->data['url'] ?? null) ? 'Abrir' : 'Marcar como leída' }}">
            <i class="icon-base ti {{ ($notification->data['url'] ?? null) ? 'tabler-external-link' : 'tabler-check' }} icon-sm"></i>
          </a>
        @endif
        <form method="POST" action="{{ route('admin.notifications.destroy', $notification->id) }}"
          onsubmit="return false;" id="delete-notif-{{ $notification->id }}">
          @csrf @method('DELETE')
          <button type="button" class="btn btn-sm btn-icon btn-text-danger rounded-pill" title="Eliminar"
            onclick="confirmDelete('delete-notif-{{ $notification->id }}', 'esta notificación')">
            <i class="icon-base ti tabler-trash icon-sm"></i>
          </button>
        </form>
      </div>
    </li>
    @empty
    <li class="list-group-item text-center py-6">
      <i class="icon-base ti tabler-bell-off icon-lg text-muted d-block mx-auto mb-2"></i>
      <p class="text-muted mb-0">
        {{ request('filtro') === 'no-leidas' ? 'No tienes notificaciones sin leer.' : 'Aún no tienes notificaciones.' }}
      </p>
    </li>
    @endforelse
  </ul>

  @if($notifications->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
      <small class="text-muted">
        Mostrando {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }} de {{ $notifications->total() }}
      </small>
      {{ $notifications->links() }}
    </div>
  @endif
</div>

@endsection
