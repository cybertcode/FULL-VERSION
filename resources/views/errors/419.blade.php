@include('errors.layout', [
  'code'              => 419,
  'title'             => 'Sesión expirada',
  'message'           => 'Tu sesión ha expirado por inactividad. Por favor recarga la página e intenta nuevamente.',
  'illustration'      => 'page-misc-error.png',
  'illustrationWidth' => 200,
  'actions'           => '<div class="d-flex flex-wrap gap-3 justify-content-center mb-10">
    <button onclick="window.location.reload()" class="btn btn-primary">
      <i class="icon-base ti tabler-refresh me-1"></i> Recargar página
    </button>
    <a href="' . url('/') . '" class="btn btn-outline-secondary">
      <i class="icon-base ti tabler-home me-1"></i> Ir al inicio
    </a>
  </div>',
])
