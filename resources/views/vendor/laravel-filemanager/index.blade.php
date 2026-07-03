<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Chrome, Firefox OS and Opera -->
  <meta name="theme-color" content="#333844">
  <!-- Windows Phone -->
  <meta name="msapplication-navbutton-color" content="#333844">
  <!-- iOS Safari -->
  <meta name="apple-mobile-web-app-status-bar-style" content="#333844">

  <title>{{ trans('laravel-filemanager::lfm.title-page') }}</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('vendor/laravel-filemanager/img/72px color.png') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-ui-dist@1.12.1/jquery-ui.min.css">
  <link rel="stylesheet" href="{{ asset('vendor/laravel-filemanager/css/cropper.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/laravel-filemanager/css/dropzone.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/laravel-filemanager/css/mime-icons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/vendor/laravel-filemanager/css/lfm.css') }}">
  <style>
    /* ════════════════════════════════════════════════
       LFM × Vuexy — Sistema de diseño exacto del tema
    ════════════════════════════════════════════════ */
    :root {
      /* Colores exactos extraídos de la plantilla */
      --lfm-primary:    #1340A0;
      --lfm-primary-rgb: 19, 64, 160;
      --lfm-primary-dk: #0f3280;
      --lfm-primary-lt: rgba(19,64,160,.08);
      --lfm-accent:     #7367f0;
      --lfm-accent-lt:  rgba(115,103,240,.08);
      --lfm-success:    #28c76f;
      --lfm-border:     #e6e6e8;
      --lfm-bg:         #f8f7fa;
      --lfm-bg-sidebar: #f8f7fa;
      --lfm-text:       #6d6b77;
      --lfm-text-dark:  #444050;
      --lfm-text-head:  #444050;
      --lfm-white:      #ffffff;
      /* Valores de diseño exactos de Vuexy */
      --lfm-radius:     6px;
      --lfm-radius-lg:  8px;
      --lfm-shadow:     0 .1875rem .75rem 0 rgba(47,43,61,.14);
      --lfm-shadow-sm:  0 .125rem .5rem 0 rgba(47,43,61,.12);
    }

    /* ══════════════════════════════════════════
       BASE
    ══════════════════════════════════════════ */
    *, *::before, *::after { box-sizing: border-box; }
    html, body { height: 100%; margin: 0; }
    body {
      font-family: "Public Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      font-size: 13px;
      line-height: 1.5;
      color: var(--lfm-text);
      background: var(--lfm-bg);
    }
    a { color: var(--lfm-primary); text-decoration: none; }

    /* ══════════════════════════════════════════
       TOPBAR — barra superior con controles
    ══════════════════════════════════════════ */
    #nav, #nav .dropdown-menu, .bg-main {
      background: var(--lfm-white) !important;
      background-color: var(--lfm-white) !important;
    }
    #nav {
      border-bottom: 1px solid var(--lfm-border) !important;
      box-shadow: var(--lfm-shadow-sm) !important;
      padding: 0 20px;
      min-height: 52px;
    }
    #nav a, #fab a { color: var(--lfm-text) !important; }
    #nav .navbar-brand, #nav .nav-link {
      color: var(--lfm-text) !important;
      font-size: 13px;
      font-weight: 500;
      letter-spacing: .01em;
      transition: color .2s;
    }
    #nav .nav-link:hover, #nav .navbar-brand:hover { color: var(--lfm-primary) !important; }
    #nav .dropdown-menu {
      border: 1px solid var(--lfm-border) !important;
      box-shadow: var(--lfm-shadow) !important;
      border-radius: var(--lfm-radius-lg) !important;
      padding: 6px !important;
      min-width: 160px;
    }
    #nav .dropdown-menu > a {
      border-radius: var(--lfm-radius) !important;
      padding: 7px 14px;
      font-size: 13px;
      color: var(--lfm-text-dark) !important;
      transition: background .15s, color .15s;
    }
    #nav .dropdown-menu > a:hover {
      background: var(--lfm-primary-lt) !important;
      color: var(--lfm-primary) !important;
    }
    #nav .navbar-toggler { color: var(--lfm-text) !important; border: none; outline: none; }
    #cancel_selection { color: var(--lfm-text) !important; }

    /* ══════════════════════════════════════════
       LAYOUT PRINCIPAL
    ══════════════════════════════════════════ */
    .d-flex.flex-row { min-height: calc(100vh - 52px); }

    /* ── Sidebar / árbol de carpetas ── */
    #tree {
      background: var(--lfm-bg-sidebar) !important;
      border-right: 1px solid var(--lfm-border);
      min-width: 220px;
      width: 220px;
      padding: 16px 10px;
      overflow-y: auto;
    }
    /* Nodos jstree */
    .jstree-default .jstree-anchor {
      color: var(--lfm-text-dark);
      font-size: 13px;
      padding: 5px 10px;
      border-radius: var(--lfm-radius);
      line-height: 1.4;
      height: auto;
    }
    .jstree-default .jstree-clicked {
      background: var(--lfm-primary-lt) !important;
      color: var(--lfm-primary) !important;
      border-radius: var(--lfm-radius) !important;
      font-weight: 600;
      box-shadow: none !important;
    }
    .jstree-default .jstree-hovered {
      background: rgba(var(--lfm-primary-rgb), .05) !important;
      border-radius: var(--lfm-radius) !important;
      box-shadow: none !important;
    }
    .jstree-default .jstree-icon.jstree-themeicon { color: var(--lfm-primary); }

    /* ── Área de contenido ── */
    #main {
      background: var(--lfm-white);
      width: 100%;
      display: flex;
      flex-direction: column;
    }

    /* ══════════════════════════════════════════
       BREADCRUMB
    ══════════════════════════════════════════ */
    #breadcrumbs { padding: 10px 20px 0; }
    .breadcrumb {
      background: transparent;
      padding: 0;
      margin: 0;
      font-size: 12px;
    }
    .breadcrumb-item a {
      color: var(--lfm-primary);
      font-weight: 500;
      transition: opacity .2s;
    }
    .breadcrumb-item a:hover { opacity: .75; }
    .breadcrumb-item.active { color: var(--lfm-text); }
    .breadcrumb-item + .breadcrumb-item::before { color: var(--lfm-border); content: "/"; }
    .breadcrumb-item:not(.active) { transition: color .2s; }
    .breadcrumb-item:not(.active):hover { color: var(--lfm-primary) !important; cursor: pointer; }

    /* ══════════════════════════════════════════
       BARRA ACCIONES (Selección múltiple + Buscar)
    ══════════════════════════════════════════ */
    .action-bar {
      display: flex !important;
      flex-direction: row !important;
      align-items: center !important;
      flex-wrap: nowrap !important;
      padding: 10px 20px !important;
      margin-bottom: 0 !important;
      gap: 12px;
      background: var(--lfm-bg) !important;
      border-bottom: 1px solid var(--lfm-border) !important;
    }
    .multiple-selection-toggle-label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--lfm-text);
      font-size: 13px;
      margin: 0;
      white-space: nowrap;
      cursor: pointer;
      user-select: none;
    }
    #multiple-selection-toggle {
      width: 16px !important;
      height: 16px !important;
      accent-color: var(--lfm-primary);
      cursor: pointer;
    }
    .search-bar {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-left: auto;
    }
    .search-bar .form-control {
      height: 34px;
      font-size: 13px;
      border: 1px solid var(--lfm-border);
      border-radius: var(--lfm-radius);
      padding: 4px 12px;
      color: var(--lfm-text-dark);
      background: var(--lfm-white);
      transition: border-color .2s, box-shadow .2s;
      min-width: 180px;
    }
    .search-bar .form-control::placeholder { color: var(--lfm-text); opacity: .6; }
    .search-bar .form-control:focus {
      border-color: var(--lfm-primary);
      box-shadow: 0 0 0 3px rgba(var(--lfm-primary-rgb), .15);
      outline: none;
    }

    /* ══════════════════════════════════════════
       BOTONES — exactos como en Vuexy
    ══════════════════════════════════════════ */
    .btn {
      border-radius: 4px !important;
      font-size: 13px !important;
      font-weight: 500 !important;
      padding: 6px 16px !important;
      line-height: 1.5 !important;
      letter-spacing: .01em;
      transition: background-color .2s, border-color .2s, box-shadow .2s, transform .1s !important;
    }
    .btn:active { transform: scale(.97); }
    .btn-primary {
      background-color: var(--lfm-primary) !important;
      border-color: var(--lfm-primary) !important;
      color: #fff !important;
      box-shadow: 0 4px 12px rgba(var(--lfm-primary-rgb), .35) !important;
    }
    .btn-primary:hover {
      background-color: var(--lfm-primary-dk) !important;
      border-color: var(--lfm-primary-dk) !important;
      box-shadow: 0 6px 16px rgba(var(--lfm-primary-rgb), .45) !important;
    }
    .btn-outline-primary {
      color: var(--lfm-primary) !important;
      border-color: var(--lfm-primary) !important;
      background: transparent !important;
    }
    .btn-outline-primary:hover {
      background-color: var(--lfm-primary) !important;
      color: #fff !important;
      box-shadow: 0 4px 12px rgba(var(--lfm-primary-rgb), .3) !important;
    }
    .btn-secondary {
      background-color: #8592a3 !important;
      border-color: #8592a3 !important;
      color: #fff !important;
    }
    .btn-outline-secondary {
      color: var(--lfm-text) !important;
      border-color: var(--lfm-border) !important;
      background: transparent !important;
    }
    .btn-outline-secondary:hover {
      background-color: var(--lfm-bg) !important;
      color: var(--lfm-text-dark) !important;
    }

    /* ══════════════════════════════════════════
       GRID DE ARCHIVOS — tarjetas tipo Vuexy
    ══════════════════════════════════════════ */
    #content { flex: 1; }
    .grid { padding: 20px; justify-content: flex-start; }
    .grid a {
      margin: 6px;
      display: inline-flex;
      flex-direction: column;
      align-self: flex-start;
      width: auto !important;
      height: auto !important;
      border-radius: var(--lfm-radius-lg);
      overflow: hidden;
      box-shadow: var(--lfm-shadow-sm);
      border: 1px solid var(--lfm-border);
      background: var(--lfm-white);
      transition: box-shadow .2s, transform .2s, border-color .2s;
    }
    .grid a:hover {
      box-shadow: var(--lfm-shadow) !important;
      border-color: var(--lfm-primary) !important;
      transform: translateY(-2px);
    }
    .grid .square {
      border: none !important;
      border-radius: 0 !important;
      width: 140px !important;
      height: 120px !important;
      background-color: var(--lfm-bg);
      flex-shrink: 0;
      transition: none;
    }
    .grid .square > div {
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
    }
    .grid a:hover .square { border: none !important; box-shadow: none; }
    .grid .square.selected {
      border: none !important;
      padding: 0;
      box-shadow: none !important;
    }
    .grid a:has(.square.selected) {
      border-color: var(--lfm-primary) !important;
      box-shadow: 0 0 0 2px var(--lfm-primary), var(--lfm-shadow) !important;
    }
    .grid .item_name {
      border: none !important;
      border-top: 1px solid var(--lfm-border) !important;
      border-radius: 0 !important;
      margin-top: 0 !important;
      font-size: 12px;
      font-weight: 500;
      padding: 8px 10px;
      text-align: center;
      width: 140px;
      max-width: 140px;
      color: var(--lfm-text-dark);
      background: var(--lfm-white);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    /* Íconos de carpetas y archivos */
    .grid .square > i {
      color: var(--lfm-primary) !important;
      font-size: 56px !important;
      padding: 0 !important;
    }

    /* ══════════════════════════════════════════
       VISTA LISTA
    ══════════════════════════════════════════ */
    .list a {
      border: none !important;
      border-bottom: 1px solid var(--lfm-border) !important;
      padding: 12px 20px;
      transition: background .15s;
      align-items: center;
    }
    .list a:first-child { border-top: 1px solid var(--lfm-border) !important; }
    .list a:last-child { border-bottom: 1px solid var(--lfm-border) !important; }
    .list a:hover { background: var(--lfm-primary-lt); }
    .list .square {
      border: 1px solid var(--lfm-border) !important;
      border-radius: var(--lfm-radius);
      overflow: hidden;
      background: var(--lfm-bg);
      width: 48px !important;
      height: 48px !important;
    }
    .list .square > i { font-size: 28px !important; color: var(--lfm-primary) !important; }
    .list .square.selected { border-color: var(--lfm-primary) !important; }
    .list .item_name { font-size: 13px; font-weight: 600; color: var(--lfm-text-dark); }
    .list time { font-size: 12px; color: var(--lfm-text); margin-top: 2px; }

    /* ══════════════════════════════════════════
       FAB — botón de acción flotante
    ══════════════════════════════════════════ */
    .fab-wrapper { margin: 24px; }
    .fab-wrapper .fab-button {
      background-color: var(--lfm-primary) !important;
      box-shadow: 0 6px 20px rgba(var(--lfm-primary-rgb), .45) !important;
      width: 52px !important;
      height: 52px !important;
      border-radius: 50%;
      transition: background-color .2s, box-shadow .2s, transform .2s !important;
    }
    .fab-wrapper .fab-button:hover {
      background-color: var(--lfm-primary-dk) !important;
      box-shadow: 0 8px 24px rgba(var(--lfm-primary-rgb), .55) !important;
      transform: scale(1.08);
    }
    .fab-wrapper .fab-toggle i { transition: transform .3s !important; }
    .fab-wrapper.fab-expand .fab-toggle i { transform: rotate(-45deg) !important; }
    .fab-wrapper .fab-action {
      background-color: var(--lfm-success) !important;
      box-shadow: 0 4px 14px rgba(40,199,111,.4) !important;
    }
    .fab-wrapper .fab-action:hover { background-color: #24b263 !important; }
    .fab-wrapper .fab-action::before {
      background-color: rgba(var(--lfm-primary-rgb), .85) !important;
      border-radius: var(--lfm-radius) !important;
      font-size: 12px;
      padding: 4px 10px !important;
    }

    /* ══════════════════════════════════════════
       PAGINACIÓN
    ══════════════════════════════════════════ */
    #pagination > ul.pagination { margin: 20px 0; gap: 2px; }
    .page-link {
      color: var(--lfm-primary);
      border: 1px solid var(--lfm-border);
      border-radius: var(--lfm-radius) !important;
      padding: 5px 11px;
      font-size: 13px;
      margin: 0 1px;
      transition: background .15s, color .15s, border-color .15s;
    }
    .page-link:hover {
      background: var(--lfm-primary-lt);
      border-color: var(--lfm-primary);
      color: var(--lfm-primary);
    }
    .page-item.active .page-link {
      background-color: var(--lfm-primary);
      border-color: var(--lfm-primary);
      color: #fff;
      box-shadow: 0 3px 8px rgba(var(--lfm-primary-rgb), .3);
    }
    .page-item.disabled .page-link { color: var(--lfm-text); opacity: .5; }

    /* ══════════════════════════════════════════
       CARPETA VACÍA
    ══════════════════════════════════════════ */
    #empty {
      color: #c2c0ca;
      height: 55vh;
    }
    #empty > i {
      font-size: 72px;
      color: #dddce1;
      margin-bottom: 8px;
    }
    #empty::after {
      content: attr(data-empty-message);
      font-size: 13px;
      color: var(--lfm-text);
      margin-top: 4px;
    }

    /* ══════════════════════════════════════════
       MODALES
    ══════════════════════════════════════════ */
    .modal-backdrop { background-color: rgba(47,43,61,.5); }
    .modal-content {
      border: none;
      border-radius: var(--lfm-radius-lg);
      box-shadow: 0 8px 32px rgba(47,43,61,.2);
      overflow: hidden;
    }
    .modal-header {
      border-bottom: 1px solid var(--lfm-border);
      padding: 18px 24px 16px;
      background: var(--lfm-white);
    }
    .modal-header .modal-title {
      font-size: 15px;
      font-weight: 600;
      color: var(--lfm-text-head);
    }
    .modal-header .close {
      color: var(--lfm-text);
      opacity: .6;
      font-size: 18px;
      padding: 0;
      line-height: 1;
    }
    .modal-header .close:hover { opacity: 1; color: var(--lfm-text-dark); }
    .modal-body { padding: 20px 24px; }
    .modal-footer {
      border-top: 1px solid var(--lfm-border);
      padding: 14px 24px;
      gap: 8px;
    }
    .modal-footer .btn { min-width: 110px; }
    /* Input dentro de modal */
    .modal-body .form-control {
      border: 1px solid var(--lfm-border);
      border-radius: var(--lfm-radius);
      font-size: 13px;
      padding: 8px 14px;
      color: var(--lfm-text-dark);
      transition: border-color .2s, box-shadow .2s;
    }
    .modal-body .form-control:focus {
      border-color: var(--lfm-primary);
      box-shadow: 0 0 0 3px rgba(var(--lfm-primary-rgb), .15);
      outline: none;
    }

    /* ══════════════════════════════════════════
       MENÚ CONTEXTUAL
    ══════════════════════════════════════════ */
    .dropdown-menu {
      border: 1px solid var(--lfm-border) !important;
      box-shadow: 0 8px 28px rgba(47,43,61,.14) !important;
      border-radius: var(--lfm-radius-lg) !important;
      padding: 6px !important;
      background: var(--lfm-white) !important;
      min-width: 160px;
    }
    .dropdown-item {
      border-radius: var(--lfm-radius) !important;
      font-size: 13px;
      color: var(--lfm-text-dark);
      padding: 7px 14px;
      display: flex;
      align-items: center;
      gap: 10px;
      transition: background .15s, color .15s;
    }
    .dropdown-item:hover {
      background-color: var(--lfm-primary-lt) !important;
      color: var(--lfm-primary) !important;
    }
    .dropdown-item i {
      width: 16px;
      font-size: 14px;
      color: var(--lfm-text);
      text-align: center;
      transition: color .15s;
    }
    .dropdown-item:hover i { color: var(--lfm-primary); }

    /* ══════════════════════════════════════════
       DROPZONE UPLOAD
    ══════════════════════════════════════════ */
    #uploadForm {
      border-radius: var(--lfm-radius-lg);
      overflow: hidden;
    }
    #uploadForm > .dz-default.dz-message {
      border: 2px dashed var(--lfm-primary) !important;
      border-radius: var(--lfm-radius-lg);
      color: var(--lfm-text);
      padding: 48px 20px;
      text-align: center;
      background: var(--lfm-primary-lt);
      font-size: 14px;
      transition: background .2s;
    }
    #uploadForm > .dz-default.dz-message:hover { background: rgba(var(--lfm-primary-rgb), .12); }
    .dz-preview .dz-image { border-radius: var(--lfm-radius); overflow: hidden; }
    .dz-preview.dz-success .dz-success-mark svg { color: var(--lfm-success); }
    .dz-preview .dz-progress { border-radius: 2px; }
    .dz-preview .dz-upload { background: var(--lfm-primary) !important; }

    /* ══════════════════════════════════════════
       ÍCONOS MIME
    ══════════════════════════════════════════ */
    .square > i { color: var(--lfm-primary) !important; }
    .mime-icon .ico { color: var(--lfm-text); }

    /* ══════════════════════════════════════════
       ALERTS / NOTIFICACIONES
    ══════════════════════════════════════════ */
    #alerts .alert {
      border-radius: var(--lfm-radius);
      font-size: 13px;
      margin: 12px 20px 0;
      border: none;
      padding: 10px 16px;
    }
    #alerts .alert-success { background: rgba(40,199,111,.12); color: #1a7a43; }
    #alerts .alert-danger  { background: rgba(234,84,85,.12);  color: #c22121; }
    #alerts .alert-warning { background: rgba(255,159,67,.12); color: #a05c00; }

    /* ══════════════════════════════════════════
       SCROLLBAR ELEGANTE
    ══════════════════════════════════════════ */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb {
      background: var(--lfm-border);
      border-radius: 3px;
    }
    ::-webkit-scrollbar-thumb:hover { background: rgba(var(--lfm-primary-rgb), .35); }
  </style>
