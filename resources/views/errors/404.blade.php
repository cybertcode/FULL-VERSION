@include('errors.layout', [
  'code'              => 404,
  'title'             => 'Página no encontrada',
  'message'           => 'La dirección que ingresaste no existe o fue movida a otra ubicación.',
  'illustration'      => 'page-misc-error.png',
  'illustrationWidth' => 225,
])
