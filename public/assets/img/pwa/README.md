# Íconos PWA — placeholder

`icon-192.png`, `icon-512.png`, `icon-512-maskable.png` fueron generados con
`scripts/generate-pwa-icons.php` (letra "V" sobre `#1340A0`) porque no había
un logo del proyecto en alta resolución (los archivos en
`public/assets/img/branding/` son de 42-140px, insuficientes para íconos PWA).

Para reemplazarlos con el logo real del proyecto:

1. Consigue un PNG cuadrado de al menos 512x512 con fondo sólido (no transparente).
2. Para la versión maskable, deja ~20% de margen de seguridad alrededor del logo
   (el SO puede recortarlo en círculo/squircle).
3. Sobrescribe los 3 archivos con esos nombres exactos, o actualiza las rutas
   en `public/manifest.webmanifest`.
