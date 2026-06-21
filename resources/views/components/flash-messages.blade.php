{{-- Toast via Bootstrap Toast (flash del servidor) --}}
@if(session()->has('flash'))
@php $flash = session('flash'); @endphp
<script>
document.addEventListener('DOMContentLoaded', function () {
  window.showToast(@json($flash['type'] ?? 'info'), @json($flash['message'] ?? ''));
});
</script>
@endif

{{--
  Los errores de validación se muestran INLINE debajo de cada campo con @error + .invalid-feedback.
  No usar SweetAlert para errores de validación — es mala UX porque oculta cuál campo falló.
  Si se necesita resaltar el primer error, agregar @section('admin-page-script') en la vista específica.
--}}
