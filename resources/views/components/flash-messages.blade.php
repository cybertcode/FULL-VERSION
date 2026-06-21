{{-- Bootstrap Toast via flash del servidor --}}
@if(session()->has('flash'))
@php $flash = session('flash'); @endphp
<script>
document.addEventListener('DOMContentLoaded', function () {
  window.showToast(@json($flash['type'] ?? 'info'), @json($flash['message'] ?? ''));
});
</script>
@endif

{{-- Errores de validación → SweetAlert2 modal --}}
@if($errors->any() && !session()->has('flash'))
<script>
window.addEventListener('load', function () {
  var first = @json($errors->first());
  var count = {{ $errors->count() }};
  Swal.fire({
    title          : 'Error de validación',
    html           : first + (count > 1 ? '<br><small class="text-muted">y ' + (count - 1) + ' error' + (count > 2 ? 'es' : '') + ' más</small>' : ''),
    icon           : 'error',
    confirmButtonText: 'Entendido',
    buttonsStyling : false,
    customClass    : { confirmButton: 'btn btn-primary waves-effect waves-light' }
  });
});
</script>
@endif
