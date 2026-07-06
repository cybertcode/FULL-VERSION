@extends('admin/layouts/master')

@section('title', 'Editar menú: ' . $menu->name)

@section('admin-vendor-style')
    @vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('admin-vendor-script')
    @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/sortablejs/sortable.js'])
@endsection

@section('admin-content')

    <x-breadcrumb title="Editar menú" :items="[['label' => 'Menús', 'url' => route('admin.menus.index')], ['label' => $menu->name]]" />

    <form id="menuForm" action="{{ route('admin.menus.update', $menu) }}" method="POST">
        @csrf @method('PUT')

        <div class="d-flex align-items-end gap-3 mb-3 flex-wrap">
            <div style="max-width:320px" class="flex-grow-1">
                <label class="form-label mb-1 small" for="menu_name">Nombre del menú</label>
                <input type="text" id="menu_name" name="name" class="form-control form-control-sm"
                    value="{{ $menu->name }}" required maxlength="100">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="icon-base ti tabler-device-floppy me-1"></i> Guardar menú
            </button>
        </div>

        <div class="row g-3">
            {{-- ─── Columna izquierda: agregar ítems ──────────────────────────── --}}
            <div class="col-lg-3">
                <div class="card mb-3">
                    <div class="card-header py-1" id="menus-edit-card-header">
                        <ul class="nav nav-tabs card-header-tabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-add-link"
                                    type="button">Enlace</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-add-pages"
                                    type="button">Páginas</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-0">

                            {{-- Enlace personalizado --}}
                            <div class="tab-pane fade show active" id="tab-add-link">
                                <div class="form-check menu-panel-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="new_link_no_target">
                                    <label class="form-check-label" for="new_link_no_target"
                                        title="El ítem no tendrá destino propio, solo sirve para agrupar sub-ítems">
                                        Solo agrupar sub-ítems
                                    </label>
                                </div>
                                <div class="mb-2" id="new_link_url_wrapper">
                                    <label class="form-label mb-1" for="new_link_url">URL</label>
                                    <div class="input-group input-group-merge input-group-sm">
                                        <span class="input-group-text"><i class="icon-base ti tabler-link"></i></span>
                                        <input type="text" id="new_link_url" class="form-control"
                                            placeholder="/servicios">
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label mb-1" for="new_link_label">Texto</label>
                                    <input type="text" id="new_link_label" class="form-control form-control-sm"
                                        placeholder="Ej. Servicios">
                                </div>
                                <div class="mb-2" id="new_link_icon_wrapper">
                                    <label class="form-label mb-1 d-block">Ícono</label>
                                    <div class="dropdown icon-picker">
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm icon-picker-toggle dropdown-toggle"
                                            data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                            <i class="icon-base ti tabler-ban icon-picker-preview"></i>
                                        </button>
                                        <div class="dropdown-menu p-2 icon-picker-menu" style="width:220px">
                                            <div class="d-flex flex-wrap gap-1 icon-picker-grid"></div>
                                        </div>
                                    </div>
                                    <input type="hidden" class="f-icon" value="">
                                </div>
                                <button type="button" class="btn btn-primary btn-sm w-100" id="btnAddLink">Agregar</button>
                            </div>

                            {{-- Páginas del CMS (frontend) --}}
                            <div class="tab-pane fade" id="tab-add-pages">
                                @if ($pages->isEmpty())
                                    <p class="text-body-secondary small mb-2 mt-2">
                                        No hay páginas publicadas todavía.
                                        <a href="{{ route('admin.pages.create') }}" target="_blank">Crear una</a>.
                                    </p>
                                @else
                                    <div class="mb-2 mt-2">
                                        <input type="text" class="form-control form-control-sm" id="pagesSearchFilter"
                                            placeholder="Buscar página...">
                                    </div>
                                    <div class="list-group list-group-sm mb-2" style="max-height:200px; overflow-y:auto"
                                        id="pagesList">
                                        @foreach ($pages as $page)
                                            <label class="list-group-item py-1 small">
                                                <input class="form-check-input me-2" type="checkbox"
                                                    value="{{ $page->id }}" data-label="{{ $page->title }}"
                                                    data-slug="{{ $page->slug }}">
                                                {{ $page->title }}
                                                <span class="text-body-secondary">/{{ $page->slug }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="mb-2" id="new_pages_icon_wrapper">
                                        <label class="form-label mb-1 d-block">Ícono (para todas las seleccionadas)</label>
                                        <div class="dropdown icon-picker">
                                            <button type="button"
                                                class="btn btn-outline-secondary btn-sm icon-picker-toggle dropdown-toggle"
                                                data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                                <i class="icon-base ti tabler-ban icon-picker-preview"></i>
                                            </button>
                                            <div class="dropdown-menu p-2 icon-picker-menu" style="width:220px">
                                                <div class="d-flex flex-wrap gap-1 icon-picker-grid"></div>
                                            </div>
                                        </div>
                                        <input type="hidden" class="f-icon" value="">
                                    </div>
                                @endif
                                <button type="button" class="btn btn-primary btn-sm w-100" id="btnAddPages"
                                    @disabled($pages->isEmpty())>Agregar</button>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Ubicación — estilo WordPress "Menu Settings > Display location" --}}
                <div class="card" id="locationsCard">
                    <h6 class="card-header py-2 mb-0">Ubicación</h6>
                    <div class="card-body py-2">
                        @foreach ($locations as $location)
                            <div class="form-check mb-1">
                                <input type="hidden" name="locations[{{ $location->value }}]" value="0">
                                <input class="form-check-input" type="checkbox" name="locations[{{ $location->value }}]"
                                    value="1" id="loc_{{ $location->value }}" @checked(in_array($location->value, $assignedLocations, true))>
                                <label class="form-check-label" for="loc_{{ $location->value }}">
                                    {{ $location->label() }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ─── Columna derecha: estructura del menú ───────────────────────── --}}
            <div class="col-lg-9">
                <div class="card" id="menuStructureCard">
                    <div class="card-header py-2">
                        <h6 class="mb-0">Estructura del menú</h6>
                        <small class="text-body-secondary">Arrastra para reordenar o anidar como sub-ítem.</small>
                    </div>
                    <div class="card-body py-2">
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
                <i class="icon-base ti menu-item-type-icon" title=""></i>
                <span class="menu-item-row-title text-truncate" style="max-width:14rem"></span>
                <span class="text-body-secondary menu-item-row-url text-truncate flex-grow-1"></span>
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
                        <input type="text" class="form-control form-control-sm f-label" maxlength="150">
                        <div class="invalid-feedback">La etiqueta es obligatoria.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1 small d-block">Destino</label>
                        <div class="btn-group btn-group-sm w-100 dest-toggle" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm dest-btn active"
                                data-dest="url">Enlace</button>
                            <button type="button" class="btn btn-outline-primary btn-sm dest-btn"
                                data-dest="page">Página</button>
                            <button type="button" class="btn btn-outline-primary btn-sm dest-btn" data-dest="none"
                                title="Sin destino — solo agrupa sub-ítems">Sin destino</button>
                        </div>
                    </div>
                    <div class="col-md-4 field-url">
                        <label class="form-label mb-1 small">URL</label>
                        <input type="text" class="form-control form-control-sm f-url" placeholder="/servicios">
                        <div class="invalid-feedback">La URL es obligatoria.</div>
                    </div>
                    <div class="col-md-4 field-page d-none">
                        <label class="form-label mb-1 small">Página</label>
                        <select class="form-select form-select-sm f-page select2-inline">
                            <option value="">Selecciona...</option>
                            @foreach ($pages as $page)
                                <option value="{{ $page->id }}" data-slug="{{ $page->slug }}">{{ $page->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 field-none d-none">
                        <label class="form-label mb-1 small d-block">&nbsp;</label>
                        <p class="text-body-secondary small mb-0">Este ítem solo agrupa a sus sub-ítems, no lleva a ninguna
                            parte.</p>
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
                            <button type="button"
                                class="btn btn-outline-secondary btn-sm icon-picker-toggle dropdown-toggle"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside">
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
        #menuStructure,
        .menu-structure-children {
            padding-left: 0;
        }

        .menu-structure-children {
            padding-left: 1.125rem;
            margin-top: .25rem;
        }

        .menu-item-row {
            background: var(--bs-paper-bg, #fff);
            border: 1px solid var(--bs-border-color);
            border-radius: .375rem;
            margin-bottom: .25rem;
        }

        .menu-item-row-header {
            display: flex;
            align-items: center;
            gap: .375rem;
            padding: .125rem .375rem;
            min-height: 1.75rem;
            font-size: .8125rem;
        }

        .menu-item-row-header .drag-handle {
            font-size: .9375rem;
        }

        .menu-item-row-header .btn.btn-icon {
            width: 1.5rem;
            height: 1.5rem;
            padding: 0;
            font-size: .75rem;
        }

        .menu-item-row-title {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .menu-item-row-url {
            font-size: .6875rem;
            margin-left: .25rem;
        }

        .menu-item-type-icon {
            font-size: .875rem;
            color: var(--bs-secondary-color);
            flex-shrink: 0;
        }

        .menu-item-row-body {
            padding: .25rem .5rem .5rem .5rem;
            font-size: .8125rem;
        }

        .menu-item-row-body .form-label {
            font-size: .6875rem;
        }

        .drag-handle {
            cursor: grab;
            color: var(--bs-secondary-color);
        }

        .menu-item-row.sortable-ghost {
            opacity: .4;
        }

        .row-toggle.expanded i {
            transform: rotate(180deg);
        }

        .icon-picker-toggle {
            width: 2.4rem;
        }

        .icon-picker-grid {
            max-height: 220px;
            overflow-y: auto;
        }

        .icon-picker-btn.active {
            background-color: var(--bs-primary);
            color: #fff;
            border-color: var(--bs-primary);
        }

        /* ─── Compactación general de la pantalla ──────────────────────── */
        #menus-edit-card-header {
            padding-top: .5rem;
            padding-bottom: .5rem;
        }

        #menus-edit-card-header .nav-link {
            font-size: .75rem;
            padding-top: .25rem;
            padding-bottom: .25rem;
        }

        /* Todo el contenido de los tabs "Enlace"/"Páginas" a un tamaño uniforme,
               separado del todos los elementos internos con margin explícito (no
               depender solo del padding del card-body, que queda visualmente
               insuficiente junto al border-bottom de las pestañas). */
        #tab-add-link,
        #tab-add-pages {
            font-size: .75rem;
        }

        #tab-add-link .form-label,
        #tab-add-pages .form-label {
            font-size: .6875rem;
        }

        /* Vuexy pone padding-block-start:0 en .card-body cuando sigue a un
               .card-header (asume que el header ya separa visualmente) — pero con
               nav-tabs dentro del header (que trae su propio border-bottom), el
               contenido queda pegado a esa línea. Se restaura el padding solo aquí. */
        #menus-edit-card-header+.card-body {
            padding-block-start: 1rem !important;
        }

        .menu-panel-check {
            margin-block: 0 .75rem;
        }

        .menu-panel-check .form-check-label {
            font-size: .75rem;
            line-height: 1.3;
        }

        #pagesList .list-group-item {
            font-size: .6875rem;
            padding-top: .25rem;
            padding-bottom: .25rem;
        }

        #locationsCard .form-check-label {
            font-size: .75rem;
        }

        #menuStructureCard .card-header {
            padding-top: .5rem;
            padding-bottom: .5rem;
        }

        #menuStructureCard .card-header h6 {
            font-size: .9375rem;
        }

        #menuStructureCard .card-header small {
            font-size: .6875rem;
        }

        /* ─── Responsive ──────────────────────────────────────────────── */
        @media (max-width: 991.98px) {
            .menu-item-row-title {
                max-width: 9rem !important;
            }

            .menu-item-row-url {
                display: none;
            }
        }

        @media (max-width: 575.98px) {
            .menu-item-row-header {
                flex-wrap: wrap;
                min-height: auto;
                padding-block: .375rem;
            }

            .menu-item-row-title {
                max-width: 100% !important;
                white-space: normal;
                flex-basis: 100%;
                order: 1;
            }

            .menu-item-row-url {
                display: none;
            }

            .menu-item-row-header .btn.btn-icon {
                margin-left: auto;
            }

            .menu-item-row-body .row>div {
                margin-bottom: .5rem;
            }
        }
    </style>
    <script>
        'use strict';

        let clientIdCounter = 0;
        let formDirty = false;
        const deletedIds = [];

        function markDirty() {
            formDirty = true;
        }

        window.addEventListener('beforeunload', function(e) {
            if (!formDirty) {
                return;
            }
            e.preventDefault();
            e.returnValue = '';
        });

        document.getElementById('menu_name').addEventListener('input', markDirty);
        document.querySelectorAll('input[name^="locations["]').forEach(el => el.addEventListener('change', markDirty));

        const COMMON_ICONS = [
            // Navegación / general
            'home', 'info-circle', 'star', 'heart', 'bell', 'help-circle',
            'external-link', 'link', 'grid-dots', 'category', 'apps',
            // Institucional / empresa
            'briefcase', 'building', 'building-store', 'building-bank',
            'users', 'users-group', 'user-circle', 'school',
            // Contacto / ubicación
            'mail', 'phone', 'map-pin', 'map-2', 'world', 'globe',
            // Contenido
            'file-text', 'file-download', 'folder', 'folder-open', 'photo',
            'book', 'news', 'clipboard-list', 'list-check', 'checklist',
            // Comercio
            'shopping-cart', 'credit-card', 'wallet', 'gift', 'ticket',
            'truck', 'package', 'box-seam',
            // Multimedia / redes
            'video', 'camera', 'music', 'headphones', 'download', 'upload',
            'share', 'brand-facebook', 'brand-instagram', 'brand-twitter',
            'brand-linkedin', 'brand-youtube', 'brand-whatsapp', 'brand-tiktok',
            // Servicios / logros
            'certificate', 'award', 'trophy', 'target', 'rocket', 'bulb',
            'bolt', 'shield-check', 'lock-check', 'stethoscope', 'tools',
            'scale', 'gavel',
            // Datos / sistema
            'settings', 'calendar', 'chart-bar', 'chart-pie', 'report',
            'database', 'server', 'cloud', 'device-laptop',
        ];

        function nextClientId() {
            return 'new-' + (++clientIdCounter);
        }

        function initSelect2On(el) {
            if (window.jQuery) {
                $(el).select2({
                    width: '100%',
                    dropdownParent: $(el).closest('.menu-item-row-body')
                });
            }
        }

        function setRowIcon(container, icon) {
            container.querySelector('.f-icon').value = icon || '';
            const preview = container.querySelector('.icon-picker-preview');
            preview.className = 'icon-base ti icon-picker-preview ' + (icon ? 'tabler-' + icon : 'tabler-ban');
            container.querySelectorAll('.icon-picker-grid .icon-picker-btn').forEach(b => {
                b.classList.toggle('active', (b.dataset.icon || '') === (icon || ''));
            });
        }

        function buildIconGrid(container) {
            const grid = container.querySelector('.icon-picker-grid');
            const makeBtn = (icon, title) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-icon btn-outline-secondary btn-sm icon-picker-btn';
                btn.title = title || icon;
                btn.dataset.icon = icon || '';
                btn.innerHTML = `<i class="icon-base ti tabler-${icon || 'ban'}"></i>`;
                btn.addEventListener('click', () => setRowIcon(container, icon));
                return btn;
            };
            grid.appendChild(makeBtn(null, 'Sin ícono'));
            COMMON_ICONS.forEach(icon => grid.appendChild(makeBtn(icon)));
        }

        function updateRowUrlBadge(li) {
            const dest = li.dataset.dest;
            const badge = li.querySelector('.menu-item-row-url');

            if (dest === 'page') {
                const option = li.querySelector('.f-page').selectedOptions[0];
                badge.textContent = option && option.value ? '/' + option.dataset.slug : '';
            } else if (dest === 'none') {
                badge.textContent = '';
            } else {
                badge.textContent = li.querySelector('.f-url').value.trim();
            }
        }

        function setRowDestType(li, dest) {
            if (!['url', 'page', 'none'].includes(dest)) {
                dest = 'url';
            }
            li.dataset.dest = dest;
            li.dataset.type = dest === 'page' ? 'page' : 'url';

            li.querySelector('.field-url').classList.toggle('d-none', dest !== 'url');
            li.querySelector('.field-page').classList.toggle('d-none', dest !== 'page');
            li.querySelector('.field-none').classList.toggle('d-none', dest !== 'none');
            li.querySelectorAll('.dest-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.dest === dest);
            });

            const typeIcon = li.querySelector('.menu-item-type-icon');
            const iconByDest = {
                page: 'tabler-file-text',
                none: 'tabler-folder',
                url: 'tabler-link'
            };
            const titleByDest = {
                page: 'Página',
                none: 'Sin destino (agrupador)',
                url: 'Enlace'
            };
            typeIcon.className = 'icon-base ti menu-item-type-icon ' + iconByDest[dest];
            typeIcon.title = titleByDest[dest];

            updateRowUrlBadge(li);
        }

        function createRow({
            clientId = null,
            id = null,
            label = '',
            type = 'url',
            dest = null,
            url = '',
            pageId = '',
            pageSlug = '',
            icon = '',
            target = '_self',
            isActive = true
        } = {}) {
            dest = dest || (type === 'page' ? 'page' : (url ? 'url' : 'none'));
            const template = document.getElementById('rowTemplate');
            const node = template.content.cloneNode(true);
            const li = node.querySelector('.menu-item-row');

            const cid = clientId || nextClientId();
            li.dataset.clientId = cid;
            if (id) {
                li.dataset.id = id;
            }

            li.querySelector('.menu-item-row-title').textContent = label;
            li.querySelector('.menu-item-row-title').title = label;
            li.querySelector('.f-label').value = label;
            li.querySelector('.f-url').value = url;
            li.querySelector('.f-target').value = target;
            li.querySelector('.f-active').checked = isActive;
            li.querySelector('.row-inactive-badge').classList.toggle('d-none', isActive);

            buildIconGrid(li);
            setRowIcon(li, icon);

            if (dest === 'page') {
                li.querySelector('.f-page').value = pageId;
            }
            setRowDestType(li, dest);
            if (dest === 'page' && pageSlug) {
                li.querySelector('.menu-item-row-url').textContent = '/' + pageSlug;
            }

            li.querySelectorAll('.dest-btn').forEach(btn => {
                btn.addEventListener('click', () => setRowDestType(li, btn.dataset.dest));
            });

            li.querySelector('.f-page').addEventListener('change', () => updateRowUrlBadge(li));

            const labelInput = li.querySelector('.f-label');
            labelInput.addEventListener('input', () => {
                li.querySelector('.menu-item-row-title').textContent = labelInput.value || '(sin etiqueta)';
                if (labelInput.classList.contains('is-invalid')) {
                    validateRow(li);
                }
            });
            labelInput.addEventListener('blur', () => validateRow(li));

            const urlInput = li.querySelector('.f-url');
            urlInput.addEventListener('input', () => {
                if (urlInput.classList.contains('is-invalid')) {
                    validateRow(li);
                }
                updateRowUrlBadge(li);
            });
            urlInput.addEventListener('blur', () => validateRow(li));

            const activeInput = li.querySelector('.f-active');
            activeInput.addEventListener('change', () => {
                li.querySelector('.row-inactive-badge').classList.toggle('d-none', activeInput.checked);
            });

            li.querySelectorAll('.f-label, .f-url, .f-target, .f-active, .dest-btn, .icon-picker-btn').forEach(el => {
                el.addEventListener('input', markDirty);
                el.addEventListener('change', markDirty);
                el.addEventListener('click', markDirty);
            });

            li.querySelector('.row-toggle').addEventListener('click', function() {
                const body = li.querySelector('.menu-item-row-body');
                body.classList.toggle('d-none');
                this.classList.toggle('expanded');
                if (!body.classList.contains('d-none')) {
                    initSelect2On(li.querySelector('.f-target'));
                    initSelect2On(li.querySelector('.f-page'));
                }
            });

            li.querySelector('.row-collapse').addEventListener('click', function() {
                li.querySelector('.menu-item-row-body').classList.add('d-none');
                li.querySelector('.row-toggle').classList.remove('expanded');
            });

            li.querySelector('.row-remove').addEventListener('click', function() {
                confirmAction({
                    title: '¿Eliminar ítem?',
                    text: `"${label}" y sus sub-ítems serán eliminados al guardar.`,
                    isDanger: true,
                    confirmText: 'Sí, eliminar',
                    onConfirm: () => {
                        if (li.dataset.id) {
                            deletedIds.push(parseInt(li.dataset.id, 10));
                        }
                        li.remove();
                        toggleEmptyState();
                        markDirty();
                    },
                });
            });

            return li;
        }

        function validateRow(li) {
            const labelInput = li.querySelector('.f-label');
            const urlInput = li.querySelector('.f-url');
            const dest = li.dataset.dest;

            const labelValid = labelInput.value.trim().length > 0;
            labelInput.classList.toggle('is-invalid', !labelValid);

            if (dest === 'url') {
                const urlValid = urlInput.value.trim().length > 0;
                urlInput.classList.toggle('is-invalid', !urlValid);
            } else {
                urlInput.classList.remove('is-invalid');
            }

            return labelValid && (dest !== 'url' || urlInput.value.trim().length > 0);
        }

        function appendRowToStructure(li, parentUl = null) {
            const container = parentUl || document.getElementById('menuStructure');
            container.appendChild(li);
            toggleEmptyState();
        }

        function toggleEmptyState() {
            const structure = document.getElementById('menuStructure');
            const isEmpty = structure.children.length === 0;
            document.getElementById('menuStructureEmpty').classList.toggle('d-none', !isEmpty);
        }

        const newLinkIconWrapper = document.getElementById('new_link_icon_wrapper');
        buildIconGrid(newLinkIconWrapper);
        setRowIcon(newLinkIconWrapper, null);

        const newPagesIconWrapper = document.getElementById('new_pages_icon_wrapper');
        if (newPagesIconWrapper) {
            buildIconGrid(newPagesIconWrapper);
            setRowIcon(newPagesIconWrapper, null);
        }

        const newLinkNoTarget = document.getElementById('new_link_no_target');
        newLinkNoTarget.addEventListener('change', function() {
            document.getElementById('new_link_url_wrapper').classList.toggle('d-none', this.checked);
        });

        document.getElementById('btnAddLink').addEventListener('click', function() {
            const noTarget = newLinkNoTarget.checked;
            const url = document.getElementById('new_link_url').value.trim();
            const label = document.getElementById('new_link_label').value.trim() || url;
            const icon = newLinkIconWrapper.querySelector('.f-icon').value;

            if (!noTarget && !url) {
                showToast('warning', 'Escribe una URL para el enlace, o marca "Sin destino".');
                return;
            }

            if (noTarget && !document.getElementById('new_link_label').value.trim()) {
                showToast('warning', 'Escribe un texto para el ítem.');
                return;
            }

            const li = createRow({
                label: noTarget ? document.getElementById('new_link_label').value.trim() : label,
                dest: noTarget ? 'none' : 'url',
                url: noTarget ? '' : url,
                icon
            });
            appendRowToStructure(li);
            markDirty();

            document.getElementById('new_link_url').value = '';
            document.getElementById('new_link_label').value = '';
            newLinkNoTarget.checked = false;
            document.getElementById('new_link_url_wrapper').classList.remove('d-none');
            setRowIcon(newLinkIconWrapper, null);
        });

        const btnAddPages = document.getElementById('btnAddPages');
        if (btnAddPages) {
            btnAddPages.addEventListener('click', function() {
                const checked = document.querySelectorAll('#pagesList input:checked');

                if (checked.length === 0) {
                    showToast('warning', 'Selecciona al menos una página.');
                    return;
                }

                const icon = newPagesIconWrapper ? newPagesIconWrapper.querySelector('.f-icon').value : '';

                checked.forEach(cb => {
                    const li = createRow({
                        label: cb.dataset.label,
                        type: 'page',
                        pageId: cb.value,
                        pageSlug: cb.dataset.slug,
                        icon
                    });
                    appendRowToStructure(li);
                    cb.checked = false;
                });
                markDirty();
                if (newPagesIconWrapper) {
                    setRowIcon(newPagesIconWrapper, null);
                }
            });
        }

        const pagesSearchFilter = document.getElementById('pagesSearchFilter');
        if (pagesSearchFilter) {
            pagesSearchFilter.addEventListener('input', function() {
                const term = this.value.trim().toLowerCase();
                document.querySelectorAll('#pagesList .list-group-item').forEach(item => {
                    item.classList.toggle('d-none', !item.textContent.toLowerCase().includes(term));
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
                onEnd: markDirty,
            });
        }

        function buildPayloadFromDom() {
            const items = [];

            function walk(ul, parentClientId, orderStart) {
                Array.from(ul.children).forEach((li, index) => {
                    if (!li.classList.contains('menu-item-row')) {
                        return;
                    }

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
                    if (childUl) {
                        walk(childUl, li.dataset.clientId, 0);
                    }
                });
            }

            walk(document.getElementById('menuStructure'), null, 0);

            return items;
        }

        document.getElementById('menuForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let allRowsValid = true;
            document.querySelectorAll('.menu-item-row').forEach(li => {
                if (!validateRow(li)) {
                    allRowsValid = false;
                    li.querySelector('.menu-item-row-body').classList.remove('d-none');
                    li.querySelector('.row-toggle').classList.add('expanded');
                }
            });

            if (!allRowsValid) {
                showToast('error', 'Completa la etiqueta y el destino de todos los ítems antes de guardar.');
                return;
            }

            const items = buildPayloadFromDom();

            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';

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
                    if (value === null || value === undefined) {
                        return;
                    }
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `items[${i}][${key}]`;
                    input.value = value === true ? '1' : (value === false ? '0' : value);
                    container.appendChild(input);
                });
            });

            formDirty = false;
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
                pageSlug: nodeData.page ? nodeData.page.slug : '',
                icon: nodeData.icon || '',
                target: nodeData.target,
                isActive: !!nodeData.is_active,
            });

            appendRowToStructure(li, parentUl);

            const childUl = li.querySelector(':scope > .menu-structure-children');
            initSortable(childUl);

            (nodeData.children || []).forEach(child => renderNode(child, childUl));
        }

        window.addEventListener('load', function() {
            initSortable(document.getElementById('menuStructure'));
            initialTree.forEach(node => renderNode(node));
        });
    </script>
@endsection
