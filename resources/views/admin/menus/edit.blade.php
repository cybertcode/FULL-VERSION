@extends('admin/layouts/master')

@section('title', 'Editar menú: '.$menu->name)

@section('admin-vendor-style')
  @vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('admin-vendor-script')
  @vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/sortablejs/sortable.js',
  ])
@endsection

@section('admin-content')

<x-breadcrumb title="Editar menú" :items="[
    ['label' => 'Menús', 'url' => route('admin.menus.index')],
    ['label' => $menu->name],
]" />

<form id="menuForm" action="{{ route('admin.menus.update', $menu) }}" method="POST">
  @csrf @method('PUT')

  <div class="d-flex align-items-end gap-4 mb-4 flex-wrap">
    <div style="max-width:360px" class="flex-grow-1">
      <label class="form-label mb-1" for="menu_name">Nombre del menú</label>
      <input type="text" id="menu_name" name="name" class="form-control" value="{{ $menu->name }}" required maxlength="100">
    </div>
    <button type="submit" class="btn btn-primary">
      <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar menú
    </button>
  </div>

  <div class="row g-4">
    {{-- ─── Columna izquierda: agregar ítems ──────────────────────────── --}}
    <div class="col-lg-3">
      <div class="card mb-4">
        <div class="card-header py-2">
          <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item">
              <button class="nav-link active py-1" data-bs-toggle="tab" data-bs-target="#tab-add-link" type="button">Enlace</button>
            </li>
            <li class="nav-item">
              <button class="nav-link py-1" data-bs-toggle="tab" data-bs-target="#tab-add-pages" type="button">Páginas</button>
            </li>
          </ul>
        </div>
        <div class="card-body py-3">
          <div class="tab-content p-0">

            {{-- Enlace personalizado --}}
            <div class="tab-pane fade show active" id="tab-add-link">
              <div class="mb-2">
                <label class="form-label mb-1 small" for="new_link_url">URL</label>
                <div class="input-group input-group-merge input-group-sm">
                  <span class="input-group-text"><i class="icon-base ti tabler-link"></i></span>
                  <input type="text" id="new_link_url" class="form-control" placeholder="/servicios">
                </div>
              </div>
              <div class="mb-2">
                <label class="form-label mb-1 small" for="new_link_label">Texto</label>
                <input type="text" id="new_link_label" class="form-control form-control-sm" placeholder="Ej. Servicios">
              </div>
              <button type="button" class="btn btn-primary btn-sm w-100" id="btnAddLink">Agregar</button>
            </div>

            {{-- Páginas del CMS (frontend) --}}
            <div class="tab-pane fade" id="tab-add-pages">
              @if ($pages->isEmpty())
                <p class="text-body-secondary small mb-2">
                  No hay páginas publicadas todavía.
                  <a href="{{ route('admin.pages.create') }}" target="_blank">Crear una</a>.
                </p>
              @else
                <div class="mb-2">
                  <input type="text" class="form-control form-control-sm" id="pagesSearchFilter" placeholder="Buscar página...">
                </div>
                <div class="list-group list-group-sm mb-2" style="max-height:220px; overflow-y:auto" id="pagesList">
                  @foreach ($pages as $page)
                    <label class="list-group-item py-1 small">
                      <input class="form-check-input me-2" type="checkbox" value="{{ $page->id }}" data-label="{{ $page->title }}">
                      {{ $page->title }}
                      <span class="text-body-secondary">/{{ $page->slug }}</span>
                    </label>
                  @endforeach
                </div>
              @endif
              <button type="button" class="btn btn-primary btn-sm w-100" id="btnAddPages" @disabled($pages->isEmpty())>Agregar</button>
            </div>

          </div>
        </div>
      </div>

      {{-- Ubicación — estilo WordPress "Menu Settings > Display location" --}}
      <div class="card">
        <h6 class="card-header py-2 mb-0">Ubicación</h6>
        <div class="card-body py-3">
          @foreach ($locations as $location)
            <div class="form-check mb-1">
              <input type="hidden" name="locations[{{ $location->value }}]" value="0">
              <input class="form-check-input" type="checkbox"
                     name="locations[{{ $location->value }}]" value="1"
                     id="loc_{{ $location->value }}"
                     @checked(in_array($location->value, $assignedLocations, true))>
              <label class="form-check-label small" for="loc_{{ $location->value }}">
                {{ $location->label() }}
              </label>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- ─── Columna derecha: estructura del menú ───────────────────────── --}}
    <div class="col-lg-9">
      <div class="card">
        <div class="card-header py-2">
          <h6 class="mb-0">Estructura del menú</h6>
          <small class="text-body-secondary">Arrastra para reordenar o anidar como sub-ítem.</small>
        </div>
        <div class="card-body py-3">
          <ul id="menuStructure" class="menu-structure list-unstyled mb-0"></ul>
          <p class="text-body-secondary mb-0 d-none" id="menuStructureEmpty">
            <i class="icon-base ti tabler-list-details icon-md mb-2 d-block"></i>
            Aún no hay ítems. Agrega el primero desde el panel de la izquierda.
          </p>
        </div>
      </div>
    </div>
  </div>

  <div id="deletedIdsContainer"></div>
