# CLAUDE.md

Guía completa para Claude Code al trabajar en este repositorio.
Actualizado: 2026-06-20 (v2 — estructura escalable completa)

---

## Qué es este proyecto

**Boilerplate/Starter Kit** basado en la plantilla comercial **Vuexy v3.0.0** (Pixinvent) sobre **Laravel 12**.
Objetivo: tener una base reutilizable para cualquier proyecto futuro — con o sin frontend público — sin empezar de cero.

- Multi-page server-rendered (Blade + Bootstrap 5). No hay SPA.
- Vite para bundling de assets.
- Auth completa via Jetstream (Livewire stack).
- Roles y permisos via Spatie Permission v6.

---

## Credenciales de desarrollo

| Campo      | Valor                    |
| ---------- | ------------------------ |
| URL        | <http://full-version.test> |
| DB         | MySQL `full-version` / root / (sin password) |
| Admin email | admin@admin.com         |
| Admin pass  | Admin123                |
| Admin rol   | Super-Admin             |

---

## Comandos

### Backend

```bash
php artisan serve                   # servidor Laravel
php artisan migrate                 # correr migraciones
php artisan migrate:fresh --seed    # reset completo de BD
php artisan tinker                  # REPL
php artisan optimize:clear          # limpiar todos los caches
```

### Frontend

```bash
# SIEMPRE usar yarn — ejecutable: C:\laragon\bin\nodejs\node-v18\yarn.cmd
yarn dev     # Vite dev server con HMR
yarn build   # build de producción
```

### Todo junto

```bash
composer run dev   # servidor + queue + logs + vite en paralelo
```

### Agregar idioma nuevo

```bash
php artisan lang:add {locale}   # publica archivos en lang/{locale}/
# Luego agregar el locale a:
# 1. TemplateCustomizer.LANGUAGES en resources/assets/vendor/js/template-customizer.js
# 2. LanguageController — array de locales permitidos
# 3. LocaleMiddleware — array de locales permitidos
# 4. navbar-partial.blade.php — item en el dropdown
# 5. yarn build
```

### Calidad de código

```bash
./vendor/bin/pint                   # PHP code style (Laravel Pint)
php artisan test                    # PHPUnit
php artisan permission:cache-reset  # limpiar cache de permisos Spatie
```

---

## REGLA DE ORO — Separación Vuexy vs Nuestro código

**Nunca mezclar** código propio con los archivos de la plantilla Vuexy.

### Zonas Vuexy — NO TOCAR

| Ruta | Motivo |
| ---- | ------ |
| `app/Http/Controllers/[apps,authentications,cards,charts,dashboard,extended_ui,...]` | Controllers demo Vuexy |
| `app/Helpers/Helpers.php` | Helper de layout Vuexy |
| `app/Actions/Fortify/` | Jetstream |
| `app/Actions/Jetstream/` | Jetstream |
| `resources/assets/` | JS/SCSS/vendor de Vuexy |
| `resources/menu/` | Menús JSON de Vuexy |
| `resources/views/layouts/` | Layouts Blade de Vuexy |
| `resources/views/content/` | Vistas demo de Vuexy |
| `config/custom.php` | Config de tema Vuexy |
| `config/variables.php` | Metadata de Vuexy |
| `vite.config.js` | Build pipeline de Vuexy |

### Zonas nuestras — AQUÍ trabajamos

| Ruta | Propósito |
| ---- | --------- |
| `app/Http/Controllers/Admin/` | Controllers del panel |
| `app/Http/Controllers/Frontend/` | Controllers del sitio público |
| `app/Http/Requests/Admin/` | Form Requests del panel |
| `app/Http/Requests/Frontend/` | Form Requests públicos |
| `app/Http/Middleware/` | Middleware custom |
| `app/Actions/Admin/` | Acciones de negocio del panel |
| `app/Actions/Frontend/` | Acciones de negocio públicas |
| `app/Services/Admin/` | Servicios del panel |
| `app/Services/Frontend/` | Servicios públicos |
| `app/Repositories/Admin/` | Repositorios del panel |
| `app/Repositories/Frontend/` | Repositorios públicos |
| `app/Models/` | Eloquent models |
| `app/Enums/` | PHP Enums (Status, RoleType, etc.) |
| `app/Traits/` | Traits reutilizables |
| `resources/views/admin/` | Vistas del panel |
| `resources/views/frontend/` | Vistas del sitio público |
| `resources/js/admin/` | JS custom del panel |
| `routes/admin.php` | Rutas del panel (`/admin/*`) |
| `routes/frontend.php` | Rutas públicas |
| `database/seeders/` | Seeders propios |
| `database/migrations/` | Migraciones propias |

