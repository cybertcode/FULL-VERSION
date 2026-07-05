@extends('admin/layouts/master')

@section('title', 'Editar menú: '.$menu->name)

@section('admin-vendor-style')
  @vite(['resources/assets/vendor/libs/jstree/jstree.scss'])
@endsection

@section('admin-vendor-script')
  @vite(['resources/assets/vendor/libs/jstree/jstree.js'])
@endsection

@section('admin-content')

<x-breadcrumb title="Editar menú" :items="[
    ['label' => 'Menús', 'url' => route('admin.menus.index')],
    ['label' => $menu->name],
]" />

<div class="row g-6">
  {{-- ─── Datos del menú ─────────────────────────────────────────────── --}}
  <div class="col-lg-4">
    <div class="card mb-6">
      <h5 class="card-header">Datos del menú</h5>
      <div class="card-body">
        <form action="{{ route('admin.menus.update', $menu) }}" method="POST">
          @csrf @method('PUT')
          <div class="mb-4">
            <label class="form-label" for="menu_name">Nombre</label>
            <input type="text" id="menu_name" name="name" class="form-control" value="{{ $menu->name }}" required maxlength="100">
          </div>
          <div class="mb-4">
            <label class="form-label" for="menu_slug">Identificador (slug)</label>
            <input type="text" id="menu_slug" name="slug" class="form-control" value="{{ $menu->slug }}" required maxlength="100">
          </div>
          <div class="mb-4">
            <label class="form-label" for="menu_location">Ubicación</label>
            <input type="text" id="menu_location" name="location" class="form-control" value="{{ $menu->location }}" maxlength="50" placeholder="header, footer, sidebar...">
          </div>
          <button type="submit" class="btn btn-primary">Guardar datos</button>
        </form>
      </div>
    </div>

    <div class="alert alert-primary d-flex align-items-start gap-2" role="alert">
      <i class="icon-base ti tabler-info-circle icon-md mt-1"></i>
      <div>Arrastra los ítems del árbol para reordenarlos o anidarlos como sub-ítems. Los cambios se guardan automáticamente.</div>
    </div>
  </div>

  {{-- ─── Árbol de ítems (jsTree) ────────────────────────────────────── --}}
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Ítems del menú</h5>
        @can('menus.edit')
          <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#itemModal" onclick="openCreateItem()">
            <i class="icon-base ti tabler-plus me-1"></i> Nuevo ítem
          </button>
        @endcan
      </div>
      <div class="card-body">
        <div id="menuJsTree"></div>
        <div id="menuTreeEmpty" class="text-body-secondary d-none">
          <i class="icon-base ti tabler-list-details icon-md mb-2 d-block"></i>
          Aún no hay ítems. Crea el primero con el botón "Nuevo ítem".
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ─── Modal crear/editar ítem ────────────────────────────────────────── --}}
<div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="itemForm">
        <div class="modal-header">
          <h5 class="modal-title" id="itemModalTitle">Nuevo ítem</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="item_id" value="">
          <div class="mb-4">
            <label class="form-label" for="item_label">Etiqueta</label>
            <input type="text" id="item_label" class="form-control" required maxlength="150">
          </div>
          <div class="mb-4">
            <label class="form-label" for="item_type">Tipo de destino</label>
            <select id="item_type" class="form-select select2" onchange="toggleItemTypeFields()">
              <option value="url">URL externa / ruta manual</option>
              <option value="route">Ruta con nombre (Laravel route)</option>
              <option value="page">Página interna</option>
            </select>
          </div>
          <div class="mb-4" id="field_url">
            <label class="form-label" for="item_url">URL</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="icon-base ti tabler-link"></i></span>
              <input type="text" id="item_url" class="form-control" placeholder="https://... o /ruta">
            </div>
          </div>
          <div class="mb-4 d-none" id="field_route">
            <label class="form-label" for="item_route_name">Nombre de ruta</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="icon-base ti tabler-route"></i></span>
              <input type="text" id="item_route_name" class="form-control" placeholder="Ej. admin.dashboard">
            </div>
          </div>
          <div class="mb-4 d-none" id="field_page">
            <label class="form-label" for="item_page_id">ID de página</label>
            <input type="number" id="item_page_id" class="form-control" placeholder="Disponible cuando exista el módulo de Páginas">
          </div>
          <div class="row">
            <div class="col-6 mb-4">
              <label class="form-label" for="item_icon">Ícono (Tabler)</label>
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="icon-base ti tabler-icons"></i></span>
                <input type="text" id="item_icon" class="form-control" placeholder="home">
              </div>
            </div>
            <div class="col-6 mb-4">
              <label class="form-label" for="item_target">Destino</label>
              <select id="item_target" class="form-select select2">
                <option value="_self">Misma pestaña</option>
                <option value="_blank">Pestaña nueva</option>
              </select>
            </div>
          </div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="item_is_active" checked>
            <label class="form-check-label" for="item_is_active">Visible en el frontend</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary waves-effect waves-light">Guardar ítem</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('admin-page-script')
<script>
'use strict';

const canEditMenu = @json(auth()->user()->can('menus.edit'));
const menuTreeUrl = '{{ route('admin.menus.tree', $menu) }}';
const menuItemsBaseUrl = '{{ url('admin/menus/'.$menu->id.'/items') }}';
const menuMoveUrl = '{{ route('admin.menus.move', $menu) }}';
let currentParentId = null;

