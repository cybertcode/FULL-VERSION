# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this project is

**Vuexy** is a commercial Laravel + Bootstrap 5 admin template by Pixinvent (v3.0.0). It is a multi-page server-rendered application using Laravel 12 (Blade views) for the backend and Vite for frontend asset bundling. There is no SPA framework — pages are traditional Blade templates with per-page vanilla JavaScript files.

## Commands

### Frontend

```bash
# Always use the npm-global yarn, NOT Laragon's bundled yarn (which uses Node 18.8)
C:\Users\MKEVYN\AppData\Roaming\npm\yarn.cmd install
C:\Users\MKEVYN\AppData\Roaming\npm\yarn.cmd dev       # Vite dev server with HMR
C:\Users\MKEVYN\AppData\Roaming\npm\yarn.cmd build     # Production build
```

Or use npm scripts directly (npm uses the correct system Node v22):
```bash
npm run dev
npm run build
```

### Backend (PHP / Laravel)

```bash
php artisan serve                    # Start Laravel dev server
php artisan migrate                  # Run migrations
php artisan migrate:fresh --seed     # Reset DB and seed
php artisan key:generate             # Generate app key (first-time setup)
php artisan tinker                   # REPL
```

### Run all dev processes together (server + queue + logs + vite):
```bash
composer run dev
```

### PHP linting & testing

```bash
./vendor/bin/pint                    # Laravel Pint (PHP code style fixer)
php artisan test                     # PHPUnit tests
php artisan test --filter TestName   # Run a single test
./vendor/bin/phpunit --filter TestName
```

### JS/CSS linting

```bash
npx eslint resources/assets/js/     # Lint JS files
npx stylelint resources/assets/vendor/scss/**/*.scss
npx prettier --write resources/      # Format all frontend files
```

## Architecture

### Dual asset pipeline

Vite (`vite.config.js`) bundles a large number of entry points in parallel:
- **Per-page JS**: `resources/assets/js/app-*.js` — one file per page/feature
- **Vendor libs JS**: `resources/assets/vendor/libs/**/*.js` — third-party libraries
- **Core SCSS**: `resources/assets/vendor/scss/**/!(_)*.scss` — theme core styles
- **Lib SCSS/CSS**: `resources/assets/vendor/libs/**/*.scss|css` — per-lib styles
- **Fonts**: `resources/assets/vendor/fonts/`
- **App entry**: `resources/js/app.js` and `resources/css/app.css`

The `@` alias resolves to `resources/`.

### Blade layout system

Every page view extends one of two master wrappers via `layouts/layoutMaster.blade.php`, which dispatches to:
- `contentNavbarLayout` — default vertical layout
- `horizontalLayout` — horizontal nav variant
- `blankLayout` — no nav/menu (e.g., auth pages)
- `layoutFront` — public front pages

Layout sections live in `resources/views/layouts/sections/` (navbar, menu, footer, scripts, styles). Scripts are split into `scriptsIncludes.blade.php` (CDN/vendor) and `scripts.blade.php` (page-specific JS via `@vite`).

### Controller → View → JS convention

Each page follows a strict 1:1:1 pattern:
- **Controller**: `app/Http/Controllers/{namespace}/{PageName}.php` returns a Blade view
- **View**: `resources/views/content/{namespace}/{page-name}.blade.php`
- **JS**: `resources/assets/js/app-{page-name}.js` (loaded via `@vite` in the view)

Controller namespaces mirror `routes/web.php` groupings: `apps`, `dashboard`, `front_pages`, `layouts`, `laravel_example`, etc.

### Configuration system (`config/custom.php`)

Theme layout, skin, RTL mode, and customizer options are set in `config/custom.php`. The `Helpers::appClasses()` method reads this config and returns an array of CSS classes and settings consumed by all Blade layouts. To change the default layout or theme, edit `config/custom.php` — **not** the Blade files directly.

`config/variables.php` holds static template metadata (name, version, URLs).

### Menu definition

Navigation menus are defined as JSON, not PHP:
- `resources/menu/verticalMenu.json` — sidebar (vertical layout)
- `resources/menu/horizontalMenu.json` — top nav (horizontal layout)

Each menu item has `url`, `name`, `slug`, `icon`, and optionally `submenu`. The `slug` must match the route name for active-state detection.

### SCSS theme structure (`resources/assets/vendor/scss/`)

```
_bootstrap.scss          # Bootstrap import
_bootstrap-extended/     # Bootstrap component overrides
_components/             # Custom components (avatars, cards, badges, etc.)
_custom-variables/       # SCSS variable overrides (colors, spacing, etc.)
pages/                   # Per-page SCSS (e.g., pages/_authentication.scss)
core.scss                # Main entry: imports all of the above
```

### `app/Helpers/Helpers.php`

Autoloaded globally (see `composer.json` `autoload.files`). Key methods:
- `appClasses()` — merges `config/custom.php` with page-level `$pageConfigs` overrides, returns layout data
- `getMenuAttributes()` — generates HTML attributes for semi-dark mode
- `updatePageConfig()` — allows per-view overrides of layout settings via `$pageConfigs` variable

### Environment

Default DB is SQLite (`database/database.sqlite`). Switch to MySQL by updating `.env` with `DB_CONNECTION=mysql` and the relevant credentials. Node >=18.17 required; use system Node v22 (Laragon's bundled Node 18.8.0 is too old for some dependencies).
