@include('errors.layout', [
  'code'              => 503,
  'title'             => 'Sitio en mantenimiento',
  'message'           => $message ?? 'El sistema se encuentra temporalmente fuera de servicio. Estamos trabajando para mejorar tu experiencia.',
  'illustration'      => 'page-misc-under-maintenance.png',
  'illustrationWidth' => 300,
])
