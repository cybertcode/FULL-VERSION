@extends('layouts/layoutMaster')

@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  ])
  @yield('admin-vendor-style')
@endsection

@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  ])
  @yield('admin-vendor-script')
@endsection

@section('page-script')
  {{-- Bootstrap Toast container (top-right, posición fija sobre el layout) --}}
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999">
    <div id="bs-toast-global" class="bs-toast toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
      <div class="toast-header">
        <i id="bs-toast-icon" class="icon-base ti icon-xs me-2"></i>
        <div class="me-auto fw-medium" id="bs-toast-title">Notificación</div>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body" id="bs-toast-body"></div>
    </div>
  </div>

  <script>
  'use strict';

  (function () {
    var _toastEl  = document.getElementById('bs-toast-global');
    var _toastObj = null;

    var _cfg = {
      success : { bg: 'bg-success', icon: 'tabler-circle-check',      title: 'Éxito'       },
      error   : { bg: 'bg-danger',  icon: 'tabler-xbox-x',            title: 'Error'        },
      info    : { bg: 'bg-info',    icon: 'tabler-info-circle',        title: 'Información'  },
      warning : { bg: 'bg-warning', icon: 'tabler-alert-triangle',     title: 'Advertencia'  }
    };

    window.showToast = function (type, message) {
      var c    = _cfg[type] || _cfg.info;
      var icon = document.getElementById('bs-toast-icon');
      var body = document.getElementById('bs-toast-body');
      var hdr  = _toastEl.querySelector('.toast-header');

      // limpiar clases bg anteriores
      ['bg-success','bg-danger','bg-info','bg-warning'].forEach(function(cls){ hdr.classList.remove(cls); icon.className = ''; });

      hdr.classList.add(c.bg, 'text-white');
      icon.className = 'icon-base ti ' + c.icon + ' icon-xs me-2';
      document.getElementById('bs-toast-title').textContent = c.title;
      body.textContent = message;

      if (_toastObj) _toastObj.hide();
      _toastObj = new bootstrap.Toast(_toastEl);
      _toastObj.show();
    };
  })();

  window.addEventListener('load', function () {
    window.showAlert = function(title, text, icon) {
      Swal.fire({
        title          : title,
        text           : text,
        icon           : icon || 'info',
        buttonsStyling : false,
        customClass    : { confirmButton: 'btn btn-primary waves-effect waves-light' }
      });
    };

    window.confirmAction = function(opts) {
      Swal.fire({
        title            : opts.title || '¿Estás seguro?',
        text             : opts.text,
        icon             : opts.icon || 'warning',
        showCancelButton : true,
        confirmButtonText: opts.confirmText || 'Sí, continuar',
        cancelButtonText : opts.cancelText  || 'Cancelar',
        reverseButtons   : true,
        focusCancel      : true,
        buttonsStyling   : false,
        customClass      : {
          confirmButton: opts.isDanger
            ? 'btn btn-danger waves-effect waves-light'
            : 'btn btn-primary waves-effect waves-light',
          cancelButton : 'btn btn-label-secondary waves-effect'
        }
      }).then(function(result) {
        if (result.isConfirmed && opts.onConfirm)                               opts.onConfirm();
        else if (result.dismiss === Swal.DismissReason.cancel && opts.onCancel) opts.onCancel();
      });
    };

    window.confirmDelete = function(formId, name) {
      Swal.fire({
        title            : '¿Eliminar' + (name ? ' "' + name + '"' : ' este elemento') + '?',
        text             : 'Esta acción es permanente y no se puede deshacer.',
        icon             : 'warning',
        showCancelButton : true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText : 'Cancelar',
        reverseButtons   : true,
        focusCancel      : true,
        buttonsStyling   : false,
        customClass      : {
          confirmButton: 'btn btn-danger waves-effect waves-light',
          cancelButton : 'btn btn-label-secondary waves-effect'
        }
      }).then(function(result) {
        if (result.isConfirmed) {
          var form = document.getElementById(formId);
          if (form) form.submit();
        }
      });
    };
  }); // end window.load — SweetAlert2 es módulo ES, necesita esperar
  </script>

  {{-- Flash messages DESPUÉS del init de Notyf --}}
  <x-flash-messages />

  @yield('admin-page-script')
@endsection

@section('content')
  @yield('admin-content')
@endsection
