@include('errors.layout', [
  'code'              => 500,
  'title'             => 'Error interno del servidor',
  'message'           => 'Algo salió mal de nuestro lado. Nuestro equipo ya fue notificado. Por favor intenta nuevamente en unos minutos.',
  'illustration'      => 'page-misc-error.png',
  'illustrationWidth' => 225,
])
