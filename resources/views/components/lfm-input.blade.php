{{--
  Selector de archivo/imagen desde el Gestor de Archivos (LFM).
  Guarda la URL pública del archivo elegido en un input de texto.

  Uso:
    <x-lfm-input name="foto" type="image" label="Imagen destacada" :value="$post->foto" required />
    <x-lfm-input name="documento" type="file" label="Adjunto PDF" help="Solo documentos del gestor." />
--}}
@props([
    'name',
    'type'        => 'image',   // image | file
    'label'       => null,
    'value'       => null,
    'required'    => false,
    'placeholder' => null,
    'help'        => null,
])

@php
    $fieldId = 'lfm-' . \Illuminate\Support\Str::slug($name, '-');
    $current = old($name, $value);
    $isImage = $type === 'image';
@endphp

<div data-lfm-field>
  @if($label)
    <label class="form-label" for="{{ $fieldId }}">
      {{ $label }}@if($required) <span class="text-danger">*</span>@endif
    </label>
  @endif

  <div class="input-group input-group-merge">
    <span class="input-group-text">
      <i class="icon-base ti {{ $isImage ? 'tabler-photo' : 'tabler-paperclip' }} icon-sm"></i>
    </span>
    <input type="text" id="{{ $fieldId }}" name="{{ $name }}"
      class="form-control @error($name) is-invalid @enderror"
      value="{{ $current }}"
      placeholder="{{ $placeholder ?? ($isImage ? 'Selecciona una imagen…' : 'Selecciona un documento…') }}"
      readonly @if($required) required @endif>
    @can('files.viewAny')
      <button type="button" class="btn btn-primary" data-lfm-trigger
        data-lfm-url="{{ route('unisharp.lfm.show') }}?type={{ $type }}"
        data-lfm-input="{{ $fieldId }}">
        <i class="icon-base ti tabler-folder-open icon-sm me-1"></i> Explorar
      </button>
    @endcan
    <button type="button" class="btn btn-label-secondary {{ $current ? '' : 'd-none' }}"
      data-lfm-clear data-lfm-input="{{ $fieldId }}" title="Quitar" aria-label="Quitar">
      <i class="icon-base ti tabler-x icon-sm"></i>
    </button>
  </div>

  @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
  @if($help)<div class="form-text">{{ $help }}</div>@endif

  @if($isImage)
    <div class="mt-3 {{ $current ? '' : 'd-none' }}" data-lfm-preview-wrap>
      <img src="{{ $current }}" alt="Vista previa" data-lfm-preview
        class="d-block rounded border" style="max-height:120px;max-width:100%;object-fit:contain;">
    </div>
  @endif
</div>

@once
<script>
  // Handler global para todos los selectores LFM de la página (delegación de eventos).
  document.addEventListener('click', function (e) {
    const trigger = e.target.closest('[data-lfm-trigger]');
    if (trigger) {
      e.preventDefault();
      const input = document.getElementById(trigger.dataset.lfmInput);
      const field = trigger.closest('[data-lfm-field]');
      const w = 920, h = 620;
      const left = Math.max(0, (screen.width - w) / 2);
      const top  = Math.max(0, (screen.height - h) / 2);
      window.open(trigger.dataset.lfmUrl, 'FileManager', `width=${w},height=${h},left=${left},top=${top}`);

      // LFM (popup) invoca window.opener.SetUrl(items) al confirmar la selección.
      window.SetUrl = function (items) {
        if (!items || !items.length) return;
        input.value = items.map(i => i.url).join(',');
        input.dispatchEvent(new Event('change', { bubbles: true }));

        const wrap = field.querySelector('[data-lfm-preview-wrap]');
        if (wrap) {
          wrap.querySelector('[data-lfm-preview]').src = items[0].url;
          wrap.classList.remove('d-none');
        }
        const clear = field.querySelector('[data-lfm-clear]');
        if (clear) clear.classList.remove('d-none');
      };
      return;
    }

    const clear = e.target.closest('[data-lfm-clear]');
    if (clear) {
      const input = document.getElementById(clear.dataset.lfmInput);
      const field = clear.closest('[data-lfm-field]');
      input.value = '';
      input.dispatchEvent(new Event('change', { bubbles: true }));
      const wrap = field.querySelector('[data-lfm-preview-wrap]');
      if (wrap) wrap.classList.add('d-none');
      clear.classList.add('d-none');
    }
  });
</script>
@endonce
