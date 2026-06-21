@extends('admin/layouts/master')

@section('title', 'Configuración del Sistema')

@section('admin-content')
<div class="row">
  <div class="col-md-12">

    {{-- Breadcrumb --}}
    <x-breadcrumb
      title="Configuración del Sistema"
      :items="[['label' => 'Configuración']]"
    />

    {{-- Nav Pills --}}
    <div class="nav-align-top">
      <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-branding" type="button" role="tab">
            <i class="icon-base ti tabler-photo icon-sm me-1_5"></i> Identidad
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-seo" type="button" role="tab">
            <i class="icon-base ti tabler-world-search icon-sm me-1_5"></i> SEO
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-company" type="button" role="tab">
            <i class="icon-base ti tabler-building icon-sm me-1_5"></i> Empresa
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-mail" type="button" role="tab">
            <i class="icon-base ti tabler-mail icon-sm me-1_5"></i> Correo
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-regional" type="button" role="tab">
            <i class="icon-base ti tabler-world icon-sm me-1_5"></i> Regional
          </button>
        </li>
      </ul>
    </div>

    <div class="tab-content p-0">

      {{-- ══════════════════════════════
           TAB IDENTIDAD / BRANDING
      ══════════════════════════════ --}}
      <div class="tab-pane fade show active" id="tab-branding" role="tabpanel">
        <form action="{{ route('admin.settings.update', 'branding') }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          {{-- Logos --}}
          <div class="card mb-6">
            <h5 class="card-header">Logos e Identidad Visual</h5>
            <div class="card-body">

              {{-- Logo principal --}}
              <div class="d-flex align-items-start align-items-sm-center gap-6 pb-6 border-bottom mb-6">
                @if(setting('site_logo'))
                  <img src="{{ Storage::url(setting('site_logo')) }}" alt="Logo"
                    id="preview-logo" class="d-block rounded border"
                    style="width:120px;height:70px;object-fit:contain;">
                @else
                  <div id="preview-logo" class="d-flex align-items-center justify-content-center rounded border bg-lighter"
                    style="width:120px;height:70px;">
                    <i class="icon-base ti tabler-photo text-muted" style="font-size:2rem;"></i>
                  </div>
                @endif
                <div class="button-wrapper">
                  <label for="site_logo" class="btn btn-primary me-3 mb-2" tabindex="0">
                    <i class="icon-base ti tabler-upload icon-sm d-sm-none"></i>
                    <span class="d-none d-sm-block">Subir logo principal</span>
                    <input type="file" id="site_logo" name="site_logo" class="account-file-input" hidden
                      accept=".jpg,.jpeg,.png,.webp,.svg"
                      onchange="previewFile(this, 'preview-logo')">
                  </label>
                  <p class="mb-1"><strong>Logo Principal</strong></p>
                  <p class="text-muted mb-0">Se muestra en el header y correos del sistema.</p>
                  <p class="text-muted small mb-0">JPG, PNG, SVG o WEBP. Máx. 2MB.</p>
                  @error('site_logo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
              </div>

              {{-- Logo oscuro y Favicon --}}
              <div class="row g-6">
                <div class="col-md-6">
                  <div class="d-flex align-items-start align-items-sm-center gap-6">
                    @if(setting('site_logo_dark'))
                      <img src="{{ Storage::url(setting('site_logo_dark')) }}" alt="Logo oscuro"
                        id="preview-logo-dark" class="d-block rounded border bg-dark"
                        style="width:90px;height:56px;object-fit:contain;">
                    @else
                      <div id="preview-logo-dark" class="d-flex align-items-center justify-content-center rounded border bg-dark"
                        style="width:90px;height:56px;">
                        <i class="icon-base ti tabler-moon text-white" style="font-size:1.5rem;"></i>
                      </div>
                    @endif
                    <div class="button-wrapper">
                      <label for="site_logo_dark" class="btn btn-label-secondary me-3 mb-2" tabindex="0">
                        <span class="d-none d-sm-block">Subir logo oscuro</span>
                        <input type="file" id="site_logo_dark" name="site_logo_dark" class="account-file-input" hidden
                          accept=".jpg,.jpeg,.png,.webp,.svg"
                          onchange="previewFile(this, 'preview-logo-dark')">
                      </label>
                      <p class="mb-1"><strong>Tema oscuro</strong></p>
                      <p class="text-muted small mb-0">Versión clara del logo (fondo oscuro). PNG o SVG transparente.</p>
                      @error('site_logo_dark')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="d-flex align-items-start align-items-sm-center gap-6">
                    @if(setting('site_favicon'))
                      <img src="{{ Storage::url(setting('site_favicon')) }}" alt="Favicon"
                        id="preview-favicon" class="d-block rounded border"
                        style="width:56px;height:56px;object-fit:contain;">
                    @else
                      <div id="preview-favicon" class="d-flex align-items-center justify-content-center rounded border bg-lighter"
                        style="width:56px;height:56px;">
                        <i class="icon-base ti tabler-star text-muted" style="font-size:1.3rem;"></i>
                      </div>
                    @endif
                    <div class="button-wrapper">
                      <label for="site_favicon" class="btn btn-label-secondary me-3 mb-2" tabindex="0">
                        <span class="d-none d-sm-block">Subir favicon</span>
                        <input type="file" id="site_favicon" name="site_favicon" class="account-file-input" hidden
                          accept=".ico,.png,.svg"
                          onchange="previewFile(this, 'preview-favicon')">
                      </label>
                      <p class="mb-1"><strong>Favicon</strong></p>
                      <p class="text-muted small mb-0">Ícono de pestaña del navegador. ICO, PNG o SVG. Máx. 512KB.</p>
                      @error('site_favicon')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>

          {{-- Nombre y descripción --}}
          <div class="card mb-6">
            <h5 class="card-header">Información General</h5>
            <div class="card-body">
              <div class="row g-6">
                <div class="col-sm-6">
                  <label class="form-label" for="site_name">Nombre del sistema <span class="text-danger">*</span></label>
                  <input type="text" id="site_name" name="site_name"
                    class="form-control @error('site_name') is-invalid @enderror"
                    value="{{ old('site_name', setting('site_name')) }}"
                    placeholder="Mi Sistema" required>
                  <div class="form-text">Aparece en el título del navegador y en los correos.</div>
                  @error('site_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-6">
                  <label class="form-label" for="site_description">Descripción corta</label>
                  <input type="text" id="site_description" name="site_description"
                    class="form-control @error('site_description') is-invalid @enderror"
                    value="{{ old('site_description', setting('site_description')) }}"
                    placeholder="Sistema de gestión empresarial">
                  <div class="form-text">Subtítulo opcional que describe el sistema.</div>
                  @error('site_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>
            <div class="card-footer text-end">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar cambios
              </button>
            </div>
          </div>

        </form>
      </div>

      {{-- ══════════════════════════════
           TAB SEO
      ══════════════════════════════ --}}
      <div class="tab-pane fade" id="tab-seo" role="tabpanel">
        <form action="{{ route('admin.settings.update', 'seo') }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <div class="card mb-6">
            <h5 class="card-header">Metadatos de Búsqueda</h5>
            <div class="card-body">
              <div class="row g-6">

                <div class="col-sm-8">
                  <label class="form-label" for="seo_title">Meta Title</label>
                  <input type="text" id="seo_title" name="seo_title"
                    class="form-control @error('seo_title') is-invalid @enderror"
                    value="{{ old('seo_title', setting('seo_title')) }}"
                    placeholder="Mi Sistema — Gestión Empresarial" maxlength="160"
                    oninput="document.getElementById('cnt-title').textContent=this.value.length">
                  <div class="d-flex justify-content-between mt-1">
                    <span class="form-text">Aparece en la pestaña del navegador y en Google.</span>
                    <span class="form-text"><span id="cnt-title">{{ strlen(setting('seo_title','')) }}</span>/160</span>
                  </div>
                  @error('seo_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-sm-4">
                  <label class="form-label" for="seo_robots">Indexación (robots)</label>
                  <select id="seo_robots" name="seo_robots" class="form-select @error('seo_robots') is-invalid @enderror">
                    <option value="index, follow"     {{ setting('seo_robots') === 'index, follow'     ? 'selected' : '' }}>index, follow — Indexar</option>
                    <option value="noindex, nofollow" {{ setting('seo_robots') === 'noindex, nofollow' ? 'selected' : '' }}>noindex, nofollow — Ocultar</option>
                    <option value="noindex, follow"   {{ setting('seo_robots') === 'noindex, follow'   ? 'selected' : '' }}>noindex, follow</option>
                    <option value="index, nofollow"   {{ setting('seo_robots') === 'index, nofollow'   ? 'selected' : '' }}>index, nofollow</option>
                  </select>
                  @error('seo_robots')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                  <label class="form-label" for="seo_description">Meta Description</label>
                  <textarea id="seo_description" name="seo_description" rows="3"
                    class="form-control @error('seo_description') is-invalid @enderror"
                    maxlength="320" placeholder="Descripción que aparece en los resultados de búsqueda..."
                    oninput="document.getElementById('cnt-desc').textContent=this.value.length">{{ old('seo_description', setting('seo_description')) }}</textarea>
                  <div class="d-flex justify-content-between mt-1">
                    <span class="form-text">Recomendado: entre 120 y 160 caracteres.</span>
                    <span class="form-text"><span id="cnt-desc">{{ strlen(setting('seo_description','')) }}</span>/320</span>
                  </div>
                  @error('seo_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                  <label class="form-label" for="seo_keywords">Keywords</label>
                  <input type="text" id="seo_keywords" name="seo_keywords"
                    class="form-control @error('seo_keywords') is-invalid @enderror"
                    value="{{ old('seo_keywords', setting('seo_keywords')) }}"
                    placeholder="gestión, sistema, empresa, perú">
                  <div class="form-text">Palabras clave separadas por comas.</div>
                  @error('seo_keywords')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

              </div>
            </div>
          </div>

          <div class="card mb-6">
            <h5 class="card-header">Open Graph — Redes Sociales</h5>
            <div class="card-body">
              <div class="d-flex align-items-start align-items-sm-center gap-6">
                @if(setting('seo_og_image'))
                  <img src="{{ Storage::url(setting('seo_og_image')) }}" alt="OG Image"
                    id="preview-og" class="d-block rounded border"
                    style="width:200px;height:110px;object-fit:cover;">
                @else
                  <div id="preview-og" class="d-flex align-items-center justify-content-center rounded border bg-lighter"
                    style="width:200px;height:110px;">
                    <i class="icon-base ti tabler-share text-muted" style="font-size:2.5rem;"></i>
                  </div>
                @endif
                <div class="button-wrapper">
                  <label for="seo_og_image" class="btn btn-primary me-3 mb-2" tabindex="0">
                    <i class="icon-base ti tabler-upload icon-sm d-sm-none"></i>
                    <span class="d-none d-sm-block">Subir imagen OG</span>
                    <input type="file" id="seo_og_image" name="seo_og_image" class="account-file-input" hidden
                      accept=".jpg,.jpeg,.png,.webp"
                      onchange="previewFile(this, 'preview-og')">
                  </label>
                  <p class="mb-1"><strong>Imagen para compartir en redes</strong></p>
                  <p class="text-muted mb-0">Aparece al compartir la URL en WhatsApp, Facebook, Twitter, etc.</p>
                  <p class="text-muted small mb-0">Tamaño recomendado: 1200×630px. JPG o PNG. Máx. 2MB.</p>
                  @error('seo_og_image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>
            <div class="card-footer text-end">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar cambios
              </button>
            </div>
          </div>

        </form>
      </div>

      {{-- ══════════════════════════════
           TAB EMPRESA
      ══════════════════════════════ --}}
      <div class="tab-pane fade" id="tab-company" role="tabpanel">
        <form action="{{ route('admin.settings.update', 'company') }}" method="POST">
          @csrf
          @method('PUT')

          <div class="card mb-6">
            <h5 class="card-header">Datos de la Empresa</h5>
            <div class="card-body">
              <div class="row g-6">

                <div class="col-sm-6">
                  <label class="form-label" for="company_name">Nombre legal</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base ti tabler-building icon-sm"></i></span>
                    <input type="text" id="company_name" name="company_name"
                      class="form-control @error('company_name') is-invalid @enderror"
                      value="{{ old('company_name', setting('company_name')) }}"
                      placeholder="Mi Empresa S.A.C.">
                    @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>

                <div class="col-sm-6">
                  <label class="form-label" for="company_email">Email de contacto</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base ti tabler-mail icon-sm"></i></span>
                    <input type="email" id="company_email" name="company_email"
                      class="form-control @error('company_email') is-invalid @enderror"
                      value="{{ old('company_email', setting('company_email')) }}"
                      placeholder="contacto@empresa.com">
                    @error('company_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>

                <div class="col-sm-6">
                  <label class="form-label" for="company_phone">Teléfono</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base ti tabler-phone icon-sm"></i></span>
                    <input type="text" id="company_phone" name="company_phone"
                      class="form-control @error('company_phone') is-invalid @enderror"
                      value="{{ old('company_phone', setting('company_phone')) }}"
                      placeholder="+51 999 999 999">
                    @error('company_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>

                <div class="col-sm-6">
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

                <div class="col-12">
                  <label class="form-label" for="company_address">Dirección</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base ti tabler-map-pin icon-sm"></i></span>
                    <input type="text" id="company_address" name="company_address"
                      class="form-control @error('company_address') is-invalid @enderror"
                      value="{{ old('company_address', setting('company_address')) }}"
                      placeholder="Av. Principal 123, Lima, Perú">
                    @error('company_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>

              </div>
            </div>
            <div class="card-footer text-end">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar cambios
              </button>
            </div>
          </div>

        </form>
      </div>

      {{-- ══════════════════════════════
           TAB CORREO
      ══════════════════════════════ --}}
      <div class="tab-pane fade" id="tab-mail" role="tabpanel">
        <form action="{{ route('admin.settings.update', 'mail') }}" method="POST">
          @csrf
          @method('PUT')

          <div class="card mb-6">
            <div class="d-flex align-items-center justify-content-between card-header">
              <h5 class="mb-0">Remitente del Sistema</h5>
              <span class="badge bg-label-info">
                <i class="icon-base ti tabler-info-circle icon-xs me-1"></i>
                Correos automáticos, notificaciones y recuperación de contraseña
              </span>
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
                      placeholder="Mi Sistema">
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
                      placeholder="noreply@miempresa.com">
                    @error('mail_from_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                  <div class="form-text">Debe coincidir con <code>MAIL_FROM_ADDRESS</code> en <code>.env</code>.</div>
                </div>

              </div>
            </div>
            <div class="card-footer text-end">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar cambios
              </button>
            </div>
          </div>

        </form>
      </div>

      {{-- ══════════════════════════════
           TAB REGIONAL
      ══════════════════════════════ --}}
      <div class="tab-pane fade" id="tab-regional" role="tabpanel">
        <form action="{{ route('admin.settings.update', 'regional') }}" method="POST">
          @csrf
          @method('PUT')

          <div class="card mb-6">
            <h5 class="card-header">Localización y Formato</h5>
            <div class="card-body">
              <div class="row g-6">

                <div class="col-sm-6">
                  <label class="form-label" for="timezone">Zona horaria</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base ti tabler-clock icon-sm"></i></span>
                    <select id="timezone" name="timezone" class="form-select @error('timezone') is-invalid @enderror">
                      @foreach(\DateTimeZone::listIdentifiers() as $tz)
                        <option value="{{ $tz }}" {{ setting('timezone', 'America/Lima') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                      @endforeach
                    </select>
                    @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                  <div class="form-text">Hora local del servidor para fechas y logs.</div>
                </div>

                <div class="col-sm-6">
                  <label class="form-label" for="default_language">Idioma por defecto</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base ti tabler-language icon-sm"></i></span>
                    <select id="default_language" name="default_language" class="form-select @error('default_language') is-invalid @enderror">
                      <option value="es" {{ setting('default_language', 'es') === 'es' ? 'selected' : '' }}>Español</option>
                      <option value="en" {{ setting('default_language', 'es') === 'en' ? 'selected' : '' }}>English</option>
                    </select>
                    @error('default_language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>

                <div class="col-sm-4">
                  <label class="form-label" for="date_format">Formato de fecha</label>
                  <select id="date_format" name="date_format" class="form-select @error('date_format') is-invalid @enderror">
                    <option value="d/m/Y" {{ setting('date_format') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY — 31/12/2025</option>
                    <option value="m/d/Y" {{ setting('date_format') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY — 12/31/2025</option>
                    <option value="Y-m-d" {{ setting('date_format') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD — 2025-12-31</option>
                  </select>
                  @error('date_format')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-sm-4">
                  <label class="form-label" for="currency_symbol">Símbolo de moneda</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base ti tabler-currency-dollar icon-sm"></i></span>
                    <input type="text" id="currency_symbol" name="currency_symbol"
                      class="form-control @error('currency_symbol') is-invalid @enderror"
                      value="{{ old('currency_symbol', setting('currency_symbol', 'S/')) }}"
                      placeholder="S/" maxlength="10">
                    @error('currency_symbol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>

                <div class="col-sm-4">
                  <label class="form-label" for="currency_decimals">Decimales</label>
                  <select id="currency_decimals" name="currency_decimals" class="form-select @error('currency_decimals') is-invalid @enderror">
                    @foreach([0, 1, 2, 3] as $d)
                      <option value="{{ $d }}" {{ (int) setting('currency_decimals', 2) === $d ? 'selected' : '' }}>
                        {{ $d }} decimal{{ $d !== 1 ? 'es' : '' }} — {{ number_format(1234.5, $d) }}
                      </option>
                    @endforeach
                  </select>
                  @error('currency_decimals')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

              </div>
            </div>
            <div class="card-footer text-end">
              <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar cambios
              </button>
            </div>
          </div>

        </form>
      </div>

    </div>{{-- /tab-content --}}
  </div>
</div>
@endsection

@section('admin-page-script')
<script>
  function previewFile(input, targetId) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
      const target = document.getElementById(targetId);
      if (!target) return;
      if (target.tagName === 'IMG') {
        target.src = e.target.result;
      } else {
        // reemplazar el div placeholder por una img
        const img = document.createElement('img');
        img.id = targetId;
        img.className = target.className.replace('d-flex align-items-center justify-content-center', 'd-block');
        img.style.cssText = target.style.cssText;
        img.style.objectFit = 'contain';
        img.src = e.target.result;
        target.replaceWith(img);
      }
    };
    reader.readAsDataURL(input.files[0]);
  }
</script>
@endsection
