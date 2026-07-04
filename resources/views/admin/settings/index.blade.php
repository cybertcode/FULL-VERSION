@extends('admin/layouts/master')

@section('title', 'Configuración del Sistema')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('admin-vendor-style')
  @vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('admin-vendor-script')
  @vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('admin-content')
<div class="row">
  <div class="col-md-12">

    <x-breadcrumb title="Configuración del Sistema" :items="[['label' => 'Configuración']]" />

    @if(setting('maintenance_mode'))
    <div class="alert alert-warning alert-dismissible d-flex align-items-center mb-6" role="alert">
      <span class="alert-icon rounded me-4"><i class="icon-base ti tabler-tool icon-sm"></i></span>
      <div><strong>Modo mantenimiento activo.</strong> El sitio está mostrando la página de mantenimiento a los visitantes.</div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

    <div class="row">

      {{-- ── Sidebar nav vertical ───────────────────────────────────────── --}}
      <div class="col-md-3 col-lg-2 mb-6 mb-md-0">
        <div class="card h-100">
          <div class="card-body p-0">
            <div class="nav flex-column nav-pills py-2" id="settingsNavPills" role="tablist" aria-orientation="vertical">
              @php
              $navItems = [
                ['target' => 'branding',     'icon' => 'tabler-photo',        'label' => 'Identidad'],
                ['target' => 'seo',          'icon' => 'tabler-world-search',  'label' => 'SEO'],
                ['target' => 'company',      'icon' => 'tabler-building',      'label' => 'Empresa'],
                ['target' => 'mail',         'icon' => 'tabler-mail',          'label' => 'Correo'],
                ['target' => 'regional',     'icon' => 'tabler-world',         'label' => 'Regional'],
                ['target' => 'security',     'icon' => 'tabler-shield-lock',   'label' => 'Seguridad'],
                ['target' => 'maintenance',  'icon' => 'tabler-tool',          'label' => 'Mantenimiento'],
                ['target' => 'integrations', 'icon' => 'tabler-plug',          'label' => 'Integraciones'],
                ['target' => 'appearance',   'icon' => 'tabler-palette',       'label' => 'Apariencia'],
              ];
              @endphp
              @foreach($navItems as $i => $item)
              <button class="nav-link w-100 d-flex align-items-center gap-3 px-4 py-2_5 rounded-0 border-0 {{ $i === 0 ? 'active' : '' }}"
                style="text-align:left;" data-bs-toggle="pill" data-bs-target="#tab-{{ $item['target'] }}"
                type="button" role="tab">
                <i class="icon-base ti {{ $item['icon'] }} icon-sm flex-shrink-0"></i>
                <span class="flex-grow-1">{{ $item['label'] }}</span>
                @if($item['target'] === 'maintenance' && setting('maintenance_mode'))
                  <span class="badge bg-warning ms-auto" style="font-size:.65rem;">ON</span>
                @endif
              </button>
              @endforeach
              <div class="border-top mx-3 my-1"></div>
              <button class="nav-link w-100 d-flex align-items-center gap-3 px-4 py-2_5 rounded-0 border-0"
                style="text-align:left;" data-bs-toggle="pill" data-bs-target="#tab-sysinfo" type="button" role="tab">
                <i class="icon-base ti tabler-info-circle icon-sm flex-shrink-0"></i>
                <span class="flex-grow-1">Info del Sistema</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      {{-- ── Tab content ─────────────────────────────────────────────────── --}}
      <div class="col-md-9 col-lg-10">
        <div class="tab-content p-0">


          {{-- ════════════════════════════════════════
               TAB 1 — IDENTIDAD / BRANDING
          ════════════════════════════════════════ --}}
          <div class="tab-pane fade show active" id="tab-branding" role="tabpanel">
            <form action="{{ route('admin.settings.update', 'branding') }}" method="POST" enctype="multipart/form-data">
              @csrf @method('PUT')

              <div class="card mb-6">
                <div class="card-header d-flex align-items-center justify-content-between">
                  <h5 class="mb-0">Logos e Identidad Visual</h5>
                  <span class="badge bg-label-primary">Branding</span>
                </div>
                <div class="card-body">

                  {{-- Logo principal — patrón exacto Vuexy account-settings --}}
                  <div class="d-flex align-items-start align-items-sm-center gap-6 pb-6 border-bottom mb-6">
                    @if(setting('site_logo'))
                      <img src="{{ Storage::url(setting('site_logo')) }}" alt="Logo"
                        id="preview-logo" class="d-block rounded border" style="width:120px;height:70px;object-fit:contain;">
                    @else
                      <div id="preview-logo" class="d-flex align-items-center justify-content-center rounded border bg-lighter" style="width:120px;height:70px;">
                        <i class="icon-base ti tabler-photo text-muted" style="font-size:2rem;"></i>
                      </div>
                    @endif
                    <div class="button-wrapper">
                      <label for="site_logo" class="btn btn-primary me-3 mb-2" tabindex="0">
                        <i class="icon-base ti tabler-upload icon-sm me-1"></i> Subir logo principal
                        <input type="file" id="site_logo" name="site_logo" class="account-file-input" hidden
                          accept="image/jpeg,image/png,image/webp,image/svg+xml"
                          onchange="previewImage(this,'preview-logo')">
                      </label>
                      <p class="mb-1 fw-medium">Logo Principal</p>
                      <p class="text-muted small mb-0">Aparece en el header, sidebar y correos. JPG, PNG, SVG o WEBP — Máx. 2MB.</p>
                      <p class="text-muted small mb-0">Recomendado: 300×80 px.</p>
                      @error('site_logo')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
                    </div>
                  </div>

                  <div class="row g-6">
                    {{-- Logo oscuro --}}
                    <div class="col-md-6">
                      <div class="d-flex align-items-start gap-4">
                        @if(setting('site_logo_dark'))
                          <img src="{{ Storage::url(setting('site_logo_dark')) }}" alt="Logo oscuro"
                            id="preview-logo-dark" class="d-block rounded border bg-dark" style="width:90px;height:56px;object-fit:contain;">
                        @else
                          <div id="preview-logo-dark" class="d-flex align-items-center justify-content-center rounded border bg-dark" style="width:90px;height:56px;">
                            <i class="icon-base ti tabler-moon text-white" style="font-size:1.5rem;"></i>
                          </div>
                        @endif
                        <div>
                          <label for="site_logo_dark" class="btn btn-sm btn-label-secondary mb-2" tabindex="0">
                            <i class="icon-base ti tabler-upload icon-xs me-1"></i> Subir logo oscuro
                            <input type="file" id="site_logo_dark" name="site_logo_dark" class="account-file-input" hidden
                              accept="image/jpeg,image/png,image/webp,image/svg+xml"
                              onchange="previewImage(this,'preview-logo-dark')">
                          </label>
                          <p class="text-muted small mb-0">Versión clara para tema oscuro. PNG/SVG con transparencia.</p>
                          @error('site_logo_dark')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
                        </div>
                      </div>
                    </div>
                    {{-- Favicon --}}
                    <div class="col-md-6">
                      <div class="d-flex align-items-start gap-4">
                        @if(setting('site_favicon'))
                          <img src="{{ Storage::url(setting('site_favicon')) }}" alt="Favicon"
                            id="preview-favicon" class="d-block rounded border" style="width:56px;height:56px;object-fit:contain;">
                        @else
                          <div id="preview-favicon" class="d-flex align-items-center justify-content-center rounded border bg-lighter" style="width:56px;height:56px;">
                            <i class="icon-base ti tabler-star text-muted" style="font-size:1.3rem;"></i>
                          </div>
                        @endif
                        <div>
                          <label for="site_favicon" class="btn btn-sm btn-label-secondary mb-2" tabindex="0">
                            <i class="icon-base ti tabler-upload icon-xs me-1"></i> Subir favicon
                            <input type="file" id="site_favicon" name="site_favicon" class="account-file-input" hidden
                              accept=".ico,image/png,image/svg+xml"
                              onchange="previewImage(this,'preview-favicon')">
                          </label>
                          <p class="text-muted small mb-0">Ícono de pestaña. ICO, PNG o SVG — Máx. 512KB.</p>
                          @error('site_favicon')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

              <div class="card mb-6">
                <h5 class="card-header">Información General</h5>
                <div class="card-body">
                  <div class="row g-6">
                    <div class="col-sm-6">
                      <label class="form-label" for="site_name">Nombre del sistema <span class="text-danger">*</span></label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-app-window icon-sm"></i></span>
                        <input type="text" id="site_name" name="site_name"
                          class="form-control @error('site_name') is-invalid @enderror"
                          value="{{ old('site_name', setting('site_name')) }}"
                          placeholder="Mi Sistema" maxlength="100" required>
                        @error('site_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="form-text">Aparece en el título del navegador, correos y sidebar.</div>
                    </div>
                    <div class="col-sm-6">
                      <label class="form-label" for="site_description">Descripción corta</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-text-caption icon-sm"></i></span>
                        <input type="text" id="site_description" name="site_description"
                          class="form-control @error('site_description') is-invalid @enderror"
                          value="{{ old('site_description', setting('site_description')) }}"
                          placeholder="Sistema de gestión empresarial" maxlength="255">
                        @error('site_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="form-text">Subtítulo descriptivo del sistema.</div>
                    </div>
                  </div>
                </div>
                <div class="card-footer text-end">
                  <button type="submit" class="btn btn-primary">
                    <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar identidad
                  </button>
                </div>
              </div>
            </form>
          </div>


          {{-- ════════════════════════════════════════
               TAB 2 — SEO
          ════════════════════════════════════════ --}}
          <div class="tab-pane fade" id="tab-seo" role="tabpanel">
            <form action="{{ route('admin.settings.update', 'seo') }}" method="POST" enctype="multipart/form-data">
              @csrf @method('PUT')

              <div class="card mb-6">
                <div class="card-header d-flex align-items-center justify-content-between">
                  <h5 class="mb-0">Metadatos de Búsqueda</h5>
                  <span class="badge bg-label-info">SEO</span>
                </div>
                <div class="card-body">
                  <div class="row g-6">

                    {{-- Meta title con contador (patrón Vuexy: oninput counter) --}}
                    <div class="col-sm-8">
                      <label class="form-label" for="seo_title">Meta Title</label>
                      <input type="text" id="seo_title" name="seo_title"
                        class="form-control @error('seo_title') is-invalid @enderror"
                        value="{{ old('seo_title', setting('seo_title')) }}"
                        placeholder="Mi Sistema — Gestión Empresarial" maxlength="160"
                        oninput="charCount(this,'cnt-seo-title',160)">
                      <div class="d-flex justify-content-between mt-1">
                        <span class="form-text">Recomendado: 50–60 caracteres.</span>
                        <span class="form-text text-nowrap"><span id="cnt-seo-title">{{ strlen(old('seo_title', setting('seo_title',''))) }}</span>/160</span>
                      </div>
                      @error('seo_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Select robots — Select2 --}}
                    <div class="col-sm-4">
                      <label class="form-label" for="seo_robots">Indexación (robots)</label>
                      <select id="seo_robots" name="seo_robots" class="select2 form-select @error('seo_robots') is-invalid @enderror">
                        @foreach(['index, follow' => 'index, follow — Indexar todo','noindex, nofollow' => 'noindex, nofollow — Ocultar','noindex, follow' => 'noindex, follow','index, nofollow' => 'index, nofollow'] as $val => $lbl)
                          <option value="{{ $val }}" {{ old('seo_robots', setting('seo_robots')) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                      </select>
                      @error('seo_robots')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Textarea meta description con contador --}}
                    <div class="col-12">
                      <label class="form-label" for="seo_description">Meta Description</label>
                      <textarea id="seo_description" name="seo_description" rows="3"
                        class="form-control @error('seo_description') is-invalid @enderror"
                        maxlength="320" placeholder="Descripción que aparece en los resultados de búsqueda..."
                        oninput="charCount(this,'cnt-seo-desc',320)">{{ old('seo_description', setting('seo_description')) }}</textarea>
                      <div class="d-flex justify-content-between mt-1">
                        <span class="form-text">Recomendado: 120–160 caracteres.</span>
                        <span class="form-text text-nowrap"><span id="cnt-seo-desc">{{ strlen(old('seo_description', setting('seo_description',''))) }}</span>/320</span>
                      </div>
                      @error('seo_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Keywords --}}
                    <div class="col-12">
                      <label class="form-label" for="seo_keywords">Keywords</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-tags icon-sm"></i></span>
                        <input type="text" id="seo_keywords" name="seo_keywords"
                          class="form-control @error('seo_keywords') is-invalid @enderror"
                          value="{{ old('seo_keywords', setting('seo_keywords')) }}"
                          placeholder="gestión, sistema, empresa, perú">
                        @error('seo_keywords')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="form-text">Palabras clave separadas por comas.</div>
                    </div>

                  </div>
                </div>
              </div>

              {{-- OG Image --}}
              <div class="card mb-6">
                <h5 class="card-header">Open Graph — Redes Sociales</h5>
                <div class="card-body">
                  <div class="d-flex align-items-start align-items-sm-center gap-6">
                    @if(setting('seo_og_image'))
                      <img src="{{ Storage::url(setting('seo_og_image')) }}" alt="OG Image"
                        id="preview-og" class="d-block rounded border" style="width:200px;height:110px;object-fit:cover;">
                    @else
                      <div id="preview-og" class="d-flex align-items-center justify-content-center rounded border bg-lighter" style="width:200px;height:110px;">
                        <i class="icon-base ti tabler-share text-muted" style="font-size:2.5rem;"></i>
                      </div>
                    @endif
                    <div class="button-wrapper">
                      <label for="seo_og_image" class="btn btn-primary me-3 mb-2" tabindex="0">
                        <i class="icon-base ti tabler-upload icon-sm me-1"></i> Subir imagen OG
                        <input type="file" id="seo_og_image" name="seo_og_image" class="account-file-input" hidden
                          accept="image/jpeg,image/png,image/webp"
                          onchange="previewImage(this,'preview-og')">
                      </label>
                      <p class="mb-1 fw-medium">Imagen para compartir en redes</p>
                      <p class="text-muted small mb-0">Aparece al compartir la URL en WhatsApp, Facebook, etc.</p>
                      <p class="text-muted small mb-0">Recomendado: 1200×630 px. JPG o PNG — Máx. 2MB.</p>
                      @error('seo_og_image')<p class="text-danger small mt-1">{{ $message }}</p>@enderror
                    </div>
                  </div>
                </div>
                <div class="card-footer text-end">
                  <button type="submit" class="btn btn-primary">
                    <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar SEO
                  </button>
                </div>
              </div>
            </form>
          </div>


          {{-- ════════════════════════════════════════
               TAB 3 — EMPRESA
          ════════════════════════════════════════ --}}
          <div class="tab-pane fade" id="tab-company" role="tabpanel">
            <form action="{{ route('admin.settings.update', 'company') }}" method="POST">
              @csrf @method('PUT')

              <div class="card mb-6">
                <div class="card-header d-flex align-items-center justify-content-between">
                  <h5 class="mb-0">Datos Legales y de Contacto</h5>
                  <span class="badge bg-label-secondary">Empresa</span>
                </div>
                <div class="card-body">
                  <div class="row g-6">

                    <div class="col-sm-6">
                      <label class="form-label" for="company_name">Razón social</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-building icon-sm"></i></span>
                        <input type="text" id="company_name" name="company_name"
                          class="form-control @error('company_name') is-invalid @enderror"
                          value="{{ old('company_name', setting('company_name')) }}"
                          placeholder="Mi Empresa S.A.C." maxlength="150">
                        @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <label class="form-label" for="company_ruc">RUC</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-id icon-sm"></i></span>
                        <input type="number" id="company_ruc" name="company_ruc"
                          class="form-control @error('company_ruc') is-invalid @enderror"
                          value="{{ old('company_ruc', setting('company_ruc')) }}"
                          placeholder="20000000000" min="0" max="99999999999" step="1"
                          oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)">
                        @error('company_ruc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <label class="form-label" for="company_type">Tipo de empresa</label>
                      <select id="company_type" name="company_type" class="select2 form-select @error('company_type') is-invalid @enderror">
                        @foreach(['SAC'=>'S.A.C.','SA'=>'S.A.','SRL'=>'S.R.L.','EIRL'=>'E.I.R.L.','SAS'=>'S.A.S.','OTRO'=>'Otro'] as $val=>$lbl)
                          <option value="{{ $val }}" {{ old('company_type', setting('company_type')) === $val ? 'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                      </select>
                      @error('company_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- email — type="email" para validación nativa --}}
                    <div class="col-sm-6">
                      <label class="form-label" for="company_email">Email de contacto</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-mail icon-sm"></i></span>
                        <input type="email" id="company_email" name="company_email"
                          class="form-control @error('company_email') is-invalid @enderror"
                          value="{{ old('company_email', setting('company_email')) }}"
                          placeholder="contacto@empresa.com" maxlength="150">
                        @error('company_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                    {{-- teléfono — inputmode tel --}}
                    <div class="col-sm-6">
                      <label class="form-label" for="company_phone">Teléfono</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-phone icon-sm"></i></span>
                        <input type="tel" id="company_phone" name="company_phone"
                          class="form-control @error('company_phone') is-invalid @enderror"
                          value="{{ old('company_phone', setting('company_phone')) }}"
                          placeholder="+51 999 999 999" maxlength="30"
                          pattern="[\+0-9\s\-\(\)]+" title="Solo dígitos, +, espacios, guiones y paréntesis"
                          oninput="this.value=this.value.replace(/[^0-9\+\s\-\(\)]/g,'')">
                        @error('company_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                    <div class="col-sm-8">
                      <label class="form-label" for="company_address">Dirección</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-map-pin icon-sm"></i></span>
                        <input type="text" id="company_address" name="company_address"
                          class="form-control @error('company_address') is-invalid @enderror"
                          value="{{ old('company_address', setting('company_address')) }}"
                          placeholder="Av. Principal 123, Lima, Perú" maxlength="255">
                        @error('company_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                    {{-- URL — type="url" con validación nativa --}}
                    <div class="col-sm-4">
                      <label class="form-label" for="company_website">Sitio web</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-world icon-sm"></i></span>
                        <input type="url" id="company_website" name="company_website"
                          class="form-control @error('company_website') is-invalid @enderror"
                          value="{{ old('company_website', setting('company_website')) }}"
                          placeholder="https://miempresa.com">
                        @error('company_website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <div class="card mb-6">
                <h5 class="card-header">Redes Sociales</h5>
                <div class="card-body">
                  <div class="row g-6">
                    @foreach([
                      'social_facebook'  => ['icon'=>'tabler-brand-facebook',  'label'=>'Facebook',   'placeholder'=>'https://facebook.com/tuempresa'],
                      'social_instagram' => ['icon'=>'tabler-brand-instagram', 'label'=>'Instagram',  'placeholder'=>'https://instagram.com/tuempresa'],
                      'social_twitter'   => ['icon'=>'tabler-brand-twitter',   'label'=>'Twitter / X','placeholder'=>'https://twitter.com/tuempresa'],
                      'social_linkedin'  => ['icon'=>'tabler-brand-linkedin',  'label'=>'LinkedIn',   'placeholder'=>'https://linkedin.com/company/tuempresa'],
                      'social_youtube'   => ['icon'=>'tabler-brand-youtube',   'label'=>'YouTube',    'placeholder'=>'https://youtube.com/@tucanal'],
                      'social_tiktok'    => ['icon'=>'tabler-brand-tiktok',    'label'=>'TikTok',     'placeholder'=>'https://tiktok.com/@tuempresa'],
                    ] as $field => $cfg)
                    <div class="col-sm-6">
                      <label class="form-label" for="{{ $field }}">{{ $cfg['label'] }}</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti {{ $cfg['icon'] }} icon-sm"></i></span>
                        <input type="url" id="{{ $field }}" name="{{ $field }}"
                          class="form-control @error($field) is-invalid @enderror"
                          value="{{ old($field, setting($field)) }}"
                          placeholder="{{ $cfg['placeholder'] }}">
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>
                    @endforeach

                    {{-- WhatsApp — numérico, no URL --}}
                    <div class="col-sm-6">
                      <label class="form-label" for="social_whatsapp">WhatsApp</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-brand-whatsapp icon-sm"></i></span>
                        <input type="number" id="social_whatsapp" name="social_whatsapp"
                          class="form-control @error('social_whatsapp') is-invalid @enderror"
                          value="{{ old('social_whatsapp', setting('social_whatsapp')) }}"
                          placeholder="51999999999" min="0" step="1"
                          oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,15)">
                        @error('social_whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="form-text">Solo números con código de país (sin + ni espacios).</div>
                    </div>
                  </div>
                </div>
                <div class="card-footer text-end">
                  <button type="submit" class="btn btn-primary">
                    <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar empresa
                  </button>
                </div>
              </div>
            </form>
          </div>


          {{-- ════════════════════════════════════════
               TAB 4 — CORREO
          ════════════════════════════════════════ --}}
          <div class="tab-pane fade" id="tab-mail" role="tabpanel">
            <form action="{{ route('admin.settings.update', 'mail') }}" method="POST">
              @csrf @method('PUT')

              <div class="card mb-6">
                <div class="card-header d-flex align-items-center justify-content-between">
                  <h5 class="mb-0">Remitente del Sistema</h5>
                  <span class="badge bg-label-info">Correo enviado a usuarios</span>
                </div>
                <div class="card-body">
                  <div class="row g-6">
                    <div class="col-sm-6">
                      <label class="form-label" for="mail_from_name">Nombre del remitente</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-user icon-sm"></i></span>
                        <input type="text" id="mail_from_name" name="mail_from_name"
                          class="form-control @error('mail_from_name') is-invalid @enderror"
                          value="{{ old('mail_from_name', setting('mail_from_name')) }}"
                          placeholder="Mi Sistema" maxlength="100">
                        @error('mail_from_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="form-text">El destinatario verá este nombre como remitente.</div>
                    </div>
                    <div class="col-sm-6">
                      <label class="form-label" for="mail_from_address">Email remitente</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-at icon-sm"></i></span>
                        <input type="email" id="mail_from_address" name="mail_from_address"
                          class="form-control @error('mail_from_address') is-invalid @enderror"
                          value="{{ old('mail_from_address', setting('mail_from_address')) }}"
                          placeholder="noreply@miempresa.com" maxlength="150">
                        @error('mail_from_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card mb-6">
                <h5 class="card-header">Servidor SMTP</h5>
                <div class="card-body">
                  <div class="row g-6">

                    <div class="col-sm-4">
                      <label class="form-label" for="mail_driver">Driver</label>
                      <select id="mail_driver" name="mail_driver" class="select2 form-select @error('mail_driver') is-invalid @enderror">
                        @foreach(['smtp'=>'SMTP','sendmail'=>'Sendmail','mailgun'=>'Mailgun','ses'=>'Amazon SES','log'=>'Log (desarrollo)'] as $val=>$lbl)
                          <option value="{{ $val }}" {{ old('mail_driver', setting('mail_driver','smtp')) === $val ? 'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                      </select>
                      @error('mail_driver')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-sm-5">
                      <label class="form-label" for="mail_host">Host SMTP</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-server icon-sm"></i></span>
                        <input type="text" id="mail_host" name="mail_host"
                          class="form-control @error('mail_host') is-invalid @enderror"
                          value="{{ old('mail_host', setting('mail_host')) }}"
                          placeholder="smtp.mailtrap.io">
                        @error('mail_host')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                    {{-- Puerto — type="number" con min/max --}}
                    <div class="col-sm-3">
                      <label class="form-label" for="mail_port">Puerto</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-plug icon-sm"></i></span>
                        <input type="number" id="mail_port" name="mail_port"
                          class="form-control @error('mail_port') is-invalid @enderror"
                          value="{{ old('mail_port', setting('mail_port','587')) }}"
                          placeholder="587" min="1" max="65535">
                        @error('mail_port')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                    <div class="col-sm-4">
                      <label class="form-label" for="mail_encryption">Cifrado</label>
                      <select id="mail_encryption" name="mail_encryption" class="select2 form-select @error('mail_encryption') is-invalid @enderror">
                        <option value="tls" {{ old('mail_encryption', setting('mail_encryption')) === 'tls' ? 'selected':'' }}>TLS (recomendado)</option>
                        <option value="ssl" {{ old('mail_encryption', setting('mail_encryption')) === 'ssl' ? 'selected':'' }}>SSL</option>
                        <option value=""   {{ old('mail_encryption', setting('mail_encryption')) === ''    ? 'selected':'' }}>Ninguno</option>
                      </select>
                      @error('mail_encryption')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-sm-4">
                      <label class="form-label" for="mail_username">Usuario SMTP</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-user icon-sm"></i></span>
                        <input type="text" id="mail_username" name="mail_username"
                          class="form-control @error('mail_username') is-invalid @enderror"
                          value="{{ old('mail_username', setting('mail_username')) }}"
                          placeholder="usuario@smtp.com" autocomplete="off">
                        @error('mail_username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                    {{-- Password — patrón .form-password-toggle de Vuexy (helpers.js lo auto-inicializa) --}}
                    <div class="col-sm-4 form-password-toggle">
                      <label class="form-label" for="mail_password">Contraseña SMTP</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-lock icon-sm"></i></span>
                        <input type="password" id="mail_password" name="mail_password"
                          class="form-control @error('mail_password') is-invalid @enderror"
                          value="{{ old('mail_password', setting('mail_password')) }}"
                          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" autocomplete="new-password">
                        <span class="input-group-text cursor-pointer">
                          <i class="icon-base ti tabler-eye-off icon-sm"></i>
                        </span>
                        @error('mail_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <div class="card mb-6">
                <h5 class="card-header">Prueba de configuración</h5>
                <div class="card-body">
                  <p class="mb-4 text-muted">Envía un correo de prueba para verificar que la configuración SMTP funciona correctamente.</p>
                  <div class="row g-4 align-items-end">
                    <div class="col-sm-7">
                      <label class="form-label" for="test_mail_address">Enviar correo de prueba a</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-mail icon-sm"></i></span>
                        <input type="email" id="test_mail_address" class="form-control"
                          placeholder="tu@email.com" value="{{ auth()->user()->email }}">
                      </div>
                    </div>
                    <div class="col-sm-5">
                      <button type="button" id="btn-test-mail" class="btn btn-label-primary w-100"
                        onclick="sendTestMail()">
                        <i class="icon-base ti tabler-send icon-sm me-1"></i> Enviar correo de prueba
                      </button>
                    </div>
                  </div>
                  <div id="test-mail-result" class="mt-4" style="display:none;"></div>
                </div>
                <div class="card-footer text-end">
                  <button type="submit" class="btn btn-primary">
                    <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar correo
                  </button>
                </div>
              </div>
            </form>
          </div>


          {{-- ════════════════════════════════════════
               TAB 5 — REGIONAL
          ════════════════════════════════════════ --}}
          <div class="tab-pane fade" id="tab-regional" role="tabpanel">
            <form action="{{ route('admin.settings.update', 'regional') }}" method="POST">
              @csrf @method('PUT')

              <div class="card mb-6">
                <div class="card-header d-flex align-items-center justify-content-between">
                  <h5 class="mb-0">Localización y Formato</h5>
                  <span class="badge bg-label-success">Regional</span>
                </div>
                <div class="card-body">
                  <div class="row g-6">

                    {{-- Timezone — Select2 con búsqueda (400+ opciones) --}}
                    <div class="col-sm-6">
                      <label class="form-label" for="timezone">
                        <i class="icon-base ti tabler-clock icon-xs me-1 text-muted"></i> Zona horaria
                      </label>
                      <select id="timezone" name="timezone" class="select2 form-select @error('timezone') is-invalid @enderror">
                        @foreach(\DateTimeZone::listIdentifiers() as $tz)
                          <option value="{{ $tz }}" {{ old('timezone', setting('timezone','America/Lima')) === $tz ? 'selected':'' }}>{{ $tz }}</option>
                        @endforeach
                      </select>
                      @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      <div class="form-text">Hora local del servidor para fechas y logs.</div>
                    </div>

                    <div class="col-sm-6">
                      <label class="form-label" for="default_language">
                        <i class="icon-base ti tabler-language icon-xs me-1 text-muted"></i> Idioma por defecto
                      </label>
                      <select id="default_language" name="default_language" class="select2 form-select @error('default_language') is-invalid @enderror">
                        <option value="es" {{ old('default_language', setting('default_language','es')) === 'es' ? 'selected':'' }}>🇵🇪 Español</option>
                        <option value="en" {{ old('default_language', setting('default_language','es')) === 'en' ? 'selected':'' }}>🇺🇸 English</option>
                      </select>
                      @error('default_language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-sm-4">
                      <label class="form-label" for="date_format">Formato de fecha</label>
                      <select id="date_format" name="date_format" class="select2 form-select @error('date_format') is-invalid @enderror">
                        <option value="d/m/Y" {{ old('date_format', setting('date_format')) === 'd/m/Y' ? 'selected':'' }}>DD/MM/YYYY — {{ date('d/m/Y') }}</option>
                        <option value="m/d/Y" {{ old('date_format', setting('date_format')) === 'm/d/Y' ? 'selected':'' }}>MM/DD/YYYY — {{ date('m/d/Y') }}</option>
                        <option value="Y-m-d" {{ old('date_format', setting('date_format')) === 'Y-m-d' ? 'selected':'' }}>YYYY-MM-DD — {{ date('Y-m-d') }}</option>
                        <option value="d M Y" {{ old('date_format', setting('date_format')) === 'd M Y' ? 'selected':'' }}>DD Mon YYYY — {{ date('d M Y') }}</option>
                      </select>
                      @error('date_format')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Moneda — input-group con símbolo --}}
                    <div class="col-sm-4">
                      <label class="form-label" for="currency_symbol">Símbolo de moneda</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-currency-dollar icon-sm"></i></span>
                        <input type="text" id="currency_symbol" name="currency_symbol"
                          class="form-control @error('currency_symbol') is-invalid @enderror"
                          value="{{ old('currency_symbol', setting('currency_symbol','S/')) }}"
                          placeholder="S/" maxlength="10">
                        @error('currency_symbol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                    {{-- Decimales — type="number" min/max --}}
                    <div class="col-sm-2">
                      <label class="form-label" for="currency_decimals">Decimales</label>
                      <select id="currency_decimals" name="currency_decimals" class="select2 form-select @error('currency_decimals') is-invalid @enderror">
                        @foreach([0,1,2,3] as $d)
                          <option value="{{ $d }}" {{ (int) old('currency_decimals', setting('currency_decimals',2)) === $d ? 'selected':'' }}>
                            {{ $d }} — {{ number_format(1234.5,$d) }}
                          </option>
                        @endforeach
                      </select>
                      @error('currency_decimals')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-sm-2">
                      <label class="form-label" for="pagination_per_page">Registros/página</label>
                      <select id="pagination_per_page" name="pagination_per_page" class="select2 form-select @error('pagination_per_page') is-invalid @enderror">
                        @foreach([10,15,25,50,100] as $n)
                          <option value="{{ $n }}" {{ (int) old('pagination_per_page', setting('pagination_per_page',15)) === $n ? 'selected':'' }}>{{ $n }}</option>
                        @endforeach
                      </select>
                      @error('pagination_per_page')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      <div class="form-text">Aplica en todos los listados.</div>
                    </div>

                  </div>
                </div>
                <div class="card-footer text-end">
                  <button type="submit" class="btn btn-primary">
                    <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar regional
                  </button>
                </div>
              </div>
            </form>
          </div>


          {{-- ════════════════════════════════════════
               TAB 6 — SEGURIDAD
          ════════════════════════════════════════ --}}
          <div class="tab-pane fade" id="tab-security" role="tabpanel">
            <form action="{{ route('admin.settings.update', 'security') }}" method="POST">
              @csrf @method('PUT')

              <div class="card mb-6">
                <div class="card-header d-flex align-items-center justify-content-between">
                  <h5 class="mb-0">Sesiones y Control de Acceso</h5>
                  <span class="badge bg-label-danger">Seguridad</span>
                </div>
                <div class="card-body">
                  <div class="row g-6">

                    {{-- Números con min/max --}}
                    <div class="col-sm-4">
                      <label class="form-label" for="session_lifetime">Duración de sesión <span class="text-muted small">(minutos)</span></label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-clock icon-sm"></i></span>
                        <input type="number" id="session_lifetime" name="session_lifetime"
                          class="form-control @error('session_lifetime') is-invalid @enderror"
                          value="{{ old('session_lifetime', setting('session_lifetime',120)) }}"
                          min="5" max="10080" placeholder="120">
                        <span class="input-group-text">min</span>
                        @error('session_lifetime')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="form-text">120 min = 2 horas. Máx: 10080 (7 días).</div>
                    </div>

                    <div class="col-sm-4">
                      <label class="form-label" for="login_max_attempts">Intentos de login máximos</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-key icon-sm"></i></span>
                        <input type="number" id="login_max_attempts" name="login_max_attempts"
                          class="form-control @error('login_max_attempts') is-invalid @enderror"
                          value="{{ old('login_max_attempts', setting('login_max_attempts',5)) }}"
                          min="1" max="20" placeholder="5">
                        <span class="input-group-text">intentos</span>
                        @error('login_max_attempts')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="form-text">Intentos antes del bloqueo temporal.</div>
                    </div>

                    <div class="col-sm-4">
                      <label class="form-label" for="login_lockout_minutes">Tiempo de bloqueo <span class="text-muted small">(minutos)</span></label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-lock icon-sm"></i></span>
                        <input type="number" id="login_lockout_minutes" name="login_lockout_minutes"
                          class="form-control @error('login_lockout_minutes') is-invalid @enderror"
                          value="{{ old('login_lockout_minutes', setting('login_lockout_minutes',15)) }}"
                          min="1" max="1440" placeholder="15">
                        <span class="input-group-text">min</span>
                        @error('login_lockout_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <div class="card mb-6">
                <h5 class="card-header">Autenticación y Verificación</h5>
                <div class="card-body">
                  <div class="row g-6">

                    {{-- Switches — patrón Vuexy form-check form-switch --}}
                    <div class="col-12">
                      <div class="d-flex align-items-start justify-content-between p-4 border rounded">
                        <div>
                          <h6 class="mb-1">Forzar autenticación de dos factores (2FA)</h6>
                          <p class="text-muted small mb-0">Obliga a todos los usuarios a activar 2FA antes de acceder al panel.</p>
                        </div>
                        <div class="form-check form-switch ms-4 mt-1">
                          <input class="form-check-input" type="checkbox" id="force_2fa" name="force_2fa"
                            role="switch" value="1" {{ old('force_2fa', setting('force_2fa')) ? 'checked':'' }}>
                          <label class="form-check-label" for="force_2fa"></label>
                        </div>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="d-flex align-items-start justify-content-between p-4 border rounded">
                        <div>
                          <h6 class="mb-1">Activar CAPTCHA en el login</h6>
                          <p class="text-muted small mb-0">Protege el formulario de login con Google reCAPTCHA v2.</p>
                        </div>
                        <div class="form-check form-switch ms-4 mt-1">
                          <input class="form-check-input" type="checkbox" id="captcha_enabled" name="captcha_enabled"
                            role="switch" value="1" {{ old('captcha_enabled', setting('captcha_enabled')) ? 'checked':'' }}>
                          <label class="form-check-label" for="captcha_enabled"></label>
                        </div>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="d-flex align-items-start justify-content-between p-4 border rounded">
                        <div>
                          <h6 class="mb-1">Permitir registro público</h6>
                          <p class="text-muted small mb-0">Si se desactiva, se oculta el enlace "Crear cuenta" y la ruta /register queda inaccesible.</p>
                        </div>
                        <div class="form-check form-switch ms-4 mt-1">
                          <input class="form-check-input" type="checkbox" id="registration_enabled" name="registration_enabled"
                            role="switch" value="1" {{ old('registration_enabled', setting('registration_enabled', true)) ? 'checked':'' }}>
                          <label class="form-check-label" for="registration_enabled"></label>
                        </div>
                      </div>
                    </div>

                    {{-- API keys — password toggle --}}
                    <div class="col-sm-6 form-password-toggle">
                      <label class="form-label" for="captcha_site_key">reCAPTCHA Site Key</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-key icon-sm"></i></span>
                        <input type="password" id="captcha_site_key" name="captcha_site_key"
                          class="form-control @error('captcha_site_key') is-invalid @enderror"
                          value="{{ old('captcha_site_key', setting('captcha_site_key')) }}"
                          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" autocomplete="new-password">
                        <span class="input-group-text cursor-pointer">
                          <i class="icon-base ti tabler-eye-off icon-sm"></i>
                        </span>
                        @error('captcha_site_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                    <div class="col-sm-6 form-password-toggle">
                      <label class="form-label" for="captcha_secret_key">reCAPTCHA Secret Key</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-key icon-sm"></i></span>
                        <input type="password" id="captcha_secret_key" name="captcha_secret_key"
                          class="form-control @error('captcha_secret_key') is-invalid @enderror"
                          value="{{ old('captcha_secret_key', setting('captcha_secret_key')) }}"
                          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" autocomplete="new-password">
                        <span class="input-group-text cursor-pointer">
                          <i class="icon-base ti tabler-eye-off icon-sm"></i>
                        </span>
                        @error('captcha_secret_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <div class="card mb-6">
                <h5 class="card-header">Política de contraseñas</h5>
                <div class="card-body">
                  <div class="row g-6">

                    <div class="col-sm-4">
                      <label class="form-label" for="password_min_length">Longitud mínima</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-ruler icon-sm"></i></span>
                        <input type="number" id="password_min_length" name="password_min_length"
                          class="form-control @error('password_min_length') is-invalid @enderror"
                          value="{{ old('password_min_length', setting('password_min_length', 8)) }}"
                          min="6" max="64" placeholder="8">
                        <span class="input-group-text">caracteres</span>
                        @error('password_min_length')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="d-flex align-items-start justify-content-between p-4 border rounded">
                        <div>
                          <h6 class="mb-1">Exigir mayúsculas y minúsculas</h6>
                          <p class="text-muted small mb-0">La contraseña debe combinar letras mayúsculas y minúsculas.</p>
                        </div>
                        <div class="form-check form-switch ms-4 mt-1">
                          <input class="form-check-input" type="checkbox" id="password_require_mixed" name="password_require_mixed"
                            role="switch" value="1" {{ old('password_require_mixed', setting('password_require_mixed', false)) ? 'checked':'' }}>
                          <label class="form-check-label" for="password_require_mixed"></label>
                        </div>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="d-flex align-items-start justify-content-between p-4 border rounded">
                        <div>
                          <h6 class="mb-1">Exigir al menos un número</h6>
                          <p class="text-muted small mb-0">La contraseña debe incluir al menos un dígito.</p>
                        </div>
                        <div class="form-check form-switch ms-4 mt-1">
                          <input class="form-check-input" type="checkbox" id="password_require_numbers" name="password_require_numbers"
                            role="switch" value="1" {{ old('password_require_numbers', setting('password_require_numbers', false)) ? 'checked':'' }}>
                          <label class="form-check-label" for="password_require_numbers"></label>
                        </div>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="d-flex align-items-start justify-content-between p-4 border rounded">
                        <div>
                          <h6 class="mb-1">Exigir símbolo especial</h6>
                          <p class="text-muted small mb-0">La contraseña debe incluir al menos un carácter especial (!@#$...).</p>
                        </div>
                        <div class="form-check form-switch ms-4 mt-1">
                          <input class="form-check-input" type="checkbox" id="password_require_symbols" name="password_require_symbols"
                            role="switch" value="1" {{ old('password_require_symbols', setting('password_require_symbols', false)) ? 'checked':'' }}>
                          <label class="form-check-label" for="password_require_symbols"></label>
                        </div>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="d-flex align-items-start justify-content-between p-4 border rounded">
                        <div>
                          <h6 class="mb-1">Verificar contra filtraciones conocidas</h6>
                          <p class="text-muted small mb-0">Rechaza contraseñas expuestas en filtraciones públicas (Have I Been Pwned, vía Laravel).</p>
                        </div>
                        <div class="form-check form-switch ms-4 mt-1">
                          <input class="form-check-input" type="checkbox" id="password_check_breach" name="password_check_breach"
                            role="switch" value="1" {{ old('password_check_breach', setting('password_check_breach', false)) ? 'checked':'' }}>
                          <label class="form-check-label" for="password_check_breach"></label>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <div class="card mb-6">
                <h5 class="card-header">Restricción de acceso al panel</h5>
                <div class="card-body">
                  <label class="form-label" for="allowed_ips_admin">IPs permitidas para el panel <span class="text-muted small">(opcional)</span></label>
                  <textarea id="allowed_ips_admin" name="allowed_ips_admin" rows="3"
                    class="form-control @error('allowed_ips_admin') is-invalid @enderror"
                    placeholder="192.168.1.1, 10.0.0.5">{{ old('allowed_ips_admin', setting('allowed_ips_admin')) }}</textarea>
                  <div class="form-text">
                    IPs separadas por coma. Tu IP actual: <code>{{ request()->ip() }}</code>
                    <span class="text-warning fw-medium">— Asegúrate de incluirla antes de guardar.</span>
                  </div>
                  @error('allowed_ips_admin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="card-footer text-end">
                  <button type="submit" class="btn btn-primary">
                    <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar seguridad
                  </button>
                </div>
              </div>
            </form>
          </div>


          {{-- ════════════════════════════════════════
               TAB 7 — MANTENIMIENTO
          ════════════════════════════════════════ --}}
          <div class="tab-pane fade" id="tab-maintenance" role="tabpanel">
            <form action="{{ route('admin.settings.update', 'maintenance') }}" method="POST">
              @csrf @method('PUT')

              <div class="card mb-6">
                <div class="card-header d-flex align-items-center justify-content-between">
                  <h5 class="mb-0">Modo Mantenimiento</h5>
                  @if(setting('maintenance_mode'))
                    <span class="badge bg-warning">ACTIVO</span>
                  @else
                    <span class="badge bg-label-secondary">Inactivo</span>
                  @endif
                </div>
                <div class="card-body">

                  <div class="d-flex align-items-start justify-content-between p-4 border rounded mb-6
                    {{ setting('maintenance_mode') ? 'border-warning bg-label-warning' : '' }}">
                    <div>
                      <h6 class="mb-1">Activar modo mantenimiento</h6>
                      <p class="text-muted small mb-0">Los visitantes verán la página de mantenimiento. Los Super-Admin y las IPs en whitelist pueden seguir accediendo.</p>
                    </div>
                    <div class="form-check form-switch ms-4 mt-1">
                      <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode"
                        role="switch" value="1" {{ old('maintenance_mode', setting('maintenance_mode')) ? 'checked':'' }}>
                      <label class="form-check-label" for="maintenance_mode"></label>
                    </div>
                  </div>

                  <div class="row g-6">
                    <div class="col-12">
                      <label class="form-label" for="maintenance_message">Mensaje para visitantes</label>
                      <textarea id="maintenance_message" name="maintenance_message" rows="3"
                        class="form-control @error('maintenance_message') is-invalid @enderror"
                        maxlength="500" placeholder="El sistema se encuentra en mantenimiento. Vuelve pronto."
                        oninput="charCount(this,'cnt-maint-msg',500)">{{ old('maintenance_message', setting('maintenance_message','El sistema se encuentra en mantenimiento. Vuelve pronto.')) }}</textarea>
                      <div class="d-flex justify-content-between mt-1">
                        <span class="form-text">Este mensaje se muestra en la página de mantenimiento.</span>
                        <span class="form-text text-nowrap"><span id="cnt-maint-msg">{{ strlen(old('maintenance_message', setting('maintenance_message',''))) }}</span>/500</span>
                      </div>
                      @error('maintenance_message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                      <label class="form-label" for="maintenance_ips">IPs con acceso durante el mantenimiento</label>
                      <textarea id="maintenance_ips" name="maintenance_ips" rows="2"
                        class="form-control @error('maintenance_ips') is-invalid @enderror"
                        placeholder="192.168.1.1, 203.0.113.25">{{ old('maintenance_ips', setting('maintenance_ips')) }}</textarea>
                      <div class="form-text">IPs separadas por coma. Tu IP actual: <code>{{ request()->ip() }}</code></div>
                      @error('maintenance_ips')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                  </div>

                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                  <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-label-secondary">
                    <i class="icon-base ti tabler-external-link icon-sm me-1"></i> Ver página de mantenimiento
                  </a>
                  <button type="submit" class="btn btn-primary">
                    <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar mantenimiento
                  </button>
                </div>
              </div>
            </form>
          </div>


          {{-- ════════════════════════════════════════
               TAB 8 — INTEGRACIONES
          ════════════════════════════════════════ --}}
          <div class="tab-pane fade" id="tab-integrations" role="tabpanel">
            <form action="{{ route('admin.settings.update', 'integrations') }}" method="POST">
              @csrf @method('PUT')

              {{-- Google --}}
              <div class="card mb-6">
                <div class="card-header d-flex align-items-center gap-3">
                  <div class="avatar avatar-sm">
                    <span class="avatar-initial rounded bg-label-danger"><i class="icon-base ti tabler-brand-google icon-sm"></i></span>
                  </div>
                  <h5 class="mb-0">Google</h5>
                </div>
                <div class="card-body">
                  <div class="row g-6">
                    <div class="col-sm-6">
                      <label class="form-label" for="google_analytics_id">Google Analytics 4 — Measurement ID</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-chart-bar icon-sm"></i></span>
                        <input type="text" id="google_analytics_id" name="google_analytics_id"
                          class="form-control @error('google_analytics_id') is-invalid @enderror"
                          value="{{ old('google_analytics_id', setting('google_analytics_id')) }}"
                          placeholder="G-XXXXXXXXXX" maxlength="50">
                        @error('google_analytics_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="form-text">Empieza con <code>G-</code>.</div>
                    </div>
                    <div class="col-sm-6">
                      <label class="form-label" for="gtm_id">Google Tag Manager — Container ID</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-tag icon-sm"></i></span>
                        <input type="text" id="gtm_id" name="gtm_id"
                          class="form-control @error('gtm_id') is-invalid @enderror"
                          value="{{ old('gtm_id', setting('gtm_id')) }}"
                          placeholder="GTM-XXXXXXX" maxlength="50">
                        @error('gtm_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="form-text">Empieza con <code>GTM-</code>.</div>
                    </div>
                    {{-- Google Maps key — password toggle para ocultar API key --}}
                    <div class="col-12 form-password-toggle">
                      <label class="form-label" for="google_maps_key">Google Maps — API Key</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-map icon-sm"></i></span>
                        <input type="password" id="google_maps_key" name="google_maps_key"
                          class="form-control @error('google_maps_key') is-invalid @enderror"
                          value="{{ old('google_maps_key', setting('google_maps_key')) }}"
                          placeholder="AIza..." autocomplete="new-password">
                        <span class="input-group-text cursor-pointer">
                          <i class="icon-base ti tabler-eye-off icon-sm"></i>
                        </span>
                        @error('google_maps_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="form-text">Disponible via <code>config('services.google_maps.key')</code>.</div>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Meta --}}
              <div class="card mb-6">
                <div class="card-header d-flex align-items-center gap-3">
                  <div class="avatar avatar-sm">
                    <span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-brand-facebook icon-sm"></i></span>
                  </div>
                  <h5 class="mb-0">Meta / Facebook</h5>
                </div>
                <div class="card-body">
                  <div class="row g-6">
                    <div class="col-sm-6">
                      <label class="form-label" for="meta_pixel_id">Meta Pixel ID</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-brand-meta icon-sm"></i></span>
                        <input type="number" id="meta_pixel_id" name="meta_pixel_id"
                          class="form-control @error('meta_pixel_id') is-invalid @enderror"
                          value="{{ old('meta_pixel_id', setting('meta_pixel_id')) }}"
                          placeholder="1234567890123456" min="0" step="1"
                          oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,20)">
                        @error('meta_pixel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="form-text">ID numérico del pixel de Meta Ads.</div>
                    </div>
                  </div>
                </div>
              </div>

              {{-- reCAPTCHA --}}
              <div class="card mb-6">
                <div class="card-header d-flex align-items-center gap-3">
                  <div class="avatar avatar-sm">
                    <span class="avatar-initial rounded bg-label-success"><i class="icon-base ti tabler-shield-check icon-sm"></i></span>
                  </div>
                  <h5 class="mb-0">Google reCAPTCHA v2</h5>
                </div>
                <div class="card-body">
                  <div class="alert alert-info d-flex align-items-center mb-6" role="alert">
                    <i class="icon-base ti tabler-info-circle icon-sm me-3 flex-shrink-0"></i>
                    Las claves de reCAPTCHA también se pueden configurar en la sección <strong>Seguridad</strong>.
                  </div>
                  <div class="row g-6">
                    <div class="col-sm-6 form-password-toggle">
                      <label class="form-label" for="recaptcha_site_key">Site Key (pública)</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-key icon-sm"></i></span>
                        <input type="password" id="recaptcha_site_key" name="recaptcha_site_key"
                          class="form-control @error('recaptcha_site_key') is-invalid @enderror"
                          value="{{ old('recaptcha_site_key', setting('recaptcha_site_key')) }}"
                          placeholder="6Le..." autocomplete="new-password">
                        <span class="input-group-text cursor-pointer">
                          <i class="icon-base ti tabler-eye-off icon-sm"></i>
                        </span>
                        @error('recaptcha_site_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>
                    <div class="col-sm-6 form-password-toggle">
                      <label class="form-label" for="recaptcha_secret_key">Secret Key (privada)</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-lock icon-sm"></i></span>
                        <input type="password" id="recaptcha_secret_key" name="recaptcha_secret_key"
                          class="form-control @error('recaptcha_secret_key') is-invalid @enderror"
                          value="{{ old('recaptcha_secret_key', setting('recaptcha_secret_key')) }}"
                          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" autocomplete="new-password">
                        <span class="input-group-text cursor-pointer">
                          <i class="icon-base ti tabler-eye-off icon-sm"></i>
                        </span>
                        @error('recaptcha_secret_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer text-end">
                  <button type="submit" class="btn btn-primary">
                    <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar integraciones
                  </button>
                </div>
              </div>
            </form>
          </div>


          {{-- ════════════════════════════════════════
               TAB 9 — APARIENCIA
          ════════════════════════════════════════ --}}
          <div class="tab-pane fade" id="tab-appearance" role="tabpanel">
            <form action="{{ route('admin.settings.update', 'appearance') }}" method="POST">
              @csrf @method('PUT')

              <div class="card mb-6">
                <div class="card-header d-flex align-items-center justify-content-between">
                  <h5 class="mb-0">Color y Personalización</h5>
                  <span class="badge bg-label-warning">Apariencia</span>
                </div>
                <div class="card-body">
                  <div class="row g-6">

                    {{-- Color picker — patrón Vuexy: type="color" nativo + input text sincronizado --}}
                    <div class="col-sm-5">
                      <label class="form-label" for="primary_color">Color primario del sistema</label>
                      <div class="input-group">
                        <input type="color" id="primary_color_picker"
                          class="form-control form-control-color"
                          style="width:50px;min-width:50px;padding:.375rem .25rem;cursor:pointer;"
                          value="{{ old('primary_color', setting('primary_color','#7367F0')) }}"
                          oninput="document.getElementById('primary_color').value=this.value;document.getElementById('primary_color_preview').style.background=this.value">
                        <input type="text" id="primary_color" name="primary_color"
                          class="form-control @error('primary_color') is-invalid @enderror"
                          value="{{ old('primary_color', setting('primary_color','#7367F0')) }}"
                          placeholder="#7367F0" maxlength="7" pattern="^#[0-9A-Fa-f]{6}$"
                          oninput="if(/^#[0-9A-Fa-f]{6}$/.test(this.value)){document.getElementById('primary_color_picker').value=this.value;document.getElementById('primary_color_preview').style.background=this.value}">
                        <span class="input-group-text p-1">
                          <span id="primary_color_preview" class="d-inline-block rounded"
                            style="width:28px;height:28px;background:{{ old('primary_color', setting('primary_color','#7367F0')) }};border:1px solid rgba(0,0,0,.1)"></span>
                        </span>
                        @error('primary_color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="form-text">Color principal del tema. Formato hexadecimal (#RRGGBB).</div>
                    </div>

                  </div>
                </div>
              </div>

              <div class="card mb-6">
                <h5 class="card-header">Términos y Privacidad</h5>
                <div class="card-body">
                  <div class="row g-6">
                    <div class="col-sm-6">
                      <label class="form-label" for="terms_url">URL de Términos y Condiciones</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-file-text icon-sm"></i></span>
                        <input type="url" id="terms_url" name="terms_url"
                          class="form-control @error('terms_url') is-invalid @enderror"
                          value="{{ old('terms_url', setting('terms_url')) }}"
                          placeholder="https://miempresa.com/terminos">
                        @error('terms_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                      <div class="form-text">Aparece en el footer y en el formulario de registro.</div>
                    </div>
                    <div class="col-sm-6">
                      <label class="form-label" for="privacy_url">URL de Política de Privacidad</label>
                      <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="icon-base ti tabler-shield icon-sm"></i></span>
                        <input type="url" id="privacy_url" name="privacy_url"
                          class="form-control @error('privacy_url') is-invalid @enderror"
                          value="{{ old('privacy_url', setting('privacy_url')) }}"
                          placeholder="https://miempresa.com/privacidad">
                        @error('privacy_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer text-end">
                  <button type="submit" class="btn btn-primary">
                    <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar apariencia
                  </button>
                </div>
              </div>
            </form>
          </div>


          {{-- ════════════════════════════════════════
               TAB 10 — INFO DEL SISTEMA
          ════════════════════════════════════════ --}}
          <div class="tab-pane fade" id="tab-sysinfo" role="tabpanel">

            <div class="row g-6 mb-6">
              <div class="col-md-6">
                <div class="card h-100">
                  <div class="card-header d-flex align-items-center gap-3">
                    <div class="avatar avatar-sm"><span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-versions icon-sm"></i></span></div>
                    <h5 class="mb-0">Versiones</h5>
                  </div>
                  <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                      @foreach([
                        ['label'=>'Aplicación', 'value'=>$systemInfo['app_version'],     'icon'=>'tabler-app-window'],
                        ['label'=>'PHP',        'value'=>$systemInfo['php_version'],      'icon'=>'tabler-brand-php'],
                        ['label'=>'Laravel',    'value'=>$systemInfo['laravel_version'],  'icon'=>'tabler-brand-laravel'],
                        ['label'=>'Entorno',    'value'=>$systemInfo['environment'],      'icon'=>'tabler-leaf'],
                        ['label'=>'Debug',      'value'=>$systemInfo['debug_mode'],       'icon'=>'tabler-bug'],
                      ] as $row)
                      <li class="list-group-item d-flex justify-content-between align-items-center px-6 py-3">
                        <span class="d-flex align-items-center gap-2 text-muted small">
                          <i class="icon-base ti {{ $row['icon'] }} icon-xs"></i> {{ $row['label'] }}
                        </span>
                        <span class="badge bg-label-secondary">{{ $row['value'] }}</span>
                      </li>
                      @endforeach
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card h-100">
                  <div class="card-header d-flex align-items-center gap-3">
                    <div class="avatar avatar-sm"><span class="avatar-initial rounded bg-label-info"><i class="icon-base ti tabler-server icon-sm"></i></span></div>
                    <h5 class="mb-0">Infraestructura</h5>
                  </div>
                  <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                      @foreach([
                        ['label'=>'Sistema operativo','value'=>$systemInfo['server_os'],       'icon'=>'tabler-device-desktop'],
                        ['label'=>'Servidor web',     'value'=>$systemInfo['server_software'], 'icon'=>'tabler-server'],
                        ['label'=>'Base de datos',    'value'=>$systemInfo['db_driver'],       'icon'=>'tabler-database'],
                        ['label'=>'Cache driver',     'value'=>$systemInfo['cache_driver'],    'icon'=>'tabler-bolt'],
                        ['label'=>'Queue driver',     'value'=>$systemInfo['queue_driver'],    'icon'=>'tabler-list'],
                      ] as $row)
                      <li class="list-group-item d-flex justify-content-between align-items-center px-6 py-3">
                        <span class="d-flex align-items-center gap-2 text-muted small">
                          <i class="icon-base ti {{ $row['icon'] }} icon-xs"></i> {{ $row['label'] }}
                        </span>
                        <span class="badge bg-label-secondary">{{ $row['value'] }}</span>
                      </li>
                      @endforeach
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <div class="card mb-6">
              <div class="card-header d-flex align-items-center gap-3">
                <div class="avatar avatar-sm"><span class="avatar-initial rounded bg-label-warning"><i class="icon-base ti tabler-database icon-sm"></i></span></div>
                <h5 class="mb-0">Almacenamiento en disco</h5>
              </div>
              <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted small">Espacio usado</span>
                  <span class="fw-medium small">{{ $systemInfo['disk_used_pct'] }}%</span>
                </div>
                <div class="progress mb-4" style="height:10px;">
                  <div class="progress-bar {{ $systemInfo['disk_used_pct'] > 85 ? 'bg-danger' : ($systemInfo['disk_used_pct'] > 70 ? 'bg-warning' : 'bg-success') }}"
                    style="width:{{ $systemInfo['disk_used_pct'] }}%;" role="progressbar"></div>
                </div>
                <div class="row text-center g-4">
                  @foreach([['Total',$systemInfo['disk_total']],['Libre',$systemInfo['disk_free']],['Zona horaria',$systemInfo['timezone']]] as [$lbl,$val])
                  <div class="col-4">
                    <div class="p-3 border rounded">
                      <p class="text-muted small mb-1">{{ $lbl }}</p>
                      <h6 class="mb-0">{{ $val }}</h6>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>

            <div class="card">
              <h5 class="card-header">Acciones de mantenimiento</h5>
              <div class="card-body">
                <div class="row g-4">
                  @foreach(['optimize:clear'=>['tabler-refresh','Limpiar caché'],'config:cache'=>['tabler-settings','Cachear config'],'route:cache'=>['tabler-route','Cachear rutas'],'view:cache'=>['tabler-eye','Cachear vistas']] as $cmd=>[$ico,$lbl])
                  <div class="col-sm-6 col-md-3">
                    <button type="button" class="btn btn-label-secondary w-100" onclick="runArtisan('{{ $cmd }}')">
                      <i class="icon-base ti {{ $ico }} icon-sm me-1"></i> {{ $lbl }}
                    </button>
                  </div>
                  @endforeach
                </div>
                <div id="artisan-result" class="mt-4" style="display:none;"></div>
              </div>
            </div>

          </div>{{-- /tab-sysinfo --}}

        </div>{{-- /tab-content --}}
      </div>{{-- /col contenido --}}
    </div>{{-- /row layout --}}

  </div>
</div>
@endsection


@section('admin-page-script')
<script>
'use strict';

// ── Select2 — espera jQuery (módulo ES de Vuexy) ─────────────────────────────
window.addEventListener('load', function () {
  var select2 = $('.select2');
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>');
      $this.select2({ dropdownParent: $this.parent() });
    });
  }
});

// ── Tab: activar el que tiene errores, o el indicado en URL ──────────────────
(function () {
  var invalid = document.querySelector('.is-invalid');
  if (invalid) {
    var pane = invalid.closest('.tab-pane');
    if (pane) { new bootstrap.Tab(document.querySelector('[data-bs-target="#' + pane.id + '"]')).show(); return; }
  }
  var tab = new URLSearchParams(window.location.search).get('tab');
  if (tab) {
    var btn = document.querySelector('[data-bs-target="#tab-' + tab + '"]');
    if (btn) new bootstrap.Tab(btn).show();
  }
})();

// ── File upload preview — patrón Vuexy: URL.createObjectURL ──────────────────
function previewImage(input, targetId) {
  if (!input.files || !input.files[0]) return;
  var target = document.getElementById(targetId);
  if (!target) return;
  var url = URL.createObjectURL(input.files[0]);
  if (target.tagName === 'IMG') {
    target.src = url;
  } else {
    var img = document.createElement('img');
    img.id = targetId;
    img.className = 'd-block rounded border';
    img.style.cssText = target.style.cssText;
    img.style.objectFit = 'contain';
    img.src = url;
    target.replaceWith(img);
  }
}

// ── Contador de caracteres (textarea / input con maxlength) ───────────────────
function charCount(el, counterId, max) {
  var len = el.value.length;
  var counter = document.getElementById(counterId);
  if (!counter) return;
  counter.textContent = len;
  counter.className = len >= max ? 'text-danger fw-medium' : (len >= max * 0.85 ? 'text-warning fw-medium' : '');
}

// ── Correo de prueba ──────────────────────────────────────────────────────────
function sendTestMail() {
  var email  = document.getElementById('test_mail_address').value.trim();
  var btn    = document.getElementById('btn-test-mail');
  var result = document.getElementById('test-mail-result');
  if (!email) { showToast('warning', 'Ingresa un email de destino.'); return; }
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enviando...';
  fetch('{{ route('admin.settings.test-mail') }}', {
    method: 'POST',
    headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json' },
    body: JSON.stringify({ email: email })
  })
  .then(function(r){ return r.json(); })
  .then(function(data){
    result.style.display = 'block';
    result.innerHTML = '<div class="alert alert-' + (data.success ? 'success':'danger') + ' d-flex align-items-center"><i class="icon-base ti ' + (data.success ? 'tabler-check':'tabler-x') + ' icon-sm me-2"></i>' + data.message + '</div>';
  })
  .catch(function(){ result.style.display='block'; result.innerHTML='<div class="alert alert-danger">Error de conexión. Intenta de nuevo.</div>'; })
  .finally(function(){ btn.disabled=false; btn.innerHTML='<i class="icon-base ti tabler-send icon-sm me-1"></i> Enviar correo de prueba'; });
}

// ── Acciones Artisan ──────────────────────────────────────────────────────────
function runArtisan(command) {
  var result = document.getElementById('artisan-result');
  result.style.display = 'block';
  result.innerHTML = '<div class="alert alert-secondary"><span class="spinner-border spinner-border-sm me-2"></span> Ejecutando <code>' + command + '</code>...</div>';
  fetch('{{ route('admin.settings.artisan') }}', {
    method: 'POST',
    headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json' },
    body: JSON.stringify({ command: command })
  })
  .then(function(r){ return r.json(); })
  .then(function(data){
    result.innerHTML = '<div class="alert alert-' + (data.success ? 'success':'danger') + ' d-flex align-items-center"><i class="icon-base ti ' + (data.success ? 'tabler-check':'tabler-x') + ' icon-sm me-2"></i>' + data.message + '</div>';
  })
  .catch(function(){ result.innerHTML = '<div class="alert alert-info">Comando enviado. Revisa los logs si es necesario.</div>'; });
}
</script>
@endsection