function toggleItemTypeFields() {
  const type = document.getElementById('item_type').value;
  document.getElementById('field_url').classList.toggle('d-none', type !== 'url');
  document.getElementById('field_route').classList.toggle('d-none', type !== 'route');
  document.getElementById('field_page').classList.toggle('d-none', type !== 'page');
}

function openCreateItem(parentId = null) {
  document.getElementById('itemForm').reset();
  document.getElementById('item_id').value = '';
  currentParentId = parentId;
  document.getElementById('itemModalTitle').textContent = 'Nuevo ítem';
  toggleItemTypeFields();
}

function openEditItem(node) {
  const a = node.li_attr;
  currentParentId = null;
  document.getElementById('item_id').value = node.id;
  document.getElementById('item_label').value = a['data-label'] || '';
  document.getElementById('item_type').value = a['data-type'] || 'url';
  document.getElementById('item_url').value = a['data-url'] || '';
  document.getElementById('item_route_name').value = a['data-route-name'] || '';
  document.getElementById('item_page_id').value = a['data-page-id'] || '';
  document.getElementById('item_icon').value = a['data-icon'] || '';
  document.getElementById('item_target').value = a['data-target'] || '_self';
  document.getElementById('item_is_active').checked = a['data-is-active'] === '1';
  document.getElementById('itemModalTitle').textContent = 'Editar ítem';
  toggleItemTypeFields();
  new bootstrap.Modal(document.getElementById('itemModal')).show();
}

function deleteMenuItem(id, label) {
  confirmAction({
    title: '¿Eliminar ítem?',
    text: `"${label}" y sus sub-ítems serán eliminados.`,
    isDanger: true,
    confirmText: 'Sí, eliminar',
    onConfirm: () => {
      fetch(`${menuItemsBaseUrl}/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
      })
        .then(r => r.json())
        .then(data => {
          showToast('success', data.message);
          $('#menuJsTree').jstree(true).delete_node(String(id));
        })
        .catch(() => showToast('error', 'Error de conexión.'));
    },
  });
}

$(function () {
  const theme = $('html').attr('data-bs-theme') === 'dark' ? 'default-dark' : 'default';
  const tree = $('#menuJsTree');

  tree.jstree({
    core: {
      themes: { name: theme, dots: true, icons: true },
      check_callback: true,
      data: {
        url: menuTreeUrl,
        dataType: 'json',
      },
    },
    plugins: canEditMenu ? ['dnd', 'contextmenu', 'wholerow'] : ['wholerow'],
    contextmenu: {
      items: function (node) {
        return {
          addChild: {
            label: 'Agregar sub-ítem',
            icon: 'icon-base ti tabler-corner-down-right',
            action: function () {
              openCreateItem(node.id);
              new bootstrap.Modal(document.getElementById('itemModal')).show();
            },
          },
          edit: {
            label: 'Editar',
            icon: 'icon-base ti tabler-pencil',
            action: function () {
              openEditItem(node);
            },
          },
          remove: {
            label: 'Eliminar',
            icon: 'icon-base ti tabler-trash',
            action: function () {
              deleteMenuItem(node.id, node.li_attr['data-label']);
            },
          },
        };
      },
    },
  });

  tree.on('loaded.jstree', function (e, data) {
    const isEmpty = data.instance.get_json().length === 0;
    document.getElementById('menuTreeEmpty').classList.toggle('d-none', ! isEmpty);
    tree.toggleClass('d-none', isEmpty);
  });

  tree.on('move_node.jstree', function (e, data) {
    fetch(menuMoveUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        id: parseInt(data.node.id, 10),
        parent: data.parent === '#' ? null : parseInt(data.parent, 10),
        position: data.position,
      }),
    })
      .then(r => r.json())
      .then(res => showToast('success', res.message))
      .catch(() => showToast('error', 'No se pudo guardar el orden.'));
  });
});

document.getElementById('itemForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const id = document.getElementById('item_id').value;
  const payload = {
    label: document.getElementById('item_label').value,
    type: document.getElementById('item_type').value,
    url: document.getElementById('item_url').value || null,
    route_name: document.getElementById('item_route_name').value || null,
    page_id: document.getElementById('item_page_id').value || null,
    icon: document.getElementById('item_icon').value || null,
    target: document.getElementById('item_target').value,
    is_active: document.getElementById('item_is_active').checked,
    parent_id: id ? null : currentParentId,
  };

  const url = id ? `${menuItemsBaseUrl}/${id}` : menuItemsBaseUrl;
  const method = id ? 'PUT' : 'POST';

  fetch(url, {
    method,
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
    },
    body: JSON.stringify(payload),
  })
    .then(r => r.json().then(data => ({ ok: r.ok, data })))
    .then(({ ok, data }) => {
      if (!ok) {
        showToast('error', data.message || 'No se pudo guardar el ítem.');
        return;
      }
      showToast('success', data.message);
      bootstrap.Modal.getInstance(document.getElementById('itemModal'))?.hide();
      $('#menuJsTree').jstree(true).refresh();
    })
    .catch(() => showToast('error', 'Error de conexión.'));
});
</script>
@endsection
