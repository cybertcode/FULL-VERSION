<p align="center"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="Laravel Logo"></p>

<h1 align="center">Boilerplate Laravel 12 + Vuexy</h1>

<p align="center">Panel administrativo listo para producción, pensado como base para cualquier proyecto nuevo.</p>

---

## Qué incluye

- **Laravel 12** + plantilla comercial **Vuexy v3** (Bootstrap 5, multi-page, sin SPA).
- **Auth completa** vía Jetstream (2FA, sesiones activas, login social Google/GitHub/Facebook).
- **Roles y permisos** granulares con Spatie Permission (`modulo.accion`), CRUD de permisos desde la UI.
- **Guard `customer`** aislado para usuarios finales del frontend (`/cuenta/*`), separado del panel admin.
- **CMS de Páginas y Menús** (`/admin/paginas`, `/admin/menus`) — plantillas con campos dinámicos, jerarquía, sanitización de HTML.
- **Auditoría** (activity log con diff legible), **impersonación de usuarios**, **notificaciones** (database + email), **backups automáticos**, **Gestor de Archivos** (LFM) con componente `<x-lfm-input>` reutilizable.
- **API REST** de ejemplo en `/api/v1` (Sanctum, paginación estándar, Policies).
- **Seguridad de fábrica**: security headers, CSP, CORS configurable, rate limiting, 2FA reforzado en login social y API, XSS/HTML sanitizado en contenido editable.
- **Calidad**: PHPUnit (273+ tests), Larastan/PHPStan nivel 6, Laravel Pint, CI en GitHub Actions.

Ver [CLAUDE.md](CLAUDE.md) para la guía técnica completa (arquitectura, convenciones, dónde tocar y dónde no).

## Cómo usar este repositorio como base de un proyecto nuevo

Hay dos formas de arrancar un proyecto nuevo a partir de este boilerplate. Ninguna modifica ni depende de esta carpeta (`full-version`) — quedan totalmente independientes entre sí.

### Opción A — Copia local rápida (Laragon), sin tocar GitHub

Para probar algo ya mismo en `c:\laragon\www\`, sin crear repositorio todavía:

```bash
# Desde c:\laragon\www\ (NO desde dentro de full-version)
cp -r full-version clinica-dental
cd clinica-dental
rm -rf .git                 # importante: si no, seguirá vinculado al historial del boilerplate
```

Luego sigue la sección **Instalación** de abajo. Con Laragon, la carpeta `clinica-dental` queda accesible automáticamente en `http://clinica-dental.test` (auto-vhost) — usa esa URL como `APP_URL` en el `.env`.

**No copies** `vendor/`, `node_modules/`, `.env`, ni `storage/framework/*` (son de esta instancia y pesan mucho) — bórralos después de copiar o excluye la copia con `rsync -a --exclude=vendor --exclude=node_modules --exclude=.env full-version/ clinica-dental/` (Git Bash) si prefieres hacerlo en un solo paso. `composer install` y `yarn install` los regeneran limpios en la instalación.

### Opción B — Repositorio nuevo en GitHub (template)

Este repo está marcado como **GitHub Template**. Para crear un repositorio nuevo e independiente en tu cuenta (útil si vas a desplegar, dar acceso a otros, o simplemente prefieres empezar con git limpio desde ya), reemplaza `clinica-dental` por el nombre real que quieras darle a tu proyecto (debe aparecer igual en ambas líneas):

```bash
gh repo create clinica-dental --template cybertcode/FULL-VERSION --private --clone
cd clinica-dental
```

Qué hace cada parte: `gh repo create clinica-dental` crea el repositorio en tu cuenta de GitHub; `--template cybertcode/FULL-VERSION` copia el código de este boilerplate (sin su historial de commits); `--private` lo crea privado; `--clone` además lo descarga a una carpeta `clinica-dental` en tu máquina, donde sea que ejecutes el comando. El `cd clinica-dental` solo entra a esa carpeta para seguir con la sección **Instalación**.

O desde la web de GitHub: botón **"Use this template"** → *"Create a new repository"*.

Esto no toca tu copia local de `full-version` en Laragon — es un repo aparte que luego puedes clonar donde quieras (incluyendo dentro de `c:\laragon\www\` con otro nombre, para tener también auto-vhost).

## Requisitos

- PHP 8.2+
- MySQL 8+ (o compatible)
- Composer 2
- Node.js 18 + **Yarn** (el proyecto usa Yarn, no npm)

## Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Edita `.env` con tus datos reales, como mínimo:

| Variable | Qué poner |
| -------- | --------- |
| `APP_NAME` | Nombre del proyecto nuevo |
| `APP_URL` | URL local — con Laragon es `http://<nombre-de-la-carpeta>.test` |
| `DB_DATABASE` | Nombre de una base de datos **nueva y vacía** (créala en phpMyAdmin/HeidiSQL antes del siguiente paso — no reutilices la del boilerplate ni la de otro proyecto) |
| `DB_USERNAME` / `DB_PASSWORD` | Credenciales de tu MySQL (Laragon local: `root` sin password) |

El resto de variables en `.env.example` están comentadas por sección (mail, colas, S3, Redis, backups, etc.) — todas opcionales para desarrollo local, revisar antes de ir a producción.

```bash
php artisan migrate:fresh --seed
yarn install
yarn build          # o `yarn dev` para desarrollo con hot-reload
php artisan serve
```

Todo junto (servidor + queue worker + logs + Vite en paralelo):

```bash
composer run dev
```

## Credenciales por defecto (seeder)

| Campo | Valor |
| ----- | ----- |
| URL panel | `/admin` |
| Email | `admin@admin.com` |
| Password | `Admin123` |
| Rol | `Super-Admin` |

**Cambiar esta contraseña antes de desplegar a producción.**

## Comandos útiles

```bash
php artisan test                                    # suite completa de tests
./vendor/bin/phpstan analyse --memory-limit=512M     # análisis estático (nivel 6)
./vendor/bin/pint                                    # formateo de código (Laravel Pint)
php artisan backup:run                               # backup manual (BD + archivos)
php artisan schedule:list                            # ver tareas programadas
```

## Antes de desplegar a producción

- `APP_ENV=production` y `APP_DEBUG=false` (evita filtrar stack traces).
- `LOG_LEVEL=error` (evita ruido de SQL/payloads en logs).
- `SESSION_SECURE_COOKIE=true` si el hosting fuerza HTTPS.
- Configurar `CORS_ALLOWED_ORIGINS` si algún frontend externo consume `/api/v1`.
- Revisar `/admin/configuracion` → Integraciones para reCAPTCHA y login social (se configuran ahí, no en `.env`).
- Cambiar la contraseña del usuario `admin@admin.com`.

## Licencia

Este boilerplate usa Laravel (MIT) y la plantilla comercial Vuexy (licencia de Pixinvent — no redistribuir el código de la plantilla fuera de proyectos con licencia válida).
