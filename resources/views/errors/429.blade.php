@include('errors.layout', [
  'code'              => 429,
  'title'             => 'Demasiadas solicitudes',
  'message'           => 'Has realizado demasiadas solicitudes en poco tiempo. Espera un momento antes de intentar nuevamente.',
  'illustration'      => 'page-misc-error.png',
  'illustrationWidth' => 200,
])
