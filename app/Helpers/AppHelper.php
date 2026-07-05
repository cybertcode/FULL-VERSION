<?php

use App\Enums\MenuLocation;
use App\Enums\UserStatus;
use App\Models\Menu;
use App\Models\MenuLocationAssignment;
use App\Services\Admin\SettingService;
use Carbon\Carbon;

if (! function_exists('formatDate')) {
    function formatDate(mixed $date, ?string $format = null): string
    {
        if (! $date) {
            return '—';
        }
        $format = $format ?? setting('date_format', 'd/m/Y');

        return Carbon::parse($date)->format($format);
    }
}

if (! function_exists('formatDateTime')) {
    function formatDateTime(mixed $date): string
    {
        if (! $date) {
            return '—';
        }
        $format = setting('date_format', 'd/m/Y').' H:i';

        return Carbon::parse($date)->format($format);
    }
}

if (! function_exists('statusBadge')) {
    function statusBadge(UserStatus $status): string
    {
        return '<span class="badge '.$status->badgeClass().'">'.$status->label().'</span>';
    }
}

if (! function_exists('moneyFormat')) {
    function moneyFormat(float $amount): string
    {
        $symbol = setting('currency_symbol', 'S/');
        $decimals = (int) setting('currency_decimals', 2);

        return $symbol.' '.number_format($amount, $decimals);
    }
}

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return app(SettingService::class)->get($key, $default);
    }
}

if (! function_exists('renderMenuAt')) {
    /**
     * Renderiza el menú asignado a una zona (header, footer, sidebar...).
     * Uso típico en un layout de frontend: {!! renderMenuAt(MenuLocation::Header) !!}
     */
    function renderMenuAt(MenuLocation $location, string $ulClass = 'navbar-nav'): string
    {
        $menu = MenuLocationAssignment::where('location', $location->value)->first()?->menu;

        return renderMenuHtml($menu, $ulClass);
    }
}

if (! function_exists('renderMenu')) {
    /**
     * Renderiza un menú directamente por su slug, sin pasar por la asignación de zona.
     * Útil para casos puntuales (ej. un menú secundario embebido en una página específica).
     */
    function renderMenu(string $slug, string $ulClass = 'navbar-nav'): string
    {
        $menu = Menu::where('slug', $slug)->first();

        return renderMenuHtml($menu, $ulClass);
    }
}

if (! function_exists('renderMenuHtml')) {
    function renderMenuHtml(?Menu $menu, string $ulClass): string
    {
        if (! $menu) {
            return '';
        }

        return '<ul class="'.$ulClass.'">'
            .view('frontend.partials.menu-nodes', ['nodes' => $menu->tree()])->render()
            .'</ul>';
    }
}
