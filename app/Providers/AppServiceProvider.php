<?php

namespace App\Providers;

use App\Services\Admin\SettingService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingService::class);
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if (Schema::hasTable('settings')) {
            $settings = app(SettingService::class);

            View::share('appSettings', $settings->all());

            // Timezone
            if ($tz = $settings->get('timezone')) {
                Config::set('app.timezone', $tz);
                date_default_timezone_set($tz);
            }

            // Mail remitente
            if ($name = $settings->get('mail_from_name')) {
                Config::set('mail.from.name', $name);
            }
            if ($address = $settings->get('mail_from_address')) {
                Config::set('mail.from.address', $address);
            }

            // SMTP completo desde BD (solo si están configurados)
            if ($host = $settings->get('mail_host')) {
                Config::set('mail.mailers.smtp.host', $host);
            }
            if ($port = $settings->get('mail_port')) {
                Config::set('mail.mailers.smtp.port', (int) $port);
            }
            if ($enc = $settings->get('mail_encryption')) {
                Config::set('mail.mailers.smtp.encryption', $enc ?: null);
            }
            if ($user = $settings->get('mail_username')) {
                Config::set('mail.mailers.smtp.username', $user);
            }
            if ($pass = $settings->get('mail_password')) {
                Config::set('mail.mailers.smtp.password', $pass);
            }

            // Idioma por defecto
            if ($lang = $settings->get('default_language')) {
                Config::set('app.locale', $lang);
                app()->setLocale($lang);
            }

            // Paginación global
            if ($perPage = $settings->get('pagination_per_page')) {
                Config::set('app.pagination_per_page', (int) $perPage);
            }

            // Duración de sesión (minutos)
            if ($lifetime = $settings->get('session_lifetime')) {
                Config::set('session.lifetime', (int) $lifetime);
            }

            // Carbon locale — sincronizar con el idioma del sistema
            if ($lang = $settings->get('default_language')) {
                \Carbon\Carbon::setLocale($lang);
            }

            // Color primario — settings siempre tiene precedencia si no hay cookie activa del customizer
            // El cookie se borra en SettingController::update() cuando se guarda el grupo 'appearance'
            if ($color = $settings->get('primary_color')) {
                Config::set('custom.custom.primaryColor', $color);
            }

            // reCAPTCHA keys (disponibles via config())
            if ($siteKey = $settings->get('recaptcha_site_key')) {
                Config::set('services.recaptcha.site_key', $siteKey);
            }
            if ($secretKey = $settings->get('recaptcha_secret_key')) {
                Config::set('services.recaptcha.secret_key', $secretKey);
            }

            // Google Maps
            if ($mapsKey = $settings->get('google_maps_key')) {
                Config::set('services.google_maps.key', $mapsKey);
            }
        }

        Vite::useStyleTagAttributes(function (?string $src, string $_url, ?array $_chunk, ?array $_manifest) {
            if ($src !== null) {
                return [
                    'class' => preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?core)-?.*/i", $src)
                        ? 'template-customizer-core-css'
                        : (preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?theme)-?.*/i", $src)
                            ? 'template-customizer-theme-css'
                            : '')
                ];
            }
            return [];
        });
    }
}
