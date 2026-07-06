<form action="{{ $action }}" method="POST">
  @csrf
  @if ($method === 'PUT') @method('PUT') @endif

  <div class="row g-6">
    {{-- ─── Columna principal ──────────────────────────────────────────── --}}
    <div class="col-lg-8">
      <div class="card mb-6">
        <div class="card-body">
          <div class="mb-6">
            <label class="form-label" for="title">Título <span class="text-danger">*</span></label>
            <input type="text" id="title" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror"
                   value="{{ old('title', $page?->title) }}" required maxlength="150" placeholder="Título de la página">
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          @if ($page)
            <div class="mb-0">
              <span class="text-muted small">URL: /{{ $page->slug }}</span>
            </div>
          @endif
        </div>
      </div>

      {{-- Contenido: se edita como código, no desde este formulario --}}
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="mb-0">Contenido</h5>
        </div>
        <div class="card-body">
          @if ($page)
            <p class="mb-2">El contenido de esta página se edita directamente en su archivo Blade:</p>
            <code class="d-block p-3 bg-lighter rounded mb-2">resources/views/frontend/paginas/{{ $page->slug }}.blade.php</code>
            <p class="text-body-secondary small mb-0">
              Abre ese archivo en tu editor de código y escribe el HTML/Blade que quieras. El archivo ya incluye
              el navbar y footer reales del sitio (menús gestionados en <a href="{{ route('admin.menus.index') }}">/admin/menus</a>),
              solo debes completar el bloque de contenido.
            </p>
          @else
            <p class="text-body-secondary mb-0">
              Al guardar, se creará automáticamente el archivo Blade de esta página en
              <code>resources/views/frontend/paginas/</code> — lo edites después desde tu editor de código.
            </p>
          @endif
        </div>
      </div>

      {{-- SEO --}}
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">SEO</h5>
        </div>
        <div class="card-body">
          <div class="mb-6">
            <label class="form-label" for="seo_title">Título SEO</label>
            <input type="text" id="seo_title" name="seo_title" class="form-control" maxlength="150"
                   value="{{ old('seo_title', $page?->seo_title) }}" placeholder="Se usa el título de la página si se deja vacío">
          </div>
          <div class="mb-6">
            <label class="form-label" for="seo_description">Descripción SEO</label>
            <textarea id="seo_description" name="seo_description" class="form-control" rows="3" maxlength="500">{{ old('seo_description', $page?->seo_description) }}</textarea>
          </div>
          <x-lfm-input name="seo_og_image" type="image" label="Imagen para compartir (Open Graph)"
                       :value="old('seo_og_image', $page?->seo_og_image)" />
        </div>
      </div>
    </div>

    {{-- ─── Sidebar: publicar ──────────────────────────────────────────── --}}
    <div class="col-lg-4">
      <div class="card mb-6">
        <div class="card-header">
          <h5 class="mb-0">Publicar</h5>
        </div>
        <div class="card-body">
          <div class="mb-6">
            <label class="form-label" for="status">Estado</label>
            <select id="status" name="status" class="form-select select2">
              @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $page?->status?->value) === $status->value)>
                  {{ $status->label() }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-0">
            <label class="form-label" for="parent_id">Página padre</label>
            <select id="parent_id" name="parent_id" class="form-select select2">
              <option value="">— Ninguna (página raíz) —</option>
              @foreach ($parents as $parent)
                <option value="{{ $parent->id }}" @selected((string) old('parent_id', $page?->parent_id) === (string) $parent->id)>
                  {{ $parent->title }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
          <a href="{{ route('admin.pages.index') }}" class="btn btn-label-secondary waves-effect">Cancelar</a>
          <button type="submit" class="btn btn-primary waves-effect waves-light">
            <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar
          </button>
        </div>
      </div>
    </div>
  </div>
</form>
