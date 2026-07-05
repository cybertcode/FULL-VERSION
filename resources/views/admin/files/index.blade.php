@extends('admin/layouts/master')

@section('title', 'Gestor de Archivos')

@section('admin-content')
<div class="row">
  <div class="col-12">

    <x-breadcrumb title="Gestor de Archivos" :items="[['label' => 'Archivos']]" />

    {{-- Selector de tipo + iframe --}}
    <div class="card overflow-hidden">

      {{-- Cabecera con selector pill --}}
      <div class="card-header d-flex align-items-center justify-content-between py-3 px-4">
        <div>
          <h6 class="mb-0 fw-semibold">Archivos del sistema</h6>
          <small class="text-body-secondary">Usa <strong>+</strong> para subir archivos o crear carpetas</small>
        </div>
        <div class="lfm-type-switcher" role="group">
          <button type="button" class="lfm-type-btn active" data-target="tab-images" data-bs-toggle="tab" data-bs-target="#tab-images">
            <i class="ti tabler-photo"></i>
            <span>Imágenes</span>
          </button>
          <button type="button" class="lfm-type-btn" data-target="tab-files" data-bs-toggle="tab" data-bs-target="#tab-files">
            <i class="ti tabler-files"></i>
            <span>Documentos</span>
          </button>
        </div>
      </div>

      {{-- Contenido --}}
      <div class="card-body p-0">
        <div class="tab-content">

          <div class="tab-pane fade show active" id="tab-images" role="tabpanel">
            <div class="lfm-wrapper">
              <iframe id="lfm-images" src="{{ route('unisharp.lfm.show') }}?type=image"
                class="lfm-frame" frameborder="0" allowfullscreen></iframe>
            </div>
          </div>

          <div class="tab-pane fade" id="tab-files" role="tabpanel">
            <div class="lfm-wrapper">
              <iframe id="lfm-files" src="{{ route('unisharp.lfm.show') }}?type=file"
                class="lfm-frame" frameborder="0" allowfullscreen></iframe>
            </div>
          </div>

        </div>
      </div>
    </div>

    {{-- Info de uso --}}
    <div class="row g-3 mt-1">
      <div class="col-md-4">
        <div class="card border-0 bg-label-primary mb-0">
          <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="avatar">
              <span class="avatar-initial rounded bg-primary">
                <i class="icon-base ti tabler-photo icon-22px text-white"></i>
              </span>
            </div>
            <div>
              <p class="mb-0 fw-medium small">Imágenes soportadas</p>
              <small class="text-body-secondary">JPG, PNG, GIF, WebP, SVG</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 bg-label-info mb-0">
          <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="avatar">
              <span class="avatar-initial rounded bg-info">
                <i class="icon-base ti tabler-file-description icon-22px text-white"></i>
              </span>
            </div>
            <div>
              <p class="mb-0 fw-medium small">Documentos soportados</p>
              <small class="text-body-secondary">PDF, Word, Excel, PowerPoint, ZIP, TXT, CSV</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 bg-label-warning mb-0">
          <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="avatar">
              <span class="avatar-initial rounded bg-warning">
                <i class="icon-base ti tabler-shield-check icon-22px text-white"></i>
              </span>
            </div>
            <div>
              <p class="mb-0 fw-medium small">Archivos bloqueados</p>
              <small class="text-body-secondary">PHP, HTML, EXE, BAT, SH, JS</small>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('admin-page-script')
<style>
  /* ── Iframe ── */
  .lfm-wrapper { width: 100%; height: 680px; }
  .lfm-frame   { width: 100%; height: 100%; border: none; display: block; }

  /* ── Selector pill tipo segmented control ── */
  .lfm-type-switcher {
    display: inline-flex;
    align-items: center;
    background: var(--bs-body-bg, #f8f7fa);
    border: 1px solid var(--bs-border-color, #e6e6e8);
    border-radius: 8px;
    padding: 3px;
    gap: 2px;
  }
  .lfm-type-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 16px;
    border: none;
    border-radius: 6px;
    background: transparent;
    color: var(--bs-secondary-color, #6d6b77);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: background .18s, color .18s, box-shadow .18s;
    white-space: nowrap;
    line-height: 1.4;
  }
  .lfm-type-btn i {
    font-size: 15px;
    line-height: 1;
  }
  .lfm-type-btn:hover:not(.active) {
    background: rgba(var(--bs-primary-rgb, 19,64,160), .06);
    color: var(--bs-primary, #1340A0);
  }
  .lfm-type-btn.active {
    background: var(--bs-primary, #1340A0);
    color: #fff;
    box-shadow: 0 3px 10px rgba(var(--bs-primary-rgb, 19,64,160), .35);
  }
</style>
<script>
  // Sincronizar clase active en los botones pill con el tab de Bootstrap
  document.querySelectorAll('.lfm-type-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.lfm-type-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
    });
  });
</script>
@endsection
