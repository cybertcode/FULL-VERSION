@php
    $currentTemplate = old('template', $page?->template?->value ?? \App\Enums\PageTemplate::Standard->value);
    $currentContent = old('content', $page?->content ?? []);
@endphp

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

      {{-- Campos dinámicos según plantilla --}}
      @foreach (\App\Enums\PageTemplate::cases() as $template)
        <div class="card mb-6 template-fields" data-template="{{ $template->value }}"
             @if($currentTemplate !== $template->value) style="display:none" @endif>
          <div class="card-header">
            <h5 class="mb-0">Contenido — {{ $template->label() }}</h5>
          </div>
          <div class="card-body">
            @foreach ($template->fields() as $field)
              <div class="mb-6">
                <label class="form-label" for="content_{{ $field['key'] }}_{{ $template->value }}">{{ $field['label'] }}</label>

                @switch($field['type'])
                  @case('richtext')
                    <div class="quill-editor-{{ $template->value }}-{{ $field['key'] }}" style="min-height:200px">
                      {!! $currentContent[$field['key']] ?? '' !!}
                    </div>
                    <input type="hidden"
                           name="content_by_template[{{ $template->value }}][{{ $field['key'] }}]"
                           id="content_{{ $field['key'] }}_{{ $template->value }}"
                           class="quill-hidden-input"
                           data-editor-target="quill-editor-{{ $template->value }}-{{ $field['key'] }}"
                           value="{{ $currentContent[$field['key']] ?? '' }}">
                    @break

                  @case('image')
                    <x-lfm-input
                      :name="'content_by_template['.$template->value.']['.$field['key'].']'"
                      type="image"
                      :value="$currentContent[$field['key']] ?? null" />
                    @break

                  @default
                    <input type="text"
                           name="content_by_template[{{ $template->value }}][{{ $field['key'] }}]"
                           id="content_{{ $field['key'] }}_{{ $template->value }}"
                           class="form-control"
                           value="{{ $currentContent[$field['key']] ?? '' }}">
                @endswitch
              </div>
            @endforeach
          </div>
        </div>
      @endforeach

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

      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Plantilla</h5>
        </div>
        <div class="card-body">
          <select id="template" name="template" class="form-select select2">
            @foreach ($templates as $template)
              <option value="{{ $template->value }}" @selected($currentTemplate === $template->value)>
                {{ $template->label() }}
              </option>
            @endforeach
          </select>
          <div class="form-text">Define qué campos de contenido puedes llenar y cómo se ve en el sitio.</div>
        </div>
      </div>
    </div>
  </div>
</form>
