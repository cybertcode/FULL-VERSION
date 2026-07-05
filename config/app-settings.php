<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración general del proyecto
    |--------------------------------------------------------------------------
    */

    'name' => env('APP_NAME', 'Mi Aplicación'),
    'version' => '1.0.0',
    'timezone' => env('APP_TIMEZONE', 'America/Lima'),

    /*
    |--------------------------------------------------------------------------
    | Paginación
    |--------------------------------------------------------------------------
    */

    'pagination' => [
        'default' => 15,
        'options' => [10, 15, 25, 50, 100],
    ],

    /*
    |--------------------------------------------------------------------------
    | Formato de fechas
    |--------------------------------------------------------------------------
    */

    'date_format' => 'd/m/Y',
    'datetime_format' => 'd/m/Y H:i',

    /*
    |--------------------------------------------------------------------------
    | Moneda
    |--------------------------------------------------------------------------
    */

    'currency' => [
        'symbol' => env('APP_CURRENCY_SYMBOL', 'S/'),
        'decimals' => 2,
        'thousands_sep' => ',',
        'decimal_sep' => '.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Uploads
    |--------------------------------------------------------------------------
    */

    'uploads' => [
        'max_size_kb' => 5120,   // 5 MB
        'allowed_images' => ['jpg', 'jpeg', 'png', 'webp'],
        'allowed_documents' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
        'disk' => env('UPLOAD_DISK', 'public'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Módulos de permisos (label + ícono para la matriz de Roles/Permisos)
    |--------------------------------------------------------------------------
    |
    | Cada módulo de permisos ("users.*", "roles.*", etc.) puede declarar aquí
    | su label visible y su ícono Tabler. Un módulo nuevo (ej. "facturas") que
    | no esté listado usa el fallback automático: ucfirst($module) + ícono
    | genérico — no es obligatorio editar esto para que el panel funcione,
    | solo para que se vea con label/ícono propio en vez del genérico.
    |
    */

    'permission_modules' => [
        'users' => ['label' => 'Usuarios', 'icon' => 'tabler-users'],
        'roles' => ['label' => 'Roles', 'icon' => 'tabler-shield-lock'],
        'permissions' => ['label' => 'Permisos', 'icon' => 'tabler-shield-check'],
        'settings' => ['label' => 'Configuración', 'icon' => 'tabler-settings'],
        'activitylog' => ['label' => 'Registro de Actividad', 'icon' => 'tabler-list-check'],
        'dashboard' => ['label' => 'Dashboard', 'icon' => 'tabler-layout-dashboard'],
        'files' => ['label' => 'Archivos', 'icon' => 'tabler-folder'],
        'logs' => ['label' => 'Logs del servidor', 'icon' => 'tabler-file-text'],
        'login-attempts' => ['label' => 'Intentos de acceso', 'icon' => 'tabler-shield-exclamation'],
    ],

];
