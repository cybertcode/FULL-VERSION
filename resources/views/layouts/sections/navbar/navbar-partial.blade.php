@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
@endphp

<!--  Brand demo (display only for navbar-full and hide on below xl) -->
@if (isset($navbarFull))
<div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4 ms-0">
  <a href="{{ url('/') }}" class="app-brand-link">
    <span class="app-brand-logo demo">@include('_partials.macros')</span>
    <span class="app-brand-text demo menu-text fw-bold">{{ setting('site_name', config('variables.templateName')) }}</span>
  </a>

  <!-- Display menu close icon only for horizontal-menu with navbar-full -->
  @if (isset($menuHorizontal))
  <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
    <i class="icon-base ti tabler-x icon-sm d-flex align-items-center justify-content-center"></i>
  </a>
  @endif
</div>
@endif

<!-- ! Not required for layout-without-menu -->
@if (!isset($navbarHideToggle))
<div
  class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
  <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
    <i class="icon-base ti tabler-menu-2 icon-md"></i>
  </a>
</div>
@endif

<div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">

  @if (!isset($menuHorizontal))
  <!-- Search -->
  <div class="navbar-nav align-items-center">
    <div class="nav-item navbar-search-wrapper px-md-0 px-2 mb-0">
      <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
        <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
      </a>
    </div>
  </div>
  <!-- /Search -->
  @endif

  <ul class="navbar-nav flex-row align-items-center ms-md-auto">
    @if (isset($menuHorizontal))
    <!-- Search -->
    <li class="nav-item navbar-search-wrapper btn btn-text-secondary btn-icon rounded-pill">
      <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
        <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
      </a>
    </li>
    <!-- /Search -->
    @endif

    <!-- Language -->
    <li class="nav-item dropdown-language dropdown">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
        href="javascript:void(0);" data-bs-toggle="dropdown">
        <i class="icon-base ti tabler-language icon-22px text-heading"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ url('lang/en') }}"
            data-language="en" data-text-direction="ltr">
            <span>English</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'es' ? 'active' : '' }}" href="{{ url('lang/es') }}"
            data-language="es" data-text-direction="ltr">
            <span>Español</span>
          </a>
        </li>
      </ul>
    </li>
    <!--/ Language -->

    @if ($configData['hasCustomizer'] == true)
    <!-- Style Switcher -->
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill" id="nav-theme"
        href="javascript:void(0);" data-bs-toggle="dropdown">
        <i class="icon-base ti tabler-sun icon-22px theme-icon-active text-heading"></i>
        <span class="d-none ms-2" id="nav-theme-text">Cambiar tema</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
        <li>
          <button type="button" class="dropdown-item align-items-center active" data-bs-theme-value="light"
            aria-pressed="false">
            <span><i class="icon-base ti tabler-sun icon-22px me-3" data-icon="sun"></i>Claro</span>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark" aria-pressed="true">
            <span><i class="icon-base ti tabler-moon-stars icon-22px me-3" data-icon="moon-stars"></i>Oscuro</span>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
            aria-pressed="false">
            <span><i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"
                data-icon="device-desktop-analytics"></i>Sistema</span>
          </button>
        </li>
      </ul>
    </li>
    <!-- / Style Switcher-->
    @endif

    <!-- Quick links  -->
    <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
        href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        <i class="icon-base ti tabler-layout-grid-add icon-22px text-heading"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-end p-0">
        <div class="dropdown-menu-header border-bottom">
          <div class="dropdown-header d-flex align-items-center py-3">
            <h6 class="mb-0 me-auto">Accesos rápidos</h6>
            <a href="javascript:void(0)"
              class="dropdown-shortcuts-add py-2 btn btn-text-secondary rounded-pill btn-icon" data-bs-toggle="tooltip"
              data-bs-placement="top" title="Agregar acceso rápido"><i
                class="icon-base ti tabler-plus icon-20px text-heading"></i></a>
          </div>
        </div>
        <div class="dropdown-shortcuts-list scrollable-container">
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-device-desktop-analytics icon-26px text-heading"></i>
              </span>
              <a href="{{ route('admin.dashboard') }}" class="stretched-link">Panel</a>
              <small>Inicio</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-users icon-26px text-heading"></i>
              </span>
              <a href="{{ route('admin.users.index') }}" class="stretched-link">Usuarios</a>
              <small>Gestión de usuarios</small>
            </div>
          </div>
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-shield-lock icon-26px text-heading"></i>
              </span>
              <a href="{{ route('admin.roles.index') }}" class="stretched-link">Roles</a>
              <small>Roles y permisos</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-settings icon-26px text-heading"></i>
              </span>
              <a href="{{ route('admin.settings.index') }}" class="stretched-link">Configuración</a>
              <small>Ajustes del sistema</small>
            </div>
          </div>
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-user-circle icon-26px text-heading"></i>
              </span>
              <a href="{{ route('admin.profile.show') }}" class="stretched-link">Mi Perfil</a>
              <small>Ver mi cuenta</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-activity icon-26px text-heading"></i>
              </span>
              <a href="{{ url('/') }}" class="stretched-link">Actividad</a>
              <small>Registro de eventos</small>
            </div>
          </div>
        </div>
      </div>
    </li>
    <!-- Quick links -->

    <!-- Notification -->
    @auth
    @php
      $navbarUnreadCount    = Auth::user()->unreadNotifications()->count();
      $navbarNotifications  = Auth::user()->notifications()->latest()->take(8)->get();
    @endphp
    <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
        href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        <span class="position-relative">
          <i class="icon-base ti tabler-bell icon-22px text-heading"></i>
          @if($navbarUnreadCount > 0)
            <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
          @endif
        </span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end p-0">
        <li class="dropdown-menu-header border-bottom">
          <div class="dropdown-header d-flex align-items-center py-3">
            <h6 class="mb-0 me-auto">Notificaciones</h6>
            <div class="d-flex align-items-center h6 mb-0">
              @if($navbarUnreadCount > 0)
                <span class="badge bg-label-primary me-2">{{ $navbarUnreadCount }} nueva{{ $navbarUnreadCount === 1 ? '' : 's' }}</span>
                <form method="POST" action="{{ route('admin.notifications.read-all') }}" class="d-inline">
                  @csrf
                  <button type="submit" class="p-2 btn btn-icon" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="Marcar todas como leídas">
                    <i class="icon-base ti tabler-mail-opened text-heading"></i>
                  </button>
                </form>
              @endif
            </div>
          </div>
        </li>
        <li class="dropdown-notifications-list scrollable-container">
          <ul class="list-group list-group-flush">
            @forelse($navbarNotifications as $notification)
            <li class="list-group-item list-group-item-action dropdown-notifications-item {{ $notification->read_at ? 'marked-as-read' : '' }}">
              <a href="{{ route('admin.notifications.read', $notification->id) }}" class="d-flex text-decoration-none text-body">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar">
                    <span class="avatar-initial rounded-circle bg-label-{{ $notification->data['color'] ?? 'primary' }}">
                      <i class="icon-base ti {{ $notification->data['icon'] ?? 'tabler-bell' }} icon-22px"></i>
                    </span>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h6 class="small mb-1 {{ $notification->read_at ? '' : 'fw-semibold' }}">{{ $notification->data['title'] ?? 'Notificación' }}</h6>
                  <small class="mb-1 d-block text-body">{{ \Illuminate\Support\Str::limit($notification->data['message'] ?? '', 70) }}</small>
                  <small class="text-body-secondary">{{ $notification->created_at->diffForHumans() }}</small>
                </div>
                @unless($notification->read_at)
                  <div class="flex-shrink-0 dropdown-notifications-actions align-self-center">
                    <span class="badge badge-dot bg-primary"></span>
                  </div>
                @endunless
              </a>
            </li>
            @empty
            <li class="list-group-item dropdown-notifications-item">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                      <i class="icon-base ti tabler-bell-off icon-22px"></i>
                    </span>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <h6 class="small mb-1">Sin notificaciones</h6>
                  <small class="mb-1 d-block text-body">Las notificaciones del sistema aparecerán aquí.</small>
                </div>
              </div>
            </li>
            @endforelse
          </ul>
        </li>
        <li class="border-top">
          <div class="d-grid p-4">
            <a class="btn btn-primary btn-sm d-flex justify-content-center" href="{{ route('admin.notifications.index') }}">
              <small class="align-middle">Ver todas las notificaciones</small>
            </a>
          </div>
        </li>
      </ul>
    </li>
    @endauth
    <!--/ Notification -->
    <!-- User -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
          <img src="{{ Auth::user()?->avatar_url ?? asset('assets/img/avatars/1.png') }}" alt class="rounded-circle" />
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item mt-0" href="{{ route('admin.profile.show') }}">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0 me-2">
                <div class="avatar avatar-online">
                  <img src="{{ Auth::user()?->avatar_url ?? asset('assets/img/avatars/1.png') }}" alt class="rounded-circle" />
                </div>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-0">{{ Auth::user()?->name ?? 'Usuario' }}</h6>
                <small class="text-body-secondary">{{ Auth::user()?->roles->first()?->name ?? '—' }}</small>
              </div>
            </div>
          </a>
        </li>
        <li>
          <div class="dropdown-divider my-1 mx-n2"></div>
        </li>
        <li>
          <a class="dropdown-item" href="{{ route('admin.profile.show') }}">
            <i class="icon-base ti tabler-user me-3 icon-md"></i><span class="align-middle">Mi Perfil</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
            <i class="icon-base ti tabler-settings me-3 icon-md"></i><span class="align-middle">Configuración</span>
          </a>
        </li>
        <li>
          <div class="dropdown-divider my-1 mx-n2"></div>
        </li>
        @if (Auth::check())
        <li>
          <a class="dropdown-item" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Cerrar sesión</span>
          </a>
        </li>
        <form method="POST" id="logout-form" action="{{ route('logout') }}">
          @csrf
        </form>
        @else
        <li>
          <div class="d-grid px-2 pt-2 pb-1">
            <a class="btn btn-sm btn-danger d-flex"
              href="{{ Route::has('login') ? route('login') : url('auth/login-basic') }}" target="_blank">
              <small class="align-middle">Login</small>
              <i class="icon-base ti tabler-login ms-2 icon-14px"></i>
            </a>
          </div>
        </li>
        @endif
      </ul>
    </li>
    <!--/ User -->
  </ul>
</div>