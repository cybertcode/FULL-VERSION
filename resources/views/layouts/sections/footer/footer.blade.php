@php
$containerFooter = isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact'
  ? 'container-xxl'
  : 'container-fluid';

$socials = [
  'social_facebook'  => ['icon' => 'tabler-brand-facebook-filled',  'label' => 'Facebook'],
  'social_instagram' => ['icon' => 'tabler-brand-instagram',        'label' => 'Instagram'],
  'social_twitter'   => ['icon' => 'tabler-brand-twitter-filled',   'label' => 'Twitter'],
  'social_linkedin'  => ['icon' => 'tabler-brand-linkedin',         'label' => 'LinkedIn'],
  'social_youtube'   => ['icon' => 'tabler-brand-youtube-filled',   'label' => 'YouTube'],
  'social_tiktok'    => ['icon' => 'tabler-brand-tiktok-filled',    'label' => 'TikTok'],
];

$hasSocials = collect($socials)->keys()->some(fn($k) => setting($k));
$whatsapp   = setting('social_whatsapp');
@endphp

<footer class="content-footer footer bg-footer-theme">
  <div class="{{ $containerFooter }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column gap-3">

      {{-- Izquierda: copyright + redes --}}
      <div class="d-flex align-items-center flex-wrap gap-3">
        <span class="text-body">
          &#169; {{ date('Y') }}, <strong>{{ setting('company_name', setting('site_name', 'Mi Sistema')) }}</strong>.
          Todos los derechos reservados.
        </span>

        @if($hasSocials || $whatsapp)
        <div class="d-flex align-items-center gap-1 ms-1">
          @foreach($socials as $key => $cfg)
            @if(setting($key))
            <a href="{{ setting($key) }}" target="_blank" rel="noopener"
              class="btn btn-icon btn-sm btn-text-secondary rounded-circle"
              title="{{ $cfg['label'] }}" style="font-size:1rem;">
              <i class="icon-base ti {{ $cfg['icon'] }}"></i>
            </a>
            @endif
          @endforeach
          @if($whatsapp)
          <a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp) }}" target="_blank" rel="noopener"
            class="btn btn-icon btn-sm btn-text-secondary rounded-circle"
            title="WhatsApp" style="font-size:1rem;">
            <i class="icon-base ti tabler-brand-whatsapp"></i>
          </a>
          @endif
        </div>
        @endif
      </div>

      {{-- Derecha: versión + crédito --}}
      <div class="d-none d-lg-flex align-items-center gap-2">
        <span class="badge bg-label-secondary">v{{ config('app.version', '1.0.0') }}</span>
        <span class="text-muted">Desarrollado por</span>
        <a href="https://developtech.pe" class="footer-link fw-medium" target="_blank" rel="noopener">DevelopTech</a>
      </div>

    </div>
  </div>
</footer>
