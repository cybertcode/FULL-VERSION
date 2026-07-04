<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [

            // ── Branding ──────────────────────────────────────────────
            ['key' => 'site_name',        'group' => 'branding', 'value' => 'Mi Sistema'],
            ['key' => 'site_description', 'group' => 'branding', 'value' => 'Sistema de gestión empresarial'],
            ['key' => 'site_logo',        'group' => 'branding', 'value' => null],
            ['key' => 'site_logo_dark',   'group' => 'branding', 'value' => null],
            ['key' => 'site_favicon',     'group' => 'branding', 'value' => null],

            // ── SEO ───────────────────────────────────────────────────
            ['key' => 'seo_title',       'group' => 'seo', 'value' => 'Mi Sistema — Gestión Empresarial'],
            ['key' => 'seo_description', 'group' => 'seo', 'value' => 'Plataforma de gestión empresarial moderna y profesional.'],
            ['key' => 'seo_keywords',    'group' => 'seo', 'value' => 'gestión, empresarial, sistema, administración'],
            ['key' => 'seo_og_image',    'group' => 'seo', 'value' => null],
            ['key' => 'seo_robots',      'group' => 'seo', 'value' => 'index, follow'],

            // ── Empresa / Contacto ─────────────────────────────────────
            ['key' => 'company_name',    'group' => 'company', 'value' => 'Mi Empresa S.A.C.'],
            ['key' => 'company_ruc',     'group' => 'company', 'value' => '20000000000'],
            ['key' => 'company_type',    'group' => 'company', 'value' => 'SAC'],
            ['key' => 'company_email',   'group' => 'company', 'value' => 'contacto@miempresa.com'],
            ['key' => 'company_phone',   'group' => 'company', 'value' => '+51 999 999 999'],
            ['key' => 'company_address', 'group' => 'company', 'value' => 'Av. Principal 123, Lima, Perú'],
            ['key' => 'company_website', 'group' => 'company', 'value' => 'https://miempresa.com'],

            // ── Redes sociales ─────────────────────────────────────────
            ['key' => 'social_facebook',  'group' => 'company', 'value' => null],
            ['key' => 'social_instagram', 'group' => 'company', 'value' => null],
            ['key' => 'social_twitter',   'group' => 'company', 'value' => null],
            ['key' => 'social_linkedin',  'group' => 'company', 'value' => null],
            ['key' => 'social_youtube',   'group' => 'company', 'value' => null],
            ['key' => 'social_whatsapp',  'group' => 'company', 'value' => null],
            ['key' => 'social_tiktok',    'group' => 'company', 'value' => null],

            // ── Email del sistema ──────────────────────────────────────
            ['key' => 'mail_from_name',    'group' => 'mail', 'value' => 'Mi Sistema'],
            ['key' => 'mail_from_address', 'group' => 'mail', 'value' => 'noreply@miempresa.com'],
            ['key' => 'mail_driver',       'group' => 'mail', 'value' => 'smtp'],
            ['key' => 'mail_host',         'group' => 'mail', 'value' => 'smtp.mailtrap.io'],
            ['key' => 'mail_port',         'group' => 'mail', 'value' => '587'],
            ['key' => 'mail_encryption',   'group' => 'mail', 'value' => 'tls'],
            ['key' => 'mail_username',     'group' => 'mail', 'value' => null],
            ['key' => 'mail_password',     'group' => 'mail', 'value' => null],

            // ── Regional ───────────────────────────────────────────────
            ['key' => 'timezone',           'group' => 'regional', 'value' => 'America/Lima'],
            ['key' => 'date_format',        'group' => 'regional', 'value' => 'd/m/Y'],
            ['key' => 'currency_symbol',    'group' => 'regional', 'value' => 'S/'],
            ['key' => 'currency_decimals',  'group' => 'regional', 'value' => '2'],
            ['key' => 'default_language',   'group' => 'regional', 'value' => 'es'],
            ['key' => 'pagination_per_page', 'group' => 'regional', 'value' => '15'],

            // ── Seguridad ──────────────────────────────────────────────
            ['key' => 'session_lifetime',      'group' => 'security', 'value' => '120'],
            ['key' => 'login_max_attempts',    'group' => 'security', 'value' => '5'],
            ['key' => 'login_lockout_minutes', 'group' => 'security', 'value' => '15'],
            ['key' => 'force_2fa',             'group' => 'security', 'value' => '0'],
            ['key' => 'captcha_enabled',       'group' => 'security', 'value' => '0'],
            ['key' => 'captcha_site_key',      'group' => 'security', 'value' => null],
            ['key' => 'captcha_secret_key',    'group' => 'security', 'value' => null],
            ['key' => 'allowed_ips_admin',     'group' => 'security', 'value' => null],

            // ── Mantenimiento ──────────────────────────────────────────
            ['key' => 'maintenance_mode',    'group' => 'maintenance', 'value' => '0'],
            ['key' => 'maintenance_message', 'group' => 'maintenance', 'value' => 'El sistema se encuentra en mantenimiento. Vuelve pronto.'],
            ['key' => 'maintenance_ips',     'group' => 'maintenance', 'value' => null],

            // ── Integraciones ──────────────────────────────────────────
            ['key' => 'google_analytics_id',  'group' => 'integrations', 'value' => null],
            ['key' => 'google_maps_key',       'group' => 'integrations', 'value' => null],
            ['key' => 'meta_pixel_id',         'group' => 'integrations', 'value' => null],
            ['key' => 'recaptcha_site_key',    'group' => 'integrations', 'value' => null],
            ['key' => 'recaptcha_secret_key',  'group' => 'integrations', 'value' => null],
            ['key' => 'gtm_id',                'group' => 'integrations', 'value' => null],

            // ── Apariencia ─────────────────────────────────────────────
            ['key' => 'primary_color', 'group' => 'appearance', 'value' => '#7367F0'],
            ['key' => 'terms_url',     'group' => 'appearance', 'value' => null],
            ['key' => 'privacy_url',   'group' => 'appearance', 'value' => null],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['group' => $setting['group'], 'value' => $setting['value']]
            );
        }
    }
}
