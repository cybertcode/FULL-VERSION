// Service worker mínimo — su único propósito es cumplir el requisito técnico
// de instalabilidad (Add to Home Screen / Install app) de Chrome/Edge/Android.
// No cachea nada: todo se sirve siempre desde red, así el panel y el frontend
// nunca muestran contenido obsoleto.
self.addEventListener('install', () => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', () => {
  // Sin estrategia de cache: dejar pasar todas las peticiones a la red.
});
