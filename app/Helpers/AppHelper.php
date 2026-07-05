<?php

use App\Enums\UserStatus;
use App\Models\Menu;
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

if (! function_exists('renderMenu')) {
    function renderMenu(string $slug, string $ulClass = 'navbar-nav'): string
    {
        $menu = Menu::where('slug', $slug)->first();

        if (! $menu) {
            return '';
        }

        $tree = $menu->tree();

        return '<ul class="'.$ulClass.'">'
            .view('frontend.partials.menu-nodes', ['nodes' => $tree])->render()
            .'</ul>';
    }
}
