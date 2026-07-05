@php
  use Illuminate\Support\Facades\Storage;
  $configData = Helper::appClasses();
  $pageConfigs = ['myLayout' => 'front'];
@endphp

@extends('layouts/layoutMaster')

@section('title', setting('site_name', 'Inicio'))

@section('vendor-style')
@vite(['resources/assets/vendor/libs/nouislider/nouislider.scss', 'resources/assets/vendor/libs/swiper/swiper.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/front-page-landing.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/nouislider/nouislider.js', 'resources/assets/vendor/libs/swiper/swiper.js'])
@endsection

@section('page-script')
@vite(['resources/assets/js/front-page-landing.js'])
@endsection

@section('content')
<div data-bs-spy="scroll" class="scrollspy-example">

  <!-- Hero: Start -->
  <section id="hero-animation">
    <div id="landingHero" class="section-py landing-hero position-relative">
      <img src="{{ asset('assets/img/front-pages/backgrounds/hero-bg.png') }}" alt="hero background"
        class="position-absolute top-0 start-50 translate-middle-x object-fit-cover w-100 h-100" data-speed="1" />
      <div class="container">
        <div class="hero-text-box text-center position-relative">
          <h1 class="text-primary hero-title display-6 fw-extrabold">{{ setting('site_name', 'Mi Sistema') }}</h1>
          <h2 class="hero-sub-title h6 mb-6">
            {{ setting('site_description', 'Bienvenido al sistema institucional.') }}
          </h2>
          <div class="landing-hero-btn d-inline-block position-relative">
            @auth
              <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg">Ir al panel</a>
            @else
              @auth('customer')
                <a href="{{ route('cuenta.dashboard') }}" class="btn btn-primary btn-lg">Ir a mi cuenta</a>
              @else
                <a href="{{ route('cuenta.login') }}" class="btn btn-primary btn-lg">Iniciar sesión</a>
              @endauth
            @endauth
          </div>
        </div>
        <div id="heroDashboardAnimation" class="hero-animation-img">
          <div id="heroAnimationImg" class="position-relative hero-dashboard-img">
            <img
              src="{{ asset('assets/img/front-pages/landing-page/hero-dashboard-' . $configData['theme'] . '.png') }}"
              alt="hero dashboard" class="animation-img"
              data-app-light-img="front-pages/landing-page/hero-dashboard-light.png"
              data-app-dark-img="front-pages/landing-page/hero-dashboard-dark.png" />
            <img
              src="{{ asset('assets/img/front-pages/landing-page/hero-elements-' . $configData['theme'] . '.png') }}"
              alt="hero elements" class="position-absolute hero-elements-img animation-img top-0 start-0"
              data-app-light-img="front-pages/landing-page/hero-elements-light.png"
              data-app-dark-img="front-pages/landing-page/hero-elements-dark.png" />
          </div>
        </div>
      </div>
    </div>
    <div class="landing-hero-blank"></div>
  </section>
  <!-- Hero: End -->

  <!-- Features: Start -->
  <section id="landingFeatures" class="section-py landing-features">
    <div class="container">
      <div class="text-center mb-4">
        <span class="badge bg-label-primary">Características</span>
      </div>
      <h4 class="text-center mb-1">
        <span class="position-relative fw-extrabold z-1">Todo lo que necesitas
          <img src="{{ asset('assets/img/front-pages/icons/section-title-icon.png') }}" alt="icono"
            class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
        </span>
        para tu próximo proyecto
      </h4>
      <p class="text-center mb-12">[Describe aquí en una línea la propuesta de valor de tu proyecto.]</p>
      <div class="features-icon-wrapper row gx-0 gy-6 g-sm-12">
        <div class="col-lg-4 col-sm-6 text-center features-icon-box">
          <div class="mb-4 text-primary text-center"><i class="icon-base ti tabler-shield-check icon-48px"></i></div>
          <h5 class="mb-2">[Característica 1]</h5>
          <p class="features-icon-description">[Describe brevemente esta característica de tu proyecto.]</p>
        </div>
        <div class="col-lg-4 col-sm-6 text-center features-icon-box">
          <div class="text-center mb-4 text-primary"><i class="icon-base ti tabler-refresh icon-48px"></i></div>
          <h5 class="mb-2">[Característica 2]</h5>
          <p class="features-icon-description">[Describe brevemente esta característica de tu proyecto.]</p>
        </div>
        <div class="col-lg-4 col-sm-6 text-center features-icon-box">
          <div class="text-center mb-4 text-primary"><i class="icon-base ti tabler-rocket icon-48px"></i></div>
          <h5 class="mb-2">[Característica 3]</h5>
          <p class="features-icon-description">[Describe brevemente esta característica de tu proyecto.]</p>
        </div>
        <div class="col-lg-4 col-sm-6 text-center features-icon-box">
          <div class="text-center mb-4 text-primary"><i class="icon-base ti tabler-api icon-48px"></i></div>
          <h5 class="mb-2">[Característica 4]</h5>
          <p class="features-icon-description">[Describe brevemente esta característica de tu proyecto.]</p>
        </div>
        <div class="col-lg-4 col-sm-6 text-center features-icon-box">
          <div class="text-center mb-4 text-primary"><i class="icon-base ti tabler-headset icon-48px"></i></div>
          <h5 class="mb-2">[Característica 5]</h5>
          <p class="features-icon-description">[Describe brevemente esta característica de tu proyecto.]</p>
        </div>
        <div class="col-lg-4 col-sm-6 text-center features-icon-box">
          <div class="text-center mb-4 text-primary"><i class="icon-base ti tabler-file-text icon-48px"></i></div>
          <h5 class="mb-2">[Característica 6]</h5>
          <p class="features-icon-description">[Describe brevemente esta característica de tu proyecto.]</p>
        </div>
      </div>
    </div>
  </section>
  <!-- Features: End -->

  <!-- Testimonios: Start -->
  <section id="landingReviews" class="section-py bg-body landing-reviews pb-0">
    <div class="container">
      <div class="row align-items-center gx-0 gy-4 g-lg-5 mb-5 pb-md-5">
        <div class="col-md-6 col-lg-5 col-xl-3">
          <div class="mb-4">
            <span class="badge bg-label-primary">Testimonios</span>
          </div>
          <h4 class="mb-1">
            <span class="position-relative fw-extrabold z-1">Lo que dicen
              <img src="{{ asset('assets/img/front-pages/icons/section-title-icon.png') }}" alt="icono"
                class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
            </span>
          </h4>
          <p class="mb-5 mb-md-12">Opiniones de ejemplo de quienes usan este proyecto.</p>
          <div class="landing-reviews-btns">
            <button id="reviews-previous-btn" class="btn btn-icon btn-label-primary reviews-btn me-3" type="button">
              <i class="icon-base ti tabler-chevron-left icon-md scaleX-n1-rtl"></i>
            </button>
            <button id="reviews-next-btn" class="btn btn-icon btn-label-primary reviews-btn" type="button">
              <i class="icon-base ti tabler-chevron-right icon-md scaleX-n1-rtl"></i>
            </button>
          </div>
        </div>
        <div class="col-md-6 col-lg-7 col-xl-9">
          <div class="swiper-reviews-carousel overflow-hidden">
            <div class="swiper" id="swiper-reviews">
              <div class="swiper-wrapper">
                @foreach ([1, 2, 3] as $i)
                <div class="swiper-slide">
                  <div class="card h-100">
                    <div class="card-body text-body d-flex flex-column justify-content-between h-100">
                      <p>"[Testimonio de ejemplo {{ $i }}. Reemplázalo con una opinión real de un cliente o usuario.]"</p>
                      <div class="text-warning mb-4">
                        @for ($s = 0; $s < 5; $s++)<i class="icon-base ti tabler-star-filled"></i>@endfor
                      </div>
                      <div class="d-flex align-items-center">
                        <div class="avatar me-3 avatar-sm">
                          <span class="avatar-initial rounded-circle bg-label-primary"><i class="icon-base ti tabler-user"></i></span>
                        </div>
                        <div>
                          <h6 class="mb-0">[Nombre {{ $i }}]</h6>
                          <p class="small text-body-secondary mb-0">[Cargo, Empresa]</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
              <div class="swiper-button-next"></div>
              <div class="swiper-button-prev"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Testimonios: End -->

  <!-- Equipo: Start -->
  <section id="landingTeam" class="section-py landing-team">
    <div class="container">
      <div class="text-center mb-4">
        <span class="badge bg-label-primary">Nuestro Equipo</span>
      </div>
      <h4 class="text-center mb-1">
        <span class="position-relative fw-extrabold z-1">Personas reales
          <img src="{{ asset('assets/img/front-pages/icons/section-title-icon.png') }}" alt="icono"
            class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
        </span>
        detrás del proyecto
      </h4>
      <p class="text-center mb-md-11 pb-0 pb-xl-12">[Reemplaza esta sección con tu equipo real o quítala si no aplica.]</p>
      <div class="row gy-12 mt-2">
        @foreach ([['label' => 'primary', 'name' => '[Nombre]', 'role' => '[Cargo]'], ['label' => 'info', 'name' => '[Nombre]', 'role' => '[Cargo]'], ['label' => 'danger', 'name' => '[Nombre]', 'role' => '[Cargo]'], ['label' => 'success', 'name' => '[Nombre]', 'role' => '[Cargo]']] as $member)
        <div class="col-lg-3 col-sm-6">
          <div class="card mt-3 mt-lg-0 shadow-none">
            <div class="bg-label-{{ $member['label'] }} border border-bottom-0 border-label-{{ $member['label'] }} position-relative team-image-box d-flex align-items-center justify-content-center">
              <i class="icon-base ti tabler-user icon-48px text-{{ $member['label'] }}"></i>
            </div>
            <div class="card-body border border-top-0 border-label-{{ $member['label'] }} text-center">
              <h5 class="card-title mb-0">{{ $member['name'] }}</h5>
              <p class="text-body-secondary mb-0">{{ $member['role'] }}</p>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>
  <!-- Equipo: End -->

  <!-- Planes: Start -->
  <section id="landingPricing" class="section-py bg-body landing-pricing">
    <div class="container">
      <div class="text-center mb-4">
        <span class="badge bg-label-primary">Planes</span>
      </div>
      <h4 class="text-center mb-1">
        <span class="position-relative fw-extrabold z-1">Planes a tu medida
          <img src="{{ asset('assets/img/front-pages/icons/section-title-icon.png') }}" alt="icono"
            class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
        </span>
      </h4>
      <p class="text-center pb-2 mb-7">[Reemplaza estos planes de ejemplo con tu oferta real, o quita esta sección si no aplica.]</p>
      <div class="text-center mb-12">
        <div class="position-relative d-inline-block pt-3 pt-md-0">
          <label class="switch switch-sm switch-primary me-0">
            <span class="switch-label fs-6 text-body me-3">Pago mensual</span>
            <input type="checkbox" class="switch-input price-duration-toggler" checked />
            <span class="switch-toggle-slider">
              <span class="switch-on"></span>
              <span class="switch-off"></span>
            </span>
            <span class="switch-label fs-6 text-body ms-3">Pago anual</span>
          </label>
        </div>
      </div>
      <div class="row g-6 pt-lg-5">
        @foreach ([['name' => 'Básico', 'monthly' => 'S/ 0', 'yearly' => 'S/ 0', 'items' => ['Funcionalidad básica', 'Soporte por correo', '1 usuario'], 'featured' => false], ['name' => 'Profesional', 'monthly' => 'S/ 0', 'yearly' => 'S/ 0', 'items' => ['Todo lo del plan Básico', 'Soporte prioritario', 'Usuarios ilimitados'], 'featured' => true], ['name' => 'Empresarial', 'monthly' => 'S/ 0', 'yearly' => 'S/ 0', 'items' => ['Todo lo del plan Profesional', 'Integraciones a medida', 'Gerente de cuenta dedicado'], 'featured' => false]] as $plan)
        <div class="col-xl-4 col-lg-6">
          <div class="card {{ $plan['featured'] ? 'border border-primary shadow-xl' : '' }}">
            <div class="card-header">
              <div class="text-center">
                <h4 class="mb-0">{{ $plan['name'] }}</h4>
                <div class="d-flex align-items-center justify-content-center">
                  <span class="price-monthly h2 text-primary fw-extrabold mb-0">{{ $plan['monthly'] }}</span>
                  <span class="price-yearly h2 text-primary fw-extrabold mb-0 d-none">{{ $plan['yearly'] }}</span>
                  <sub class="h6 text-body-secondary mb-n1 ms-1">/mes</sub>
                </div>
              </div>
            </div>
            <div class="card-body">
              <ul class="list-unstyled pricing-list">
                @foreach ($plan['items'] as $item)
                <li>
                  <h6 class="d-flex align-items-center mb-3">
                    <span class="badge badge-center rounded-pill {{ $plan['featured'] ? 'bg-primary' : 'bg-label-primary' }} p-0 me-3"><i
                        class="icon-base ti tabler-check icon-12px"></i></span>
                    {{ $item }}
                  </h6>
                </li>
                @endforeach
              </ul>
              <div class="d-grid mt-8">
                <a href="{{ route('cuenta.register') }}" class="btn {{ $plan['featured'] ? 'btn-primary' : 'btn-label-primary' }}">Comenzar</a>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>
  <!-- Planes: End -->

  <!-- Cifras: Start -->
  <section id="landingFunFacts" class="section-py landing-fun-facts">
    <div class="container">
      <div class="row gy-6">
        @foreach ([['color' => 'primary', 'icon' => 'tabler-users', 'value' => '[0]', 'label' => '[Métrica de ejemplo]'], ['color' => 'success', 'icon' => 'tabler-mood-smile', 'value' => '[0]', 'label' => '[Métrica de ejemplo]'], ['color' => 'info', 'icon' => 'tabler-star', 'value' => '[0]', 'label' => '[Métrica de ejemplo]'], ['color' => 'warning', 'icon' => 'tabler-shield-check', 'value' => '[0]', 'label' => '[Métrica de ejemplo]']] as $fact)
        <div class="col-sm-6 col-lg-3">
          <div class="card border border-{{ $fact['color'] }} shadow-none">
            <div class="card-body text-center">
              <div class="mb-4 text-{{ $fact['color'] }}"><i class="icon-base ti {{ $fact['icon'] }} icon-48px"></i></div>
              <h3 class="mb-0">{{ $fact['value'] }}</h3>
              <p class="fw-medium mb-0">{{ $fact['label'] }}</p>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>
  <!-- Cifras: End -->

  <!-- FAQ: Start -->
  <section id="landingFAQ" class="section-py bg-body landing-faq">
    <div class="container">
      <div class="text-center mb-4">
        <span class="badge bg-label-primary">FAQ</span>
      </div>
      <h4 class="text-center mb-1">
        Preguntas
        <span class="position-relative fw-extrabold z-1">frecuentes
          <img src="{{ asset('assets/img/front-pages/icons/section-title-icon.png') }}" alt="icono"
            class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
        </span>
      </h4>
      <p class="text-center mb-12 pb-md-4">Resuelve tus dudas más comunes aquí.</p>
      <div class="row gy-12 align-items-center justify-content-center">
        <div class="col-lg-8">
          <div class="accordion" id="accordionFAQ">
            @foreach ([['q' => '[Pregunta frecuente 1]', 'a' => '[Respuesta de ejemplo 1. Reemplaza con contenido real.]'], ['q' => '[Pregunta frecuente 2]', 'a' => '[Respuesta de ejemplo 2. Reemplaza con contenido real.]'], ['q' => '[Pregunta frecuente 3]', 'a' => '[Respuesta de ejemplo 3. Reemplaza con contenido real.]']] as $i => $faq)
            <div class="card accordion-item">
              <h2 class="accordion-header" id="heading{{ $i }}">
                <button type="button" class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" data-bs-toggle="collapse"
                  data-bs-target="#accordion{{ $i }}" aria-expanded="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="accordion{{ $i }}">{{ $faq['q'] }}</button>
              </h2>
              <div id="accordion{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $i }}"
                data-bs-parent="#accordionFAQ">
                <div class="accordion-body">{{ $faq['a'] }}</div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- FAQ: End -->

  <!-- CTA: Start -->
  <section id="landingCTA" class="section-py landing-cta position-relative p-lg-0 pb-0">
    <img src="{{ asset('assets/img/front-pages/backgrounds/cta-bg-' . $configData['theme'] . '.png') }}"
      class="position-absolute bottom-0 end-0 scaleX-n1-rtl h-100 w-100 z-n1" alt="fondo"
      data-app-light-img="front-pages/backgrounds/cta-bg-light.png"
      data-app-dark-img="front-pages/backgrounds/cta-bg-dark.png" />
    <div class="container">
      <div class="row align-items-center gy-12">
        <div class="col-lg-6 text-start text-sm-center text-lg-start">
          <h3 class="cta-title text-primary fw-bold mb-1">¿Listo para comenzar?</h3>
          <h5 class="text-body mb-8">[Frase de invitación a la acción de ejemplo.]</h5>
          <a href="{{ route('cuenta.register') }}" class="btn btn-lg btn-primary">Comenzar</a>
        </div>
      </div>
    </div>
  </section>
  <!-- CTA: End -->

  <!-- Contacto: Start -->
  <section id="landingContact" class="section-py bg-body landing-contact">
    <div class="container">
      <div class="text-center mb-4">
        <span class="badge bg-label-primary">Contacto</span>
      </div>
      <h4 class="text-center mb-1">
        <span class="position-relative fw-extrabold z-1">Conversemos
          <img src="{{ asset('assets/img/front-pages/icons/section-title-icon.png') }}" alt="icono"
            class="section-title-img position-absolute object-fit-contain bottom-0 z-n1" />
        </span>
      </h4>
      <p class="text-center mb-12 pb-md-4">¿Alguna pregunta o comentario? Escríbenos.</p>
      <div class="row g-6 justify-content-center">
        <div class="col-lg-5">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="mb-6">Datos de contacto</h5>
              @if(setting('company_email'))
                <div class="d-flex align-items-center mb-4">
                  <div class="badge bg-label-primary rounded p-1_5 me-3"><i class="icon-base ti tabler-mail icon-lg"></i></div>
                  <div>
                    <p class="mb-0">Email</p>
                    <h6 class="mb-0"><a href="mailto:{{ setting('company_email') }}" class="text-heading">{{ setting('company_email') }}</a></h6>
                  </div>
                </div>
              @endif
              @if(setting('company_phone'))
                <div class="d-flex align-items-center">
                  <div class="badge bg-label-success rounded p-1_5 me-3"><i class="icon-base ti tabler-phone-call icon-lg"></i></div>
                  <div>
                    <p class="mb-0">Teléfono</p>
                    <h6 class="mb-0"><a href="tel:{{ setting('company_phone') }}" class="text-heading">{{ setting('company_phone') }}</a></h6>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
        <div class="col-lg-7">
          <div class="card h-100">
            <div class="card-body">
              <h4 class="mb-2">Envíanos un mensaje</h4>
              <p class="mb-6">Completa el formulario y te responderemos a la brevedad.</p>
              <form>
                <div class="row g-4">
                  <div class="col-md-6">
                    <label class="form-label" for="contact-form-fullname">Nombre completo</label>
                    <input type="text" class="form-control" id="contact-form-fullname" placeholder="Juan Pérez" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="contact-form-email">Email</label>
                    <input type="email" id="contact-form-email" class="form-control" placeholder="juan@correo.com" />
                  </div>
                  <div class="col-12">
                    <label class="form-label" for="contact-form-message">Mensaje</label>
                    <textarea id="contact-form-message" class="form-control" rows="7"
                      placeholder="Escribe tu mensaje"></textarea>
                  </div>
                  <div class="col-12">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Contacto: End -->

</div>
@endsection
