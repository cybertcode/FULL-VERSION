<?php

if (! function_exists('formatDate')) {
    function formatDate(?string $date, string $format = 'd/m/Y'): string
    {
        return $date ? \Carbon\Carbon::parse($date)->format($format) : '—';
    }
}

if (! function_exists('formatDateTime')) {
    function formatDateTime(?string $date): string
    {
        return $date ? \Carbon\Carbon::parse($date)->format('d/m/Y H:i') : '—';
    }
}

if (! function_exists('statusBadge')) {
    /**
     * Genera HTML de badge Bootstrap para un estado.
     * Uso en Blade: {!! statusBadge($user->status) !!}
     */
    function statusBadge(\App\Enums\UserStatus $status): string
    {
        return '<span class="badge ' . $status->badgeClass() . '">' . $status->label() . '</span>';
    }
}

if (! function_exists('moneyFormat')) {
    function moneyFormat(float $amount, string $currency = 'S/'): string
    {
        return $currency . ' ' . number_format($amount, 2);
    }
}

if (! function_exists('setting')) {
    /**
     * Obtiene un valor de configuración del sistema desde la BD (con cache).
     * Disponible en toda la app y en Blade.
     *
     * Uso: setting('site_name')
     *      setting('mail_from_address', 'noreply@app.com')
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return app(\App\Services\Admin\SettingService::class)->get($key, $default);
    }
}
