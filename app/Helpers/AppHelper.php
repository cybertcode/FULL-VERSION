<?php

if (! function_exists('formatDate')) {
    function formatDate(mixed $date, ?string $format = null): string
    {
        if (! $date) return '—';
        $format = $format ?? setting('date_format', 'd/m/Y');
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (! function_exists('formatDateTime')) {
    function formatDateTime(mixed $date): string
    {
        if (! $date) return '—';
        $format = setting('date_format', 'd/m/Y') . ' H:i';
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (! function_exists('statusBadge')) {
    function statusBadge(\App\Enums\UserStatus $status): string
    {
        return '<span class="badge ' . $status->badgeClass() . '">' . $status->label() . '</span>';
    }
}

if (! function_exists('moneyFormat')) {
    function moneyFormat(float $amount): string
    {
        $symbol   = setting('currency_symbol', 'S/');
        $decimals = (int) setting('currency_decimals', 2);
        return $symbol . ' ' . number_format($amount, $decimals);
    }
}

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return app(\App\Services\Admin\SettingService::class)->get($key, $default);
    }
}