---

## Arquitectura del proyecto

### Capas de código (de arriba hacia abajo)

```
Controller → Request (validación) → Action o Service → Model
                                  ↘ Repository (queries complejas)
```

- **Controllers** — solo reciben request, llaman Action/Service, retornan respuesta
- **Requests** — validación de formularios (uno por operación)
- **Actions** — una operación de negocio por clase (`CreateUser`, `UpdateRole`) — patrón Jetstream
- **Services** — lógica agrupada por dominio cuando hay múltiples operaciones relacionadas
- **Repositories** — queries Eloquent complejas o reutilizables
- **Models** — Eloquent puro, sin lógica de negocio

### Convención de nombres

| Elemento | Ejemplo |
| -------- | ------- |
| Controller | `UserController.php` |
| Request | `StoreUserRequest.php`, `UpdateUserRequest.php` |
| Action | `CreateUser.php`, `DeleteUser.php` |
| Service | `UserService.php` |
| Repository | `UserRepository.php` |
| Enum | `UserStatus.php`, `RoleType.php` |
| Model | `User.php` |
| Vista | `resources/views/admin/users/index.blade.php` |

---

## Rutas

### Estructura

```
routes/
├── web.php        ← Vuexy demos + grupo auth de Jetstream
├── admin.php      ← panel: prefijo /admin, name admin.*, middleware auth
└── frontend.php   ← sitio público: sin prefijo, sin auth
```

### Cómo agregar rutas del panel

En `routes/admin.php`:

```php
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('users', UserController::class);
        // Route names: admin.users.index, admin.users.create, etc.
    });
```

### Cómo agregar rutas públicas

En `routes/frontend.php`:

```php
Route::get('/', [HomeController::class, 'index'])->name('home');
```

---

## Auth y permisos

### Jetstream

- Stack: **Livewire**
- Login real en `/login` (ruta de Jetstream)
- Vistas Jetstream swapeadas al estilo Vuexy
- Teams habilitados — cada usuario tiene un personal team

### Spatie Permission

- Roles: `Super-Admin`, `admin`, `user`
- `Super-Admin` bypasses todos los checks via `Gate::before` en `AppServiceProvider`
- Permisos con formato `modulo.accion`: `users.view`, `users.create`, `users.edit`, `users.delete`
- El modelo `User` tiene el trait `HasRoles`

### En Blade

```blade
@role('Super-Admin')
  <!-- solo super admin -->
@endrole

@can('users.create')
  <!-- tiene permiso -->
@endcan
```

### En Controllers

```php
$this->authorize('users.edit');
// o
abort_unless(auth()->user()->can('users.edit'), 403);
```

---

## Seeders

Siempre correr en este orden (ya definido en `DatabaseSeeder`):

1. `RolesAndPermissionsSeeder` — crea roles y permisos
2. `AdminUserSeeder` — crea admin con `withPersonalTeam()`

Al agregar nuevos seeders, registrarlos en `DatabaseSeeder::run()`.

---

## UI Globals — Toast y SweetAlert2

**Archivo:** `resources/views/components/ui-globals.blade.php`
**Inyectado en:** `layouts/commonMaster.blade.php` → disponible en **todas** las páginas (admin, demos Vuexy, frontend).

SweetAlert2 debe estar cargado antes de usarlo (el panel admin lo carga en `admin/layouts/master`).

### API disponible en JS (window.*)

```js
// Bootstrap Toast — no requiere SweetAlert2
showToast('success', 'Guardado correctamente');
showToast('error',   'Ocurrió un error');
showToast('info',    'Ten en cuenta esto');
showToast('warning', 'Revisa los datos');
showToast('success', 'Texto', { title: 'Título custom', delay: 6000 });

// SweetAlert2 — solo disponible después del evento 'load'
showAlert('Título', 'Mensaje');
showAlert('Título', 'Mensaje', 'success'); // icon: success|error|warning|info|question

showAlertHtml('Título', '<b>HTML</b> permitido', 'info');

confirmAction({
  title      : '¿Publicar?',
  text       : 'El contenido será visible para todos.',
  confirmText: 'Sí, publicar',
  cancelText : 'Cancelar',
  isDanger   : false,           // true → botón rojo
  onConfirm  : () => { /* acción */ },
  onCancel   : () => { /* cancelado */ }
});

confirmDelete('form-id', 'Nombre del elemento');   // hace submit del <form>

confirmDeleteUrl('/admin/users/5', 'Juan López');   // fetch DELETE + reload

promptInput({
  title      : '¿Razón del rechazo?',
  inputLabel : 'Motivo',
  placeholder: 'Escribe aquí...',
  onConfirm  : (value) => { console.log(value); }
});
```

