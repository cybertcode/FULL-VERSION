<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [

            // ── Branding ─────────────────────────────────────────────
            ['key' => 'site_name',        'group' => 'branding', 'value' => 'Mi Sistema'],
            ['key' => 'site_description', 'group' => 'branding', 'value' => 'Sistema de gestión empresarial'],
            ['key' => 'site_logo',        'group' => 'branding', 'value' => null],
            ['key' => 'site_logo_dark',   'group' => 'branding', 'value' => null],
            ['key' => 'site_favicon',     'group' => 'branding', 'value' => null],

            // ── SEO ──────────────────────────────────────────────────
            ['key' => 'seo_title',       'group' => 'seo', 'value' => 'Mi Sistema — Gestión Empresarial'],
            ['key' => 'seo_description', 'group' => 'seo', 'value' => 'Plataforma de gestión empresarial moderna y profesional.'],
            ['key' => 'seo_keywords',    'group' => 'seo', 'value' => 'gestión, empresarial, sistema, administración'],
            ['key' => 'seo_og_image',    'group' => 'seo', 'value' => null],
            ['key' => 'seo_robots',      'group' => 'seo', 'value' => 'index, follow'],

            // ── Empresa / Contacto ────────────────────────────────────
            ['key' => 'company_name',    'group' => 'company', 'value' => 'Mi Empresa S.A.C.'],
            ['key' => 'company_email',   'group' => 'company', 'value' => 'contacto@miempresa.com'],
            ['key' => 'company_phone',   'group' => 'company', 'value' => '+51 999 999 999'],
            ['key' => 'company_address', 'group' => 'company', 'value' => 'Av. Principal 123, Lima, Perú'],
            ['key' => 'company_website', 'group' => 'company', 'value' => 'https://miempresa.com'],

            // ── Email del sistema ─────────────────────────────────────
            ['key' => 'mail_from_name',    'group' => 'mail', 'value' => 'Mi Sistema'],
            ['key' => 'mail_from_address', 'group' => 'mail', 'value' => 'noreply@miempresa.com'],

            // ── Regional ──────────────────────────────────────────────
            ['key' => 'timezone',          'group' => 'regional', 'value' => 'America/Lima'],
            ['key' => 'date_format',       'group' => 'regional', 'value' => 'd/m/Y'],
            ['key' => 'currency_symbol',   'group' => 'regional', 'value' => 'S/'],
            ['key' => 'currency_decimals', 'group' => 'regional', 'value' => '2'],
            ['key' => 'default_language',  'group' => 'regional', 'value' => 'es'],

        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['group' => $setting['group'], 'value' => $setting['value']]
            );
        }
    }
}
