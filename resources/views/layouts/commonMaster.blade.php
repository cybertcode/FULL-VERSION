<!DOCTYPE html>
@php
  use Illuminate\Support\Str;
  use Illuminate\Support\Facades\Storage;
  use App\Helpers\Helpers;

  $menuFixed =
      $configData['layout'] === 'vertical'
          ? $menuFixed ?? ''
          : ($configData['layout'] === 'front'
              ? ''
              : $configData['headerType']);
  $navbarType =
      $configData['layout'] === 'vertical'
          ? $configData['navbarType']
          : ($configData['layout'] === 'front'
              ? 'layout-navbar-fixed'
              : '');
  $isFront = ($isFront ?? '') == true ? 'Front' : '';
  $contentLayout = isset($container) ? ($container === 'container-xxl' ? 'layout-compact' : 'layout-wide') : '';

  // Get skin name from configData - only applies to admin layouts
  $isAdminLayout = !Str::contains($configData['layout'] ?? '', 'front');
  $skinName = $isAdminLayout ? $configData['skinName'] ?? 'default' : 'default';

  // Get semiDark value from configData - only applies to admin layouts
  $semiDarkEnabled = $isAdminLayout && filter_var($configData['semiDark'] ?? false, FILTER_VALIDATE_BOOLEAN);

  // Generate primary color CSS if color is set
  $primaryColorCSS = '';
  if (isset($configData['color']) && $configData['color']) {
      $primaryColorCSS = Helpers::generatePrimaryColorCSS($configData['color']);
  }

@endphp

<html lang="{{ session()->get('locale') ?? app()->getLocale() }}"
  class="{{ $navbarType ?? '' }} {{ $contentLayout ?? '' }} {{ $menuFixed ?? '' }} {{ $menuCollapsed ?? '' }} {{ $footerFixed ?? '' }} {{ $customizerHidden ?? '' }}"
  dir="{{ $configData['textDirection'] }}" data-skin="{{ $skinName }}" data-assets-path="{{ asset('/assets') . '/' }}"
  data-base-url="{{ url('/') }}" data-framework="laravel" data-template="{{ $configData['layout'] }}-menu-template"
  data-bs-theme="{{ $configData['theme'] }}" @if ($isAdminLayout && $semiDarkEnabled) data-semidark-menu="true" @endif>

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>
    @yield('title') | {{ setting('site_name', config('variables.templateName', 'App')) }}
  </title>
  <meta name="description"
    content="{{ setting('seo_description', config('variables.templateDescription', '')) }}" />
  <meta name="keywords"
    content="{{ setting('seo_keywords', config('variables.templateKeyword', '')) }}" />
  <meta property="og:title" content="{{ setting('seo_title', setting('site_name', config('variables.templateName', ''))) }}" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ url('/') }}" />
  <meta property="og:image" content="{{ setting('seo_og_image') ? Storage::url(setting('seo_og_image')) : config('variables.ogImage', '') }}" />
  <meta property="og:description"
    content="{{ setting('seo_description', config('variables.templateDescription', '')) }}" />
  <meta property="og:site_name"
    content="{{ setting('site_name', config('variables.templateName', '')) }}" />
  <meta name="robots" content="{{ setting('seo_robots', 'noindex, nofollow') }}" />
  <!-- laravel CRUD token -->
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ setting('site_favicon') ? Storage::url(setting('site_favicon')) : asset('assets/img/favicon/favicon.ico') }}" />

  <!-- Include Styles -->
  <!-- $isFront is used to append the front layout styles only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/styles' . $isFront)

  @if (
      $primaryColorCSS &&
          (config('custom.custom.primaryColor') ||
              isset($_COOKIE['admin-primaryColor']) ||
              isset($_COOKIE['front-primaryColor'])))
    <!-- Primary Color Style -->
    <style id="primary-color-style">
      {!! $primaryColorCSS !!}
    </style>
  @endif

  <!-- Include Scripts for customizer, helper, analytics, config -->
  <!-- $isFront is used to append the front layout scriptsIncludes only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scriptsIncludes' . $isFront)

  {{-- Google Tag Manager --}}
  @if(setting('gtm_id'))
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','{{ setting('gtm_id') }}');</script>
  @endif

  {{-- Google Analytics 4 --}}
  @if(setting('google_analytics_id') && !setting('gtm_id'))
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ setting('google_analytics_id') }}"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ setting('google_analytics_id') }}');
  </script>
  @endif

  {{-- Meta Pixel --}}
  @if(setting('meta_pixel_id'))
  <script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
    n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
    document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ setting('meta_pixel_id') }}');
    fbq('track', 'PageView');
  </script>
  <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ setting('meta_pixel_id') }}&ev=PageView&noscript=1"/></noscript>
  @endif
</head>

<body>
  {{-- Google Tag Manager (noscript) --}}
  @if(setting('gtm_id'))
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ setting('gtm_id') }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  @endif

  <!-- Layout Content -->
  @yield('layoutContent')
  <!--/ Layout Content -->

  

  <!-- Include Scripts -->
  <!-- $isFront is used to append the front layout scripts only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scripts' . $isFront)

  {{-- UI Globals: Bootstrap Toast + SweetAlert2 helpers — disponibles en todo el proyecto --}}
  <x-ui-globals />
</body>

</html>
