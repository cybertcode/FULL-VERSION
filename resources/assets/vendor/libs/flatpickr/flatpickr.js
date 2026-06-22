import flatpickr from 'flatpickr/dist/flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es.js';

// Locale español globalmente para todo el proyecto
flatpickr.localize(Spanish);

try {
  window.flatpickr = flatpickr;
} catch (e) {}

export { flatpickr };