### Flash desde PHP (Blade)

```php
// En el controller:
return redirect()->back()->with('flash', [
    'type'    => 'success',   // success | error | info | warning
    'message' => 'Guardado correctamente',
]);
```

## Gestor de Archivos (LFM) y componente x-lfm-input

**UniSharp Laravel FileManager v2** — biblioteca central de medios en `/admin/archivos` (iframe sobre `/admin/archivos/fm`).

- Config en `config/lfm.php`: disco `public`, carpetas `imagenes/` y `documentos/`, carpeta privada por usuario + `compartido`, ejecutables bloqueados.
- Protegido por permiso granular `files.viewAny` (middleware en config, NO por rol).
- Vistas publicadas (restyled Vuexy) en `resources/views/vendor/laravel-filemanager/`; traducciones en `resources/lang/vendor/laravel-filemanager/es/lfm.php`.
- ⚠️ En Blade, NUNCA escribir `<x-nombre>` dentro de comentarios JS/HTML — Blade lo compila como componente real (ParseError).

### Selector reutilizable `<x-lfm-input>`

`resources/views/components/lfm-input.blade.php` — abre el LFM como popup y guarda la **URL pública** del archivo en un input de texto (readonly). Incluye vista previa para imágenes, botón quitar, soporte `old()`, `@error`, y oculta el botón Explorar si el usuario no tiene `files.viewAny`. El JS va inline con `@once` (no depende de secciones del layout).

```blade
<x-lfm-input name="foto" type="image" label="Imagen destacada" :value="$post->foto" required
  help="Elige una imagen de la biblioteca o sube una nueva." />
<x-lfm-input name="documento" type="file" label="Adjunto PDF" />
```

- `type`: `image` | `file`. El backend debe validar con `['url' o 'string', 'max:2048']` — recibe una URL, no un archivo.
- Uso recomendado: contenido editorial/reutilizable (banners, posts, adjuntos). Para uploads estructurales ligados a un registro (avatares, logos de settings) seguir usando `<input type="file">` + ImageService.

## Vistas del panel (admin)

Las vistas admin extienden `admin/layouts/master` — **NO** `layouts/layoutMaster` directamente.
SweetAlert2 ya está cargado en el master. Bootstrap Toast está disponible globalmente vía `x-ui-globals`.

```blade
@extends('admin/layouts/master')

@section('title', 'Usuarios')

@section('admin-content')
  <!-- contenido aquí -->
@endsection

@section('admin-vendor-style')
  {{-- CSS extra (datatables, select2, etc.) --}}
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss'])
@endsection

@section('admin-vendor-script')
  {{-- JS extra del vendor --}}
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.js'])
@endsection

@section('admin-page-script')
  {{-- JS propio de la página --}}
  <script> /* ... */ </script>
@endsection
```

IMPORTANTE: Usar `@section`/`@endsection` (NO `@push`) para `admin-vendor-style`, `admin-vendor-script` y `admin-page-script` — el master usa `@yield` que requiere `@section`.

Bootstrap Toast y SweetAlert2 ya están disponibles — no incluirlos manualmente en cada vista.

Estructura de carpetas para un módulo:

```
resources/views/admin/
└── users/
    ├── index.blade.php
    ├── create.blade.php
    ├── edit.blade.php
    └── show.blade.php
```

---

## Arquitectura Vuexy (referencia)

### Sistema de layouts

`layoutMaster.blade.php` despacha a uno de 4 layouts según `config/custom.php`:

- `contentNavbarLayout` — vertical (default)
- `horizontalLayout` — horizontal
- `blankLayout` — sin nav (auth pages)
- `layoutFront` — páginas públicas

Para cambiar tema o layout editar `config/custom.php` — nunca los Blade directamente.

### Menús

Definidos en JSON:

- `resources/menu/verticalMenu.json` — sidebar
- `resources/menu/horizontalMenu.json` — top nav

