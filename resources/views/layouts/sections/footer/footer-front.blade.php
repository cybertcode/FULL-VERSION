<!-- Footer: Start -->
<footer class="landing-footer bg-body footer-text">
  <div class="footer-top position-relative overflow-hidden z-1">
    <img src="{{ asset('assets/img/front-pages/backgrounds/footer-bg.png') }}" alt="footer bg"
      class="footer-bg banner-bg-img z-n1" />
    <div class="container">
      <div class="row gx-0 gy-6 g-lg-10">
        <div class="col-lg-5">
          <a href="{{ url('/') }}" class="app-brand-link mb-6">
            <span class="app-brand-logo demo">@include('_partials.macros')</span>
            <span
              class="app-brand-text demo footer-link fw-bold ms-2 ps-1">{{ setting('site_name', config('variables.templateName')) }}</span>
          </a>
          <p class="footer-text footer-logo-description mb-6">{{ setting('site_description', 'Bienvenido a nuestro sitio.') }}</p>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <h6 class="footer-title mb-6">Enlaces</h6>
          <ul class="list-unstyled footer-menu-list">
            @php
              $footerMenu = \App\Models\MenuLocationAssignment::where('location', \App\Enums\MenuLocation::Footer->value)->first()?->menu;
            @endphp
            @if($footerMenu)
              @foreach($footerMenu->tree() as $node)
                @continue(! $node->is_active)
                <li class="mb-4"><a href="{{ $node->resolvedUrl() }}" target="{{ $node->target }}" class="footer-link">{{ $node->label }}</a></li>
              @endforeach
            @endif
          </ul>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
          <h6 class="footer-title mb-6">Contacto</h6>
          <ul class="list-unstyled">
            @if(setting('company_email'))
              <li class="mb-4"><a href="mailto:{{ setting('company_email') }}" class="footer-link">{{ setting('company_email') }}</a></li>
            @endif
            @if(setting('company_phone'))
              <li class="mb-4"><a href="tel:{{ setting('company_phone') }}" class="footer-link">{{ setting('company_phone') }}</a></li>
            @endif
            @if(setting('company_website'))
              <li class="mb-4"><a href="{{ setting('company_website') }}" target="_blank" rel="noopener" class="footer-link">{{ setting('company_website') }}</a></li>
            @endif
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom py-3 py-md-5">
    <div class="container d-flex flex-wrap justify-content-between flex-md-row flex-column text-center text-md-start">
      <div class="mb-2 mb-md-0">
        <span class="footer-bottom-text">©
          <script>
          document.write(new Date().getFullYear());
          </script>
        </span>
        <span class="footer-bottom-text fw-medium text-white">{{ setting('company_name', setting('site_name', config('app.name'))) }}</span>
        <span class="footer-bottom-text"> Todos los derechos reservados.</span>
      </div>
      <div>
        @php
          $footerSocials = collect([
              'social_facebook'  => 'tabler-brand-facebook',
              'social_instagram' => 'tabler-brand-instagram',
              'social_twitter'   => 'tabler-brand-twitter',
              'social_linkedin'  => 'tabler-brand-linkedin',
              'social_youtube'   => 'tabler-brand-youtube',
              'social_tiktok'    => 'tabler-brand-tiktok',
          ])->map(fn ($icon, $key) => ['url' => setting($key), 'icon' => $icon])
            ->filter(fn ($s) => filled($s['url']));
        @endphp
        @foreach($footerSocials as $social)
          <a href="{{ $social['url'] }}" class="me-2 text-white" target="_blank" rel="noopener">
            <i class="icon-base ti {{ $social['icon'] }} icon-lg"></i>
          </a>
        @endforeach
      </div>
    </div>
  </div>
</footer>
<!-- Footer: End -->