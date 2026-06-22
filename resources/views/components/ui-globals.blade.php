{{--
  Componente global de UI — disponible en TODAS las páginas del proyecto.
  Provee: Bootstrap Toast (showToast), SweetAlert2 (showAlert, confirmAction, confirmDelete, confirmForm).
  SweetAlert2 y Bootstrap deben estar ya cargados antes de este componente.
--}}

{{-- Bootstrap Toast container — top-right, sobre cualquier elemento --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:99999">
  <div id="bs-toast-global" class="bs-toast toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
    <div class="toast-header">
      <i id="bs-toast-icon" class="icon-base ti icon-xs me-2"></i>
      <span class="me-auto fw-medium" id="bs-toast-title"></span>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="bs-toast-body"></div>
  </div>
</div>

<script>
'use strict';

// ─── Bootstrap Toast ──────────────────────────────────────────────────────────
(function () {
  var _el  = document.getElementById('bs-toast-global');
  var _obj = null;

  var _types = {
    success : { bg: 'bg-success', icon: 'tabler-circle-check',   title: 'Éxito'       },
    error   : { bg: 'bg-danger',  icon: 'tabler-xbox-x',         title: 'Error'        },
    info    : { bg: 'bg-info',    icon: 'tabler-info-circle',     title: 'Información'  },
    warning : { bg: 'bg-warning', icon: 'tabler-alert-triangle',  title: 'Advertencia'  }
  };

  /**
   * showToast(type, message, options?)
   *   type    : 'success' | 'error' | 'info' | 'warning'
   *   message : string
   *   options : { title?: string, delay?: number }
   */
  window.showToast = function (type, message, options) {
    var cfg  = _types[type] || _types.info;
    var opts = options || {};
    var hdr  = _el.querySelector('.toast-header');
    var icon = document.getElementById('bs-toast-icon');

    ['bg-success','bg-danger','bg-info','bg-warning'].forEach(function (c) { hdr.classList.remove(c); });
    hdr.classList.add(cfg.bg, 'text-white');
    icon.className = 'icon-base ti ' + cfg.icon + ' icon-xs me-2';
    document.getElementById('bs-toast-title').textContent = opts.title || cfg.title;
    document.getElementById('bs-toast-body').textContent  = message;

    if (opts.delay) _el.setAttribute('data-bs-delay', opts.delay);

    if (_obj) { try { _obj.hide(); } catch(e){} }
    _obj = new bootstrap.Toast(_el);
    _obj.show();
  };
})();

// ─── SweetAlert2 helpers (requieren que Swal esté cargado — módulo ES) ────────
window.addEventListener('load', function () {

  /**
   * showAlert(title, text, icon?)
   *   Muestra un modal informativo simple.
   *   icon: 'success' | 'error' | 'warning' | 'info' | 'question'
   */
  window.showAlert = function (title, text, icon) {
    return Swal.fire({
      title          : title,
      text           : text,
      icon           : icon || 'info',
      buttonsStyling : false,
      customClass    : { confirmButton: 'btn btn-primary waves-effect waves-light' }
    });
  };

  /**
   * showAlertHtml(title, html, icon?)
   *   Igual que showAlert pero el contenido acepta HTML.
   */
  window.showAlertHtml = function (title, html, icon) {
    return Swal.fire({
      title          : title,
      html           : html,
      icon           : icon || 'info',
      buttonsStyling : false,
      customClass    : { confirmButton: 'btn btn-primary waves-effect waves-light' }
    });
  };

  /**
   * confirmAction(opts)
   *   Diálogo de confirmación genérico.
   *   opts: {
   *     title?       : string,
   *     text?        : string,
   *     icon?        : string,           // default 'warning'
   *     confirmText? : string,           // default 'Sí, continuar'
   *     cancelText?  : string,           // default 'Cancelar'
   *     isDanger?    : bool,             // botón confirm en rojo
   *     onConfirm?   : function,
   *     onCancel?    : function
   *   }
   */
  window.confirmAction = function (opts) {
    opts = opts || {};
    return Swal.fire({
      title            : opts.title       || '¿Estás seguro?',
      text             : opts.text        || '',
      icon             : opts.icon        || 'warning',
      showCancelButton : true,
      confirmButtonText: opts.confirmText || 'Sí, continuar',
      cancelButtonText : opts.cancelText  || 'Cancelar',
      reverseButtons   : true,
      focusCancel      : true,
      buttonsStyling   : false,
      customClass: {
        confirmButton: opts.isDanger
          ? 'btn btn-danger waves-effect waves-light'
          : 'btn btn-primary waves-effect waves-light',
        cancelButton : 'btn btn-label-secondary waves-effect'
      }
    }).then(function (result) {
      if (result.isConfirmed  && opts.onConfirm) opts.onConfirm(result);
      if (result.isDismissed  && opts.onCancel)  opts.onCancel(result);
    });
  };

  /**
   * confirmDelete(formId, name?)
   *   Pide confirmación y hace submit del formulario si acepta.
   *   formId : id del <form> a enviar
   *   name   : nombre del elemento que se eliminará (para el mensaje)
   */
  window.confirmDelete = function (formId, name) {
    return Swal.fire({
      title            : '¿Eliminar' + (name ? ' "' + name + '"' : ' este elemento') + '?',
      text             : 'Esta acción es permanente y no se puede deshacer.',
      icon             : 'warning',
      showCancelButton : true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText : 'Cancelar',
      reverseButtons   : true,
      focusCancel      : true,
      buttonsStyling   : false,
      customClass: {
        confirmButton: 'btn btn-danger waves-effect waves-light',
        cancelButton : 'btn btn-label-secondary waves-effect'
      }
    }).then(function (result) {
      if (result.isConfirmed) {
        var form = document.getElementById(formId);
        if (form) form.submit();
      }
    });
  };

  /**
   * confirmDeleteUrl(url, name?, method?)
   *   Igual que confirmDelete pero hace fetch en lugar de submit de formulario.
   *   Útil cuando no hay un <form> en la página (ej. botones en tablas AJAX).
   *   method: 'DELETE' por defecto
   */
  window.confirmDeleteUrl = function (url, name, method) {
    return Swal.fire({
      title            : '¿Eliminar' + (name ? ' "' + name + '"' : ' este elemento') + '?',
      text             : 'Esta acción es permanente y no se puede deshacer.',
      icon             : 'warning',
      showCancelButton : true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText : 'Cancelar',
      reverseButtons   : true,
      focusCancel      : true,
      buttonsStyling   : false,
      customClass: {
        confirmButton: 'btn btn-danger waves-effect waves-light',
        cancelButton : 'btn btn-label-secondary waves-effect'
      }
    }).then(function (result) {
      if (!result.isConfirmed) return;
      var token = document.querySelector('meta[name="csrf-token"]');
      fetch(url, {
        method  : method || 'DELETE',
        redirect: 'manual',
        headers: {
          'X-CSRF-TOKEN'    : token ? token.getAttribute('content') : '',
          'Accept'          : 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      }).then(function (r) {
        if (r.type === 'opaqueredirect' || (r.status >= 300 && r.status < 400)) {
          window.location.reload(); return;
        }
        if (r.ok) { window.location.reload(); return; }
        return r.json().catch(function () { return {}; }).then(function (body) {
          var msg = body.message || 'No se pudo completar la operación.';
          if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'No permitido', text: msg, confirmButtonText: 'Entendido',
              buttonsStyling: false, customClass: { confirmButton: 'btn btn-primary waves-effect' } });
          } else {
            window.showToast('error', msg);
          }
        });
      }).catch(function () {
        var msg = 'Error de conexión.';
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonText: 'Entendido',
            buttonsStyling: false, customClass: { confirmButton: 'btn btn-primary waves-effect' } });
        } else {
          window.showToast('error', msg);
        }
      });
    });
  };

  /**
   * promptInput(opts)
   *   Pide un valor de texto al usuario antes de continuar.
   *   opts: {
   *     title?       : string,
   *     text?        : string,
   *     inputLabel?  : string,
   *     placeholder? : string,
   *     confirmText? : string,
   *     cancelText?  : string,
   *     onConfirm?   : function(value)
   *   }
   */
  window.promptInput = function (opts) {
    opts = opts || {};
    return Swal.fire({
      title             : opts.title       || 'Ingresa un valor',
      text              : opts.text        || '',
      input             : 'text',
      inputLabel        : opts.inputLabel  || '',
      inputPlaceholder  : opts.placeholder || '',
      showCancelButton  : true,
      confirmButtonText : opts.confirmText || 'Aceptar',
      cancelButtonText  : opts.cancelText  || 'Cancelar',
      reverseButtons    : true,
      focusCancel       : false,
      buttonsStyling    : false,
      customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light',
        cancelButton : 'btn btn-label-secondary waves-effect'
      },
      inputValidator: function (value) {
        if (!value || !value.trim()) return 'Este campo es obligatorio.';
      }
    }).then(function (result) {
      if (result.isConfirmed && opts.onConfirm) opts.onConfirm(result.value);
    });
  };

}); // end window.load
</script>