El `slug` de cada item debe coincidir exactamente con el `name` de la ruta para el active-state.

**Filtro por permisos**: cada item acepta una clave opcional `"permission": "users.viewAny"` — el item solo se muestra si el usuario tiene ese permiso (Super-Admin pasa siempre). El filtrado ocurre en `MenuServiceProvider` (View composer, render time). Items sin `permission` son visibles para cualquier autenticado. Padres sin URL se ocultan si todos sus hijos quedan ocultos; los `menuHeader` huérfanos también.

### Asset pipeline Vite

- JS por página: `resources/assets/js/app-*.js`
- Vendor: `resources/assets/vendor/libs/**/*.js`
- SCSS: `resources/assets/vendor/scss/**/!(_)*.scss`
- Cargar en vista: `@vite(['resources/assets/js/app-nombre.js'])`

---

## Providers

| Provider | Responsabilidad |
| -------- | --------------- |
| `AppServiceProvider` | Bindings del contenedor + Vite config Vuexy |
| `AuthServiceProvider` | `Gate::before` Super-Admin + registro de Policies |
| `MenuServiceProvider` | Comparte menús JSON a todas las vistas (Vuexy) |
| `FortifyServiceProvider` | Configuración de Fortify/auth (Jetstream) |
| `JetstreamServiceProvider` | Configuración de Jetstream |

Para agregar una Policy nueva, registrarla en `AuthServiceProvider::$policies`.

---

## Middleware disponibles

```php
// En rutas o controllers:
->middleware('role:Super-Admin')
->middleware('role:admin|Super-Admin')
->middleware('permission:users.edit')
->middleware('role_or_permission:admin|users.view')
```

---

## Exceptions custom

| Clase | Código | Cuándo usarla |
| ----- | ------ | ------------- |
| `BusinessException` | 422 | Regla de negocio violada — se muestra al usuario |
| `UnauthorizedException` | 403 | Acción de dominio no permitida |

```php
// En un Service o Action:
throw new \App\Exceptions\BusinessException('No puedes eliminar un usuario activo.');
throw new \App\Exceptions\UnauthorizedException('Solo puedes editar tus propios registros.');
```

El handler en `bootstrap/app.php` las convierte automáticamente:
- Web: `back()->withErrors()` o vista `errors.403`
- API (`expectsJson`): respuesta JSON con el mensaje

---

## Enums disponibles

| Enum | Valores | Métodos |
| ---- | ------- | ------- |
| `App\Enums\UserStatus` | `Active`, `Inactive`, `Banned` | `label()`, `badgeClass()` |
| `App\Enums\RoleType` | `SuperAdmin`, `Admin`, `User` | `label()` |

```php
// En modelo — cast:
protected function casts(): array {
    return ['status' => \App\Enums\UserStatus::class];
}

// En Blade:
{!! statusBadge($user->status) !!}
```

---

## Traits disponibles

| Trait | Propósito | Requisitos |
| ----- | --------- | ---------- |
| `App\Traits\HasActive` | Scopes `active()`, `inactive()`, métodos `activate()`, `deactivate()` | Columna `status` tipo `UserStatus` |
| `App\Traits\HasFilters` | Scope `filter($request)` con búsqueda y ordenamiento | Array `$searchable` en el modelo |
| `App\Traits\HasAudit` | Auto-rellena `created_by` / `updated_by` con el user autenticado | Columnas `created_by`, `updated_by` en la tabla |

```php
// Ejemplo de modelo que usa los traits:
class Post extends Model {
    use HasActive, HasFilters, HasAudit;

    protected array $searchable = ['title', 'content'];
    protected string $defaultSort = 'created_at';

    protected function casts(): array {
        return ['status' => UserStatus::class];
    }
}

// En controller:
Post::filter($request)->active()->paginate(15);
```

---

## Helpers globales (AppHelper.php)

| Función | Descripción | Ejemplo |
| -------- | ----------- | ------- |
| `formatDate($date)` | Fecha en `d/m/Y` | `formatDate($user->created_at)` |
| `formatDateTime($date)` | Fecha+hora en `d/m/Y H:i` | `formatDateTime($model->updated_at)` |
| `statusBadge($status)` | Badge Bootstrap HTML para `UserStatus` | `{!! statusBadge($user->status) !!}` |
| `moneyFormat($amount)` | Formato monetario con prefijo | `moneyFormat(1500.5)` → `S/ 1,500.50` |

---

## Vistas de error

