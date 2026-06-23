@include('errors.layout', [
  'code'              => 403,
  'title'             => 'Sin autorización',
  'message'           => 'No tienes permisos suficientes para acceder a este recurso. Si crees que es un error, contacta al administrador.',
  'illustration'      => 'page-misc-you-are-not-authorized.png',
  'illustrationWidth' => 180,
])