</head>
<body>
  <nav class="navbar sticky-top navbar-expand-lg navbar-dark" id="nav">
    <a class="navbar-brand invisible-lg d-none d-lg-inline" id="to-previous">
      <i class="fas fa-arrow-left fa-fw"></i>
      <span class="d-none d-lg-inline">{{ trans('laravel-filemanager::lfm.nav-back') }}</span>
    </a>
    <a class="navbar-brand d-block d-lg-none" id="show_tree">
      <i class="fas fa-bars fa-fw"></i>
    </a>
    <a class="navbar-brand d-block d-lg-none" id="current_folder"></a>
    <a id="loading" class="navbar-brand"><i class="fas fa-spinner fa-spin"></i></a>
    <div class="ml-auto px-2">
      <a class="navbar-link d-none" id="cancel_selection">
        <i class="fa fa-times fa-fw"></i>
        <span class="d-none d-lg-inline">{{ trans('laravel-filemanager::lfm.menu-cancel-selection') }}</span>
      </a>
    </div>
    <a class="navbar-toggler collapsed border-0 px-1 py-2 m-0" data-toggle="collapse" data-target="#nav-buttons">
      <i class="fas fa-cog fa-fw"></i>
    </a>
    <div class="collapse navbar-collapse flex-grow-0" id="nav-buttons">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-display="grid">
            <i class="fas fa-th-large fa-fw"></i>
            <span>{{ trans('laravel-filemanager::lfm.nav-thumbnails') }}</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-display="list">
            <i class="fas fa-list-ul fa-fw"></i>
            <span>{{ trans('laravel-filemanager::lfm.nav-list') }}</span>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-sort fa-fw"></i>{{ trans('laravel-filemanager::lfm.nav-sort') }}
          </a>
          <div class="dropdown-menu dropdown-menu-right border-0"></div>
        </li>
      </ul>
    </div>
  </nav>

  <nav class="bg-light fixed-bottom border-top d-none" id="actions">
    <a data-action="openfolder" data-multiple="false"><i class="fas fa-folder-open"></i>{{ trans('laravel-filemanager::lfm.btn-open') }}</a>
    <a data-action="preview" data-multiple="true"><i class="fas fa-images"></i>{{ trans('laravel-filemanager::lfm.menu-view') }}</a>
    <a data-action="use" data-multiple="true"><i class="fas fa-check"></i>{{ trans('laravel-filemanager::lfm.btn-confirm') }}</a>
  </nav>

  <div class="d-flex flex-row">
    <div id="tree"></div>

    <div id="main">
      <div id="alerts"></div>

      <nav aria-label="breadcrumb" class="d-none d-lg-block" id="breadcrumbs">
        <ol class="breadcrumb">
          <li class="breadcrumb-item invisible">Home</li>
        </ol>
      </nav>

      <div class="action-bar">
        <label class="multiple-selection-toggle-label">
          <input type="checkbox" id="multiple-selection-toggle" style="width: 18px; height: 18px; margin-right: 8px">
          {{ trans('laravel-filemanager::lfm.menu-multiple') }}
        </label>

        <div class="search-bar">
          <input type="text" name="keyword" id="keyword" placeholder="{{ trans('laravel-filemanager::lfm.placeholder-keyword') }}" class="form-control">
          <button type="button" id="keyword-button" class="btn btn-outline-primary">{{ trans('laravel-filemanager::lfm.btn-search') }}</button>
          <button type="button" id="keyword-reset-button" class="btn btn-outline-secondary">{{ trans('laravel-filemanager::lfm.btn-reset') }}</button>
        </div>
      </div>

      <div id="empty" class="d-none">
        <i class="far fa-folder-open"></i>
        {{ trans('laravel-filemanager::lfm.message-empty') }}
      </div>

      <div id="content"></div>
      <div id="pagination"></div>

      <a id="item-template" class="d-none">
        <div class="square"></div>

        <div class="info">
          <div class="item_name text-truncate"></div>
          <time class="text-muted font-weight-light text-truncate"></time>
        </div>
      </a>
    </div>

    <div id="fab"></div>
  </div>

  <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">{{ trans('laravel-filemanager::lfm.title-upload') }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aia-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('unisharp.lfm.upload') }}" role='form' id='uploadForm' name='uploadForm' method='post' enctype='multipart/form-data' class="dropzone">
            <div class="form-group" id="attachment">
              <div class="controls text-center">
                <div class="input-group w-100">
                  <a class="btn btn-primary w-100 text-white" id="upload-button">{{ trans('laravel-filemanager::lfm.message-choose') }}</a>
                </div>
              </div>
            </div>
            <input type='hidden' name='working_dir' id='working_dir'>
            <input type='hidden' name='type' id='type' value='{{ request("type") }}'>
            <input type='hidden' name='_token' value='{{csrf_token()}}'>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary w-100" data-dismiss="modal">{{ trans('laravel-filemanager::lfm.btn-close') }}</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="notify" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary w-100" data-dismiss="modal">{{ trans('laravel-filemanager::lfm.btn-close') }}</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="confirm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary w-100" data-dismiss="modal">{{ trans('laravel-filemanager::lfm.btn-close') }}</button>
          <button type="button" class="btn btn-primary w-100" data-dismiss="modal" id="confirm-button-yes">{{ trans('laravel-filemanager::lfm.btn-confirm') }}</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="dialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <input type="text" class="form-control">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary w-100" data-dismiss="modal">{{ trans('laravel-filemanager::lfm.btn-close') }}</button>
          <button type="button" class="btn btn-primary w-100" data-dismiss="modal">{{ trans('laravel-filemanager::lfm.btn-confirm') }}</button>
        </div>
      </div>
    </div>
  </div>

  <div id="carouselTemplate" class="d-none carousel slide bg-light" data-ride="carousel">
    <ol class="carousel-indicators">
      <li data-target="#previewCarousel" data-slide-to="0" class="active"></li>
    </ol>
    <div class="carousel-inner">
      <div class="carousel-item active">
        <a class="carousel-label"></a>
        <div class="carousel-image"></div>
      </div>
    </div>
    <a class="carousel-control-prev" href="#previewCarousel" role="button" data-slide="prev">
      <div class="carousel-control-background" aria-hidden="true">
        <i class="fas fa-chevron-left"></i>
      </div>
      <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#previewCarousel" role="button" data-slide="next">
      <div class="carousel-control-background" aria-hidden="true">
        <i class="fas fa-chevron-right"></i>
      </div>
      <span class="sr-only">Next</span>
    </a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.2.1/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.3/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.0/dist/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-ui-dist@1.12.1/jquery-ui.min.js"></script>
  <script src="{{ asset('vendor/laravel-filemanager/js/cropper.min.js') }}"></script>
  <script src="{{ asset('vendor/laravel-filemanager/js/dropzone.min.js') }}"></script>
  <script>
    var lang = {!! json_encode(trans('laravel-filemanager::lfm')) !!};
    var actions = [
      // {
      //   name: 'use',
      //   icon: 'check',
      //   label: 'Confirm',
      //   multiple: true
      // },
      {
        name: 'rename',
        icon: 'edit',
        label: lang['menu-rename'],
        multiple: false
      },
      {
        name: 'download',
        icon: 'download',
        label: lang['menu-download'],
        multiple: true
      },
      // {
      //   name: 'preview',
      //   icon: 'image',
      //   label: lang['menu-view'],
      //   multiple: true
      // },
      {
        name: 'move',
        icon: 'paste',
        label: lang['menu-move'],
        multiple: true
      },
      {
        name: 'resize',
        icon: 'arrows-alt',
        label: lang['menu-resize'],
        multiple: false
      },
      {
        name: 'crop',
        icon: 'crop',
        label: lang['menu-crop'],
        multiple: false
      },
      {
        name: 'trash',
        icon: 'trash',
        label: lang['menu-delete'],
        multiple: true
      },
    ];

    var sortings = [
      {
        by: 'alphabetic',
        icon: 'sort-alpha-down',
        label: lang['nav-sort-alphabetic']
      },
      {
        by: 'time',
        icon: 'sort-numeric-down',
        label: lang['nav-sort-time']
      }
    ];
  </script>
  <script>{!! \File::get(base_path('vendor/unisharp/laravel-filemanager/public/js/script.js')) !!}</script>
  {{-- Use the line below instead of the above if you need to cache the script. --}}
  {{-- <script src="{{ asset('vendor/laravel-filemanager/js/script.js') }}"></script> --}}
  <script>
    Dropzone.options.uploadForm = {
      paramName: "upload[]", // The name that will be used to transfer the file
      uploadMultiple: false,
      parallelUploads: 5,
      timeout:0,
      clickable: '#upload-button',
      dictDefaultMessage: lang['message-drop'],
      init: function() {
        var _this = this; // For the closure
        this.on('success', function(file, response) {
          if (response == 'OK') {
            loadFolders();
          } else {
            this.defaultOptions.error(file, response.join('\n'));
          }
        });
      },
      acceptedFiles: "{{ implode(',', $helper->availableMimeTypes()) }}",
      maxFilesize: ({{ $helper->maxUploadSize() }} / 1000)
    }

    var token = getUrlParam('token');
    if (token !== null) {
      Dropzone.options.uploadForm.headers = {
        'Authorization': 'Bearer ' + token
      };
    }
  </script>
</body>
</html>