</form>

{{-- ─── Template de una fila del menú (clonado por JS) ─────────────────── --}}
<template id="rowTemplate">
  <li class="menu-item-row" data-client-id="">
    <div class="menu-item-row-header">
      <i class="icon-base ti tabler-grip-vertical drag-handle"></i>
      <span class="menu-item-row-title flex-grow-1"></span>
      <span class="badge bg-label-secondary row-inactive-badge d-none">Oculto</span>
      <button type="button" class="btn btn-icon btn-sm btn-text-secondary row-toggle" title="Editar">
        <i class="icon-base ti tabler-chevron-down"></i>
      </button>
      <button type="button" class="btn btn-icon btn-sm btn-text-danger row-remove" title="Eliminar">
        <i class="icon-base ti tabler-trash"></i>
      </button>
    </div>
    <div class="menu-item-row-body d-none">
      <div class="row g-2">
        <div class="col-md-5">
          <label class="form-label mb-1 small">Etiqueta</label>
          <input type="text" class="form-control form-control-sm f-label" maxlength="150" required>
        </div>
        <div class="col-md-3">
          <label class="form-label mb-1 small d-block">Destino</label>
          <div class="btn-group btn-group-sm w-100 dest-toggle" role="group">
            <button type="button" class="btn btn-outline-primary btn-sm dest-btn active" data-dest="url">Enlace</button>
            <button type="button" class="btn btn-outline-primary btn-sm dest-btn" data-dest="page">Página</button>
          </div>
        </div>
        <div class="col-md-4 field-url">
          <label class="form-label mb-1 small">URL</label>
          <input type="text" class="form-control form-control-sm f-url" placeholder="/servicios">
        </div>
        <div class="col-md-4 field-page d-none">
          <label class="form-label mb-1 small">Página</label>
          <select class="form-select form-select-sm f-page select2-inline">
            <option value="">Selecciona...</option>
            @foreach ($pages as $page)
              <option value="{{ $page->id }}">{{ $page->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label mb-1 small">Abrir en</label>
          <select class="form-select form-select-sm f-target select2-inline">
            <option value="_self">Misma pestaña</option>
            <option value="_blank">Pestaña nueva</option>
          </select>
        </div>
        <div class="col-auto">
          <label class="form-label mb-1 small d-block">Ícono</label>
          <div class="dropdown icon-picker">
            <button type="button" class="btn btn-outline-secondary btn-sm icon-picker-toggle dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside">
              <i class="icon-base ti tabler-ban icon-picker-preview"></i>
            </button>
            <div class="dropdown-menu p-2 icon-picker-menu" style="width:220px">
              <div class="d-flex flex-wrap gap-1 icon-picker-grid"></div>
            </div>
          </div>
          <input type="hidden" class="f-icon" value="">
        </div>
        <div class="col-auto d-flex align-items-end">
          <div class="form-check form-switch mb-1">
            <input class="form-check-input f-active" type="checkbox" checked>
            <label class="form-check-label small">Visible</label>
          </div>
        </div>
        <div class="col-auto d-flex align-items-end ms-auto">
          <button type="button" class="btn btn-sm btn-label-secondary row-collapse">Cerrar</button>
        </div>
      </div>
    </div>
    <ul class="menu-structure-children list-unstyled"></ul>
  </li>
</template>

@endsection

@section('admin-page-script')
<style>
  .menu-structure, .menu-structure-children { padding-left: 0; }
  .menu-structure-children { padding-left: 1.5rem; margin-top: .375rem; }
  .menu-item-row {
    background: var(--bs-paper-bg, #fff);
    border: 1px solid var(--bs-border-color);
    border-radius: .5rem;
    margin-bottom: .375rem;
  }
  .menu-item-row-header {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .375rem .625rem;
  }
  .menu-item-row-body { padding: .25rem .625rem .625rem .625rem; }
  .drag-handle { cursor: grab; color: var(--bs-secondary-color); }
  .menu-item-row.sortable-ghost { opacity: .4; }
  .row-toggle.expanded i { transform: rotate(180deg); }
  .icon-picker-toggle { width: 2.4rem; }
  .icon-picker-btn.active { background-color: var(--bs-primary); color: #fff; border-color: var(--bs-primary); }
</style>
<script>
'use strict';

let clientIdCounter = 0;
const deletedIds = [];

const COMMON_ICONS = [
  'home', 'info-circle', 'briefcase', 'mail', 'phone', 'map-pin',
  'shopping-cart', 'users', 'file-text', 'photo', 'settings', 'star',
  'heart', 'bell', 'calendar', 'help-circle', 'book', 'building',
  'device-laptop', 'news',
];

function nextClientId() {
  return 'new-' + (++clientIdCounter);
}

function initSelect2On(el) {
  if (window.jQuery) { $(el).select2({ width: '100%', dropdownParent: $(el).closest('.menu-item-row-body') }); }
}

function setRowIcon(li, icon) {
  li.querySelector('.f-icon').value = icon || '';
  const preview = li.querySelector('.icon-picker-preview');
  preview.className = 'icon-base ti icon-picker-preview ' + (icon ? 'tabler-' + icon : 'tabler-ban');
  li.querySelectorAll('.icon-picker-grid .icon-picker-btn').forEach(b => {
    b.classList.toggle('active', (b.dataset.icon || '') === (icon || ''));
  });
}

function buildIconGrid(li) {
  const grid = li.querySelector('.icon-picker-grid');
  const makeBtn = (icon, title) => {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'btn btn-icon btn-outline-secondary btn-sm icon-picker-btn';
    btn.title = title || icon;
    btn.dataset.icon = icon || '';
    btn.innerHTML = `<i class="icon-base ti tabler-${icon || 'ban'}"></i>`;
    btn.addEventListener('click', () => setRowIcon(li, icon));
    return btn;
  };
  grid.appendChild(makeBtn(null, 'Sin ícono'));
  COMMON_ICONS.forEach(icon => grid.appendChild(makeBtn(icon)));
}

function setRowDestType(li, type) {
  const isPage = type === 'page';
  li.dataset.type = isPage ? 'page' : 'url';
  li.querySelector('.field-url').classList.toggle('d-none', isPage);
  li.querySelector('.field-page').classList.toggle('d-none', ! isPage);
  li.querySelectorAll('.dest-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.dest === li.dataset.type);
  });
}

function createRow({ clientId = null, id = null, label = '', type = 'url', url = '', pageId = '', icon = '', target = '_self', isActive = true } = {}) {
  const template = document.getElementById('rowTemplate');
  const node = template.content.cloneNode(true);
  const li = node.querySelector('.menu-item-row');

  const cid = clientId || nextClientId();
  li.dataset.clientId = cid;
  if (id) { li.dataset.id = id; }

  li.querySelector('.menu-item-row-title').textContent = label;
  li.querySelector('.f-label').value = label;
  li.querySelector('.f-url').value = url;
  li.querySelector('.f-target').value = target;
  li.querySelector('.f-active').checked = isActive;
  li.querySelector('.row-inactive-badge').classList.toggle('d-none', isActive);

  buildIconGrid(li);
  setRowIcon(li, icon);

  setRowDestType(li, type);
  if (type === 'page') { li.querySelector('.f-page').value = pageId; }

  li.querySelectorAll('.dest-btn').forEach(btn => {
    btn.addEventListener('click', () => setRowDestType(li, btn.dataset.dest));
  });

  const labelInput = li.querySelector('.f-label');
  labelInput.addEventListener('input', () => {
    li.querySelector('.menu-item-row-title').textContent = labelInput.value || '(sin etiqueta)';
  });

  const activeInput = li.querySelector('.f-active');
  activeInput.addEventListener('change', () => {
    li.querySelector('.row-inactive-badge').classList.toggle('d-none', activeInput.checked);
  });

  li.querySelector('.row-toggle').addEventListener('click', function () {
    const body = li.querySelector('.menu-item-row-body');
    body.classList.toggle('d-none');
    this.classList.toggle('expanded');
    if (! body.classList.contains('d-none')) {
      initSelect2On(li.querySelector('.f-target'));
      initSelect2On(li.querySelector('.f-page'));
    }
  });

  li.querySelector('.row-collapse').addEventListener('click', function () {
    li.querySelector('.menu-item-row-body').classList.add('d-none');
    li.querySelector('.row-toggle').classList.remove('expanded');
  });

  li.querySelector('.row-remove').addEventListener('click', function () {
    confirmAction({
      title: '¿Eliminar ítem?',
      text: `"${label}" y sus sub-ítems serán eliminados al guardar.`,
      isDanger: true,
      confirmText: 'Sí, eliminar',
      onConfirm: () => {
        if (li.dataset.id) { deletedIds.push(parseInt(li.dataset.id, 10)); }
        li.remove();
        toggleEmptyState();
      },
    });
  });

  return li;
}

function appendRowToStructure(li, parentUl = null) {
  const container = parentUl || document.getElementById('menuStructure');
  container.appendChild(li);
  toggleEmptyState();
}

function toggleEmptyState() {
  const structure = document.getElementById('menuStructure');
  const isEmpty = structure.children.length === 0;
  document.getElementById('menuStructureEmpty').classList.toggle('d-none', ! isEmpty);
}

document.getElementById('btnAddLink').addEventListener('click', function () {
  const url = document.getElementById('new_link_url').value.trim();
  const label = document.getElementById('new_link_label').value.trim() || url;

  if (! url) {
    showToast('warning', 'Escribe una URL para el enlace.');
    return;
  }

  const li = createRow({ label, type: 'url', url });
  appendRowToStructure(li);

  document.getElementById('new_link_url').value = '';
  document.getElementById('new_link_label').value = '';
});

const btnAddPages = document.getElementById('btnAddPages');
if (btnAddPages) {
  btnAddPages.addEventListener('click', function () {
    const checked = document.querySelectorAll('#pagesList input:checked');

    if (checked.length === 0) {
      showToast('warning', 'Selecciona al menos una página.');
      return;
    }

    checked.forEach(cb => {
      const li = createRow({ label: cb.dataset.label, type: 'page', pageId: cb.value });
      appendRowToStructure(li);
      cb.checked = false;
    });
  });
}

const pagesSearchFilter = document.getElementById('pagesSearchFilter');
if (pagesSearchFilter) {
  pagesSearchFilter.addEventListener('input', function () {
    const term = this.value.trim().toLowerCase();
    document.querySelectorAll('#pagesList .list-group-item').forEach(item => {
      item.classList.toggle('d-none', ! item.textContent.toLowerCase().includes(term));
    });
  });
}

function initSortable(list) {
  new Sortable(list, {
    group: 'menu-structure',
    handle: '.drag-handle',
    animation: 150,
    fallbackOnBody: true,
    swapThreshold: 0.65,
  });
}

function buildPayloadFromDom() {
  const items = [];

  function walk(ul, parentClientId, orderStart) {
    Array.from(ul.children).forEach((li, index) => {
      if (! li.classList.contains('menu-item-row')) { return; }

      const type = li.dataset.type === 'page' ? 'page' : 'url';

      items.push({
        client_id: li.dataset.clientId,
        id: li.dataset.id || null,
        parent_client_id: parentClientId,
        label: li.querySelector('.f-label').value,
        type,
        url: type === 'url' ? (li.querySelector('.f-url').value || null) : null,
        page_id: type === 'page' ? (li.querySelector('.f-page').value || null) : null,
        icon: li.querySelector('.f-icon').value || null,
        target: li.querySelector('.f-target').value,
        is_active: li.querySelector('.f-active').checked,
        order: orderStart + index,
      });

      const childUl = li.querySelector(':scope > .menu-structure-children');
      if (childUl) { walk(childUl, li.dataset.clientId, 0); }
    });
  }

  walk(document.getElementById('menuStructure'), null, 0);

  return items;
}

document.getElementById('menuForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const items = buildPayloadFromDom();
  const invalid = items.find(i => ! i.label || (i.type === 'url' && ! i.url) || (i.type === 'page' && ! i.page_id));

  if (invalid) {
    showToast('error', 'Completa la etiqueta y el destino de todos los ítems antes de guardar.');
    return;
  }

  const container = document.getElementById('deletedIdsContainer');
  container.innerHTML = '';
  deletedIds.forEach(id => {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'deleted_ids[]';
    input.value = id;
    container.appendChild(input);
  });

  items.forEach((item, i) => {
    Object.entries(item).forEach(([key, value]) => {
      if (value === null || value === undefined) { return; }
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = `items[${i}][${key}]`;
      input.value = value === true ? '1' : (value === false ? '0' : value);
      container.appendChild(input);
    });
  });

  this.submit();
});

const initialTree = @json($tree->toArray());

function renderNode(nodeData, parentUl = null) {
  const li = createRow({
    id: nodeData.id,
    label: nodeData.label,
    type: nodeData.type,
    url: nodeData.url || '',
    pageId: nodeData.page_id || '',
    icon: nodeData.icon || '',
    target: nodeData.target,
    isActive: !! nodeData.is_active,
  });

  appendRowToStructure(li, parentUl);

  const childUl = li.querySelector(':scope > .menu-structure-children');
  initSortable(childUl);

  (nodeData.children || []).forEach(child => renderNode(child, childUl));
}

window.addEventListener('load', function () {
  initSortable(document.getElementById('menuStructure'));
  initialTree.forEach(node => renderNode(node));
});
</script>
@endsection
