@if (session()->has('flash'))
  @php $flash = session('flash'); @endphp
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (typeof Swal === 'undefined') return;

      const type    = @json($flash['type']);
      const message = @json($flash['message']);

      const iconMap  = { success: 'success', error: 'error', warning: 'warning', info: 'info' };
      const colorMap = {
        success: 'var(--bs-success)',
        error  : 'var(--bs-danger)',
        warning: 'var(--bs-warning)',
        info   : 'var(--bs-info)',
      };

      Swal.fire({
        toast            : true,
        position         : 'top-end',
        icon             : iconMap[type] ?? 'info',
        title            : message,
        showConfirmButton: false,
        timer            : 4000,
        timerProgressBar : true,
        iconColor        : colorMap[type] ?? colorMap.info,
      });
    });
  </script>
@endif
