<?php

namespace App\Providers;

use App\Listeners\LogLoginAttempt;
use App\Listeners\RememberTwoFactorDeviceOnLogin;
use App\Services\Admin\SettingService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Events\ValidTwoFactorAuthenticationCodeProvided;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingService::class);
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Event::listen(Login::class, [LogLoginAttempt::class, 'handleLogin']);
        Event::listen(Failed::class, [LogLoginAttempt::class, 'handleFailed']);
        Event::listen(ValidTwoFactorAuthenticationCodeProvided::class, RememberTwoFactorDeviceOnLogin::class);

        if (Schema::hasTable('settings')) {
            $settings = app(SettingService::class);

            View::share('appSettings', $settings->all());

            // Nombre de la app — usado en asuntos y pies de correos
            if ($siteName = $settings->get('site_name')) {
                Config::set('app.name', $siteName);
            }

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

            // Paginación global — usa la misma clave que BaseAdminController
            if ($perPage = $settings->get('pagination_per_page')) {
                Config::set('app-settings.pagination.default', (int) $perPage);
            }

            // Duración de sesión (minutos)
            if ($lifetime = $settings->get('session_lifetime')) {
                Config::set('session.lifetime', (int) $lifetime);
            }

            // Carbon locale — sincronizar con el idioma del sistema
            if ($lang = $settings->get('default_language')) {
                Carbon::setLocale($lang);
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

            // Login social — Google
            if ($googleId = $settings->get('social_google_client_id')) {
                Config::set('services.google.client_id', $googleId);
            }
            if ($googleSecret = $settings->get('social_google_client_secret')) {
                Config::set('services.google.client_secret', $googleSecret);
            }
            Config::set('services.google.redirect', url('/auth/social/google/callback'));

            // Login social — GitHub
            if ($githubId = $settings->get('social_github_client_id')) {
                Config::set('services.github.client_id', $githubId);
            }
            if ($githubSecret = $settings->get('social_github_client_secret')) {
                Config::set('services.github.client_secret', $githubSecret);
            }
            Config::set('services.github.redirect', url('/auth/social/github/callback'));

            // Login social — Facebook
            if ($facebookId = $settings->get('social_facebook_client_id')) {
                Config::set('services.facebook.client_id', $facebookId);
            }
            if ($facebookSecret = $settings->get('social_facebook_client_secret')) {
                Config::set('services.facebook.client_secret', $facebookSecret);
            }
            Config::set('services.facebook.redirect', url('/auth/social/facebook/callback'));
        }

        Vite::useStyleTagAttributes(function (?string $src, string $_url, ?array $_chunk, ?array $_manifest) {
            if ($src !== null) {
                return [
                    'class' => preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?core)-?.*/i", $src)
                        ? 'template-customizer-core-css'
                        : (preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?theme)-?.*/i", $src)
                            ? 'template-customizer-theme-css'
                            : ''),
                ];
            }

            return [];
        });
    }
}
