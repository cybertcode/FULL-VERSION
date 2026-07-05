<?php

use UniSharp\LaravelFilemanager\Handlers\ConfigHandler;

return [

    // LFM gestiona sus propias rutas bajo /admin/archivos/fm
    'use_package_routes' => true,

    // Middleware: auth + permiso granular files.viewAny
    'middlewares' => ['web', 'auth:sanctum', 'can:files.viewAny'],

    'url_prefix' => 'admin/archivos/fm',

    /*
    |--------------------------------------------------------------------------
    | Carpetas compartidas y privadas
    |--------------------------------------------------------------------------
    */
    'allow_private_folder' => true,
    'private_folder_name' => ConfigHandler::class,
    'allow_shared_folder' => true,
    'shared_folder_name' => 'compartido',

    /*
    |--------------------------------------------------------------------------
    | Categorías de carpetas
    |--------------------------------------------------------------------------
    */
    'folder_categories' => [
        'file' => [
            'folder_name' => 'documentos',
            'startup_view' => 'list',
            'max_size' => 102400, // 100 MB en KB
            'thumb' => true,
            'thumb_width' => 80,
            'thumb_height' => 80,
            'valid_mime' => [
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/zip',
                'application/x-zip-compressed',
                'text/plain',
                'text/csv',
            ],
        ],
        'image' => [
            'folder_name' => 'imagenes',
            'startup_view' => 'grid',
            'max_size' => 20480, // 20 MB en KB
            'thumb' => true,
            'thumb_width' => 80,
            'thumb_height' => 80,
            'valid_mime' => [
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'image/svg+xml',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Paginación
    |--------------------------------------------------------------------------
    */
    'paginator' => [
        'perPage' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Almacenamiento
    |--------------------------------------------------------------------------
    */
    'disk' => 'public',
    'temporary_url_duration' => 30,
    's3_acls_disabled' => false,

    /*
    |--------------------------------------------------------------------------
    | Validación de archivos
    |--------------------------------------------------------------------------
    */
    'rename_file' => false,
    'rename_duplicates' => true,
    'alphanumeric_filename' => false,
    'alphanumeric_directory' => false,
    'convert_to_alphanumeric' => false,
    'should_validate_size' => true,
    'should_validate_mime' => true,
    'over_write_on_duplicate' => false,

    // Bloquear ejecutables
    'disallowed_mimetypes' => ['text/x-php', 'text/html', 'application/x-httpd-php'],
    'disallowed_extensions' => ['php', 'html', 'htm', 'exe', 'bat', 'sh', 'js'],

    'item_columns' => ['name', 'url', 'time', 'icon', 'is_file', 'is_image', 'thumb_url'],
    'is_reverse_view' => false,

    /*
    |--------------------------------------------------------------------------
    | Optimización de imágenes
    |--------------------------------------------------------------------------
    */
    'optimize_uploaded_images' => [
        'enabled' => true,
        'format' => null,
        'quality' => 85,
        'max_width' => 2000,
        'max_height' => 2000,
        'progressive' => true,
        'keep_original_when_larger' => true,
        'mimetypes' => [
            'image/jpeg', 'image/pjpeg', 'image/png',
            'image/webp', 'image/avif', 'image/bmp',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Miniaturas
    |--------------------------------------------------------------------------
    */
    'should_create_thumbnails' => true,
    'thumb_folder_name' => 'thumbs',
    'raster_mimetypes' => [
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/webp',
    ],
    'thumb_img_width' => 200,
    'thumb_img_height' => 200,

    /*
    |--------------------------------------------------------------------------
    | Tipos de archivo conocidos
    |--------------------------------------------------------------------------
    */
    'file_type_array' => [
        'pdf' => 'Adobe Acrobat',
        'doc' => 'Microsoft Word',
        'docx' => 'Microsoft Word',
        'xls' => 'Microsoft Excel',
        'xlsx' => 'Microsoft Excel',
        'csv' => 'CSV',
        'zip' => 'Archivo ZIP',
        'gif' => 'Imagen GIF',
        'jpg' => 'Imagen JPEG',
        'jpeg' => 'Imagen JPEG',
        'png' => 'Imagen PNG',
        'webp' => 'Imagen WebP',
        'svg' => 'Imagen SVG',
        'ppt' => 'Microsoft PowerPoint',
        'pptx' => 'Microsoft PowerPoint',
        'txt' => 'Texto plano',
    ],

    /*
    |--------------------------------------------------------------------------
    | PHP ini overrides
    |--------------------------------------------------------------------------
    */
    'php_ini_overrides' => [
        'memory_limit' => '256M',
    ],

    'intervention_driver' => 'gd',
];
