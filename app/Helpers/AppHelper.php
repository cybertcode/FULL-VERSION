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
