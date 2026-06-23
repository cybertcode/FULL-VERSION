{{--
  Layout base para todas las páginas de error.
  Variables disponibles:
    $code       — código HTTP (404, 403, 500, 503)
    $title      — título corto ("Página no encontrada")
    $message    — descripción amigable
    $illustration — nombre del archivo en public/assets/img/illustrations/
    $illustrationWidth — ancho en px (default 225)
    $slot       — botones de acción (si no se define, muestra el botón default)
--}}
@php
  use Illuminate\Support\Facades\Storage;
  $configData       = Helper::appClasses();
  $customizerHidden = 'customizer-hide';
  $siteName         = setting('site_name', config('variables.templateName', 'Mi Sistema'));
  $companyName      = setting('company_name', $siteName);
  $supportEmail     = setting('mail_from_address', '');
  $logoUrl          = setting('site_logo') ? Storage::url(setting('site_logo')) : null;
  $logoDarkUrl      = setting('site_logo_dark') ? Storage::url(setting('site_logo_dark')) : null;
  $authUser         = auth()->user();
  $illustrationWidth = $illustrationWidth ?? 225;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-bs-theme="{{ $configData['theme'] }}"
      data-template="{{ $configData['headerType'] }}"
      data-assets-path="{{ asset('assets') . '/' }}"
      data-base-url="{{ url('/') }}"
      dir="{{ $configData['textDirection'] }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>{{ $code }} — {{ $title }} | {{ $siteName }}</title>
  <link rel="icon" type="image/x-icon" href="{{ setting('site_favicon') ? Storage::url(setting('site_favicon')) : asset('assets/img/favicon/favicon.ico') }}" />
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;display=swap" rel="stylesheet" />
  @vite([
    'resources/assets/vendor/fonts/iconify/iconify.css',
    'resources/assets/vendor/libs/node-waves/node-waves.scss',
    'resources/assets/vendor/scss/core.scss',
    'resources/assets/css/demo.css',
    'resources/assets/vendor/scss/pages/page-misc.scss',
    'resources/css/app.css',
  ])
  <style>
    .error-page-wrapper {
      min-height: 100dvh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .error-code {
      font-size: clamp(4rem, 12vw, 8rem);
      font-weight: 900;
      line-height: 1;
      letter-spacing: -4px;
    }
    .error-brand img { height: 32px; }
    .error-brand-text { font-size: 1.1rem; font-weight: 700; }
  </style>
</head>
<body class="customizer-hide">

  <div class="container-xxl container-p-y error-page-wrapper">

    {{-- Logo / Marca --}}
    <div class="text-center mb-8">
      <a href="{{ url('/') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none error-brand">
        @if($logoUrl)
          <img src="{{ $logoUrl }}" alt="{{ $siteName }}"
               data-logo-light="{{ $logoUrl }}"
               data-logo-dark="{{ $logoDarkUrl ?? $logoUrl }}" />
        @else
          <span class="icon-base ti tabler-shield-lock text-primary" style="font-size:2rem;"></span>
        @endif
        <span class="error-brand-text text-heading">{{ $siteName }}</span>
      </a>
    </div>

    <div class="misc-wrapper text-center">

      {{-- Código de error --}}
      <div class="error-code text-primary mb-2">{{ $code }}</div>

      {{-- Título --}}
      <h4 class="mb-2">{{ $title }}</h4>

      {{-- Mensaje --}}
      <p class="mb-2 text-body-secondary">{{ $message }}</p>

      {{-- Usuario autenticado --}}
      @if($authUser)
        <p class="mb-6 text-body-secondary small">
          Estás conectado como <strong>{{ $authUser->name }}</strong>
          @if($authUser->roles->isNotEmpty())
            ({{ $authUser->roles->first()->name }})
          @endif
        </p>
      @else
        <div class="mb-6"></div>
      @endif

      {{-- Botones de acción --}}
      @isset($actions)
        {!! $actions !!}
      @else
        <div class="d-flex flex-wrap gap-3 justify-content-center mb-10">
          @if($authUser)
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}"
               class="btn btn-outline-secondary">
              <i class="icon-base ti tabler-arrow-left me-1"></i> Volver
            </a>
            <a href="{{ url('/') }}" class="btn btn-primary">
              <i class="icon-base ti tabler-home me-1"></i> Ir al panel
            </a>
          @else
            <a href="{{ route('login') }}" class="btn btn-primary">
              <i class="icon-base ti tabler-login me-1"></i> Iniciar sesión
            </a>
          @endif
        </div>
      @endisset

      {{-- Ilustración --}}
      <div class="mt-4">
        <img src="{{ asset('assets/img/illustrations/' . $illustration) }}"
             alt="{{ $code }}"
             width="{{ $illustrationWidth }}"
             class="img-fluid" />
      </div>

      {{-- Información de contacto --}}
      @if($supportEmail && in_array($code, [500, 503]))
        <p class="mt-6 text-body-secondary small">
          Si el problema persiste, contacta a soporte:
          <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
        </p>
      @endif

      {{-- Footer --}}
      <p class="mt-8 text-body-secondary small">
        &copy; {{ date('Y') }} {{ $companyName }}. Todos los derechos reservados.
      </p>

    </div>
  </div>

  {{-- Fondo decorativo --}}
  <div class="container-fluid misc-bg-wrapper" style="pointer-events:none">
    <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}"
         height="355" alt=""
         data-app-light-img="illustrations/bg-shape-image-light.png"
         data-app-dark-img="illustrations/bg-shape-image-dark.png" />
  </div>

  @vite([
    'resources/assets/vendor/libs/jquery/jquery.js',
    'resources/assets/vendor/libs/popper/popper.js',
    'resources/assets/vendor/js/bootstrap.js',
    'resources/assets/vendor/libs/node-waves/node-waves.js',
    'resources/assets/vendor/js/helpers.js',
    'resources/assets/js/config.js',
    'resources/assets/js/main.js',
  ])

  {{-- Swap logo claro/oscuro sin MutationObserver pesado --}}
  <script>
    (function () {
      const html = document.documentElement;
      const logos = document.querySelectorAll('[data-logo-light]');
      function applyTheme() {
        const dark = html.getAttribute('data-bs-theme') === 'dark';
        logos.forEach(img => {
          img.src = dark ? img.dataset.logoDark : img.dataset.logoLight;
        });
      }
      applyTheme();
      new MutationObserver(applyTheme).observe(html, { attributes: true, attributeFilter: ['data-bs-theme'] });
    })();
  </script>

</body>
</html>