Laravel usa automáticamente `resources/views/errors/{code}.blade.php`.
Creadas con el estilo Vuexy:

| Vista | HTTP | Descripción |
| ----- | ---- | ----------- |
| `errors/404.blade.php` | 404 | Página no encontrada |
| `errors/403.blade.php` | 403 | Sin autorización |
| `errors/500.blade.php` | 500 | Error del servidor |

---

## Tests

- `phpunit.xml` usa SQLite en memoria (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`)
- Nunca corren contra MySQL real
- Tests de Jetstream ya incluidos en `tests/Feature/`
- Nuevos tests van en `tests/Feature/Admin/` o `tests/Unit/`

---

## Bugs corregidos

| Bug | Archivo | Fix |
| --- | ------- | --- |
| `currentTeam->id` null crash | `navbar-partial.blade.php:496` | `Auth::user()?->currentTeam ?` + seeder usa `withPersonalTeam()` |
| Controllers Vuexy faltantes | `layouts/NavbarFull.php`, `NavbarFullSidebar.php` | Creados con sus vistas |

---

## Paquetes instalados

### PHP

| Paquete | Versión | Uso |
| ------- | ------- | --- |
| `laravel/framework` | ^12.0 | Core |
| `laravel/jetstream` | ^5.5 | Auth completa + Teams |
| `laravel/sanctum` | ^4.0 | API tokens |
| `livewire/livewire` | ^3.6.4 | Componentes reactivos |
| `spatie/laravel-permission` | ^6.25 | Roles y permisos |
| `pixinvent/vuexy-laravel-bootstrap-jetstream` | ^3.0 | Swap vistas Jetstream → Vuexy |

### JS destacados

| Paquete | Uso |
| ------- | --- |
| `bootstrap` 5.3.5 | UI framework |
| `apexcharts` 4.2.0 | Gráficas |
| `datatables.net-bs5` 2.1.8 | Tablas avanzadas |
| `@fullcalendar/*` 6.1.17 | Calendario |
| `flatpickr` 4.6.13 | Date picker |
| `sweetalert2` 11.14.5 | Alertas/modales |
| `quill` 2.0.3 | Editor de texto rico |
| `leaflet` 1.9.4 | Mapas |
| `select2` 4.0.13 | Selects con búsqueda |
| `dropzone` 5.9.3 | Upload drag & drop |

---

## Módulos del boilerplate (completados)

- [x] CRUD Usuarios (`Admin/UserController`) — avatar, perfil, exports, bulk actions
- [x] CRUD Roles y Permisos (`Admin/RoleController`)
- [x] Dashboard con stats, gráfica ApexCharts (registros/mes) y actividad reciente (`DashboardService`)
- [x] Auditoría — visor de Activity Log en `/admin/auditoria` con filtros, modal detalle y export CSV (`ActivityLogService`)
- [x] Settings del sistema (tabla clave-valor, 9 grupos) — autorizado con `settings.view/edit/testMail/runArtisan`
- [x] Notificaciones — canal database, dropdown navbar real, página `/admin/notificaciones`, `App\Notifications\SystemNotification` genérica (title, message, icon, color, url)
- [x] Gestor de Archivos LFM + `<x-lfm-input>`
- [x] Log Viewer en `/admin/logs` (permiso `logs.view`, solo Super-Admin por defecto)
- [x] Correos con marca — header con logo de settings, botones #1340A0, español vía laravel-lang
- [x] Landing frontend `/` — consume branding de settings (logo, nombre, empresa, redes)
- [x] Menú filtrado por permisos (clave `"permission"` en JSON)
- [x] Tests Feature de todos los módulos (168 en verde)

## Gotchas importantes

- **Lang path**: la app usa `resources/lang` (NO `lang/` raíz — Vuexy legacy). Paquetes que publiquen en `lang/` deben moverse a `resources/lang` o las traducciones JSON no cargan.
- **Blade en comentarios**: nunca escribir `<x-componente>` en comentarios JS/HTML dentro de Blade — se compila como componente real (ParseError).
- **SQLite en tests**: no usar `DATE_FORMAT` ni SQL específico de MySQL en queries de services — agrupar en PHP (Collection `countBy`).
- **User usa SoftDeletes**: en tests, `assertSoftDeleted` en vez de `assertNull($user->fresh())`.
- **`name` del usuario**: es nullable y se construye desde el perfil (`Perfil::buildName`) — no validarlo como requerido.
