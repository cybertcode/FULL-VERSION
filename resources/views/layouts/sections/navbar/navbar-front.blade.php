@php
use App\Enums\MenuLocation;
use Illuminate\Support\Facades\Storage;
@endphp

@section('vendor-script')
@vite(['resources/assets/vendor/js/dropdown-hover.js', 'resources/assets/vendor/js/mega-dropdown.js'])
@endsection

<style>
  .landing-navbar .navbar-nav > .nav-item > .nav-link {
    padding-block: .375rem;
    padding-inline: .5rem;
    font-size: .8125rem;
    font-weight: 500;
  }
  .landing-navbar .navbar-nav > .nav-item > .nav-link .icon-base { font-size: .875rem; }
  .landing-navbar .dropdown-item { font-size: .8125rem; padding-block: .375rem; }
  .landing-navbar .app-brand-text { font-size: 1rem; }
  @media (max-width: 991.98px) {
    .landing-navbar .navbar-nav > .nav-item > .nav-link { padding-block: .5rem; padding-inline: .25rem; }
    .landing-nav-menu .navbar-nav .dropdown-menu {
      position: static !important;
      float: none;
      inset: auto !important;
      transform: none !important;
      box-shadow: none;
      border: none;
      background: transparent;
      padding-inline-start: 1rem;
      margin: 0;
    }
  }
</style>

<!-- Navbar: Start -->
<nav class="layout-navbar shadow-none py-0">
  <div class="container">
    <div class="navbar navbar-expand-lg landing-navbar px-3 px-md-8">
      <!-- Menu logo wrapper: Start -->
      <div class="navbar-brand app-brand demo d-flex py-0 me-4 me-xl-8 ms-0">
        <!-- Mobile menu toggle: Start-->
        <button class="navbar-toggler border-0 px-0 me-4" type="button" data-bs-toggle="collapse"
          data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
          aria-label="Toggle navigation">
          <i class="icon-base ti tabler-menu-2 icon-lg align-middle text-heading fw-medium"></i>
        </button>
        <!-- Mobile menu toggle: End-->
        <a href="{{ url('/') }}" class="app-brand-link">
          @if(setting('site_logo'))
            <img src="{{ Storage::url(setting('site_logo')) }}" alt="{{ setting('site_name') }}"
              class="app-brand-logo demo" style="height: 28px; object-fit: contain;">
          @else
            <span class="app-brand-logo demo">@include('_partials.macros')</span>
          @endif
          <span class="app-brand-text demo menu-text fw-bold ms-2 ps-1">{{ setting('site_name', config('variables.templateName')) }}</span>
        </a>
      </div>
      <!-- Menu logo wrapper: End -->
      <!-- Menu wrapper: Start -->
      <div class="collapse navbar-collapse landing-nav-menu" id="navbarSupportedContent">
        <button class="navbar-toggler border-0 text-heading position-absolute end-0 top-0 scaleX-n1-rtl p-2"
          type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="icon-base ti tabler-x icon-lg"></i>
        </button>
        <ul class="navbar-nav me-auto">
          @include('frontend.partials.menu-nodes', ['nodes' => optional(\App\Models\MenuLocationAssignment::where('location', MenuLocation::Header->value)->first()?->menu)->tree() ?? collect()])
        </ul>
      </div>
      <div class="landing-menu-overlay d-lg-none"></div>
      <!-- Menu wrapper: End -->
      <!-- Toolbar: Start -->
      <ul class="navbar-nav flex-row align-items-center ms-auto">
        @if ($configData['hasCustomizer'] == true)
        <!-- Style Switcher -->
        <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-1">
          <a class="nav-link dropdown-toggle hide-arrow" id="nav-theme" href="javascript:void(0);"
            data-bs-toggle="dropdown">
            <i class="icon-base ti tabler-sun icon-lg theme-icon-active"></i>
            <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
            <li>
              <button type="button" class="dropdown-item align-items-center active" data-bs-theme-value="light"
                aria-pressed="false">
                <span><i class="icon-base ti tabler-sun icon-md me-3" data-icon="sun"></i>Light</span>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark"
                aria-pressed="true">
                <span><i class="icon-base ti tabler-moon-stars icon-md me-3" data-icon="moon-stars"></i>Dark</span>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
                aria-pressed="false">
                <span><i class="icon-base ti tabler-device-desktop-analytics icon-md me-3"
                    data-icon="device-desktop-analytics"></i>System</span>
              </button>
            </li>
          </ul>
        </li>
        <!-- / Style Switcher-->
        @endif

        <!-- navbar button: Start -->
        <li>
          @auth
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary"><span
                class="icon-base ti tabler-layout-dashboard scaleX-n1-rtl me-md-1"></span><span
                class="d-none d-md-block">Panel</span></a>
          @else
            @auth('customer')
              <a href="{{ route('cuenta.dashboard') }}" class="btn btn-primary"><span
                  class="icon-base ti tabler-user scaleX-n1-rtl me-md-1"></span><span
                  class="d-none d-md-block">Mi cuenta</span></a>
            @else
              <a href="{{ route('cuenta.login') }}" class="btn btn-primary"><span
                  class="icon-base ti tabler-login scaleX-n1-rtl me-md-1"></span><span
                  class="d-none d-md-block">Iniciar sesión</span></a>
            @endauth
          @endauth
        </li>
        <!-- navbar button: End -->
      </ul>
      <!-- Toolbar: End -->
    </div>
  </div>
</nav>
<!-- Navbar: End -->