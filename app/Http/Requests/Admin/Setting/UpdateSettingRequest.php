<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Branding
            'site_name' => ['sometimes', 'required', 'string', 'max:100'],
            'site_description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'site_logo' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'site_logo_dark' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'site_favicon' => ['sometimes', 'nullable', 'file', 'mimes:ico,png,svg', 'max:512'],

            // SEO
            'seo_title' => ['sometimes', 'nullable', 'string', 'max:160'],
            'seo_description' => ['sometimes', 'nullable', 'string', 'max:320'],
            'seo_keywords' => ['sometimes', 'nullable', 'string', 'max:255'],
            'seo_og_image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'seo_robots' => ['sometimes', 'nullable', 'string', Rule::in(['index, follow', 'noindex, nofollow', 'noindex, follow', 'index, nofollow'])],

            // Company
            'company_name' => ['sometimes', 'nullable', 'string', 'max:150'],
            'company_ruc' => ['sometimes', 'nullable', 'string', 'max:20'],
            'company_type' => ['sometimes', 'nullable', 'string', 'max:50'],
            'company_email' => ['sometimes', 'nullable', 'email', 'max:150'],
            'company_phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'company_address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'company_website' => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_facebook' => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_instagram' => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_twitter' => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_linkedin' => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_youtube' => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_whatsapp' => ['sometimes', 'nullable', 'string', 'max:20'],
            'social_tiktok' => ['sometimes', 'nullable', 'url', 'max:255'],

            // Mail
            'mail_from_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'mail_from_address' => ['sometimes', 'nullable', 'email', 'max:150'],
            'mail_driver' => ['sometimes', 'nullable', 'string', 'in:smtp,sendmail,mailgun,ses,log'],
            'mail_host' => ['sometimes', 'nullable', 'string', 'max:255'],
            'mail_port' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:65535'],
            'mail_encryption' => ['sometimes', 'nullable', 'string', 'in:tls,ssl,'],
            'mail_username' => ['sometimes', 'nullable', 'string', 'max:255'],
            'mail_password' => ['sometimes', 'nullable', 'string', 'max:255'],

            // Regional
            'timezone' => ['sometimes', 'nullable', 'string', 'timezone'],
            'date_format' => ['sometimes', 'nullable', 'string', 'max:20'],
            'currency_symbol' => ['sometimes', 'nullable', 'string', 'max:10'],
            'currency_decimals' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:4'],
            'default_language' => ['sometimes', 'nullable', 'string', 'in:en,es'],
            'pagination_per_page' => ['sometimes', 'nullable', 'integer', 'min:5', 'max:200'],

            // Seguridad
            'session_lifetime' => ['sometimes', 'nullable', 'integer', 'min:5', 'max:10080'],
            'login_max_attempts' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:20'],
            'login_lockout_minutes' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:1440'],
            'force_2fa' => ['sometimes', 'nullable', 'boolean'],
            'captcha_enabled' => ['sometimes', 'nullable', 'boolean'],
            'captcha_site_key' => ['sometimes', 'nullable', 'string', 'max:255'],
            'captcha_secret_key' => ['sometimes', 'nullable', 'string', 'max:255'],
            'allowed_ips_admin' => ['sometimes', 'nullable', 'string', 'max:2000'],

            // Mantenimiento
            'maintenance_mode' => ['sometimes', 'nullable', 'boolean'],
            'maintenance_message' => ['sometimes', 'nullable', 'string', 'max:500'],
            'maintenance_ips' => ['sometimes', 'nullable', 'string', 'max:2000'],

            // Integraciones
            'google_analytics_id' => ['sometimes', 'nullable', 'string', 'max:50'],
            'google_maps_key' => ['sometimes', 'nullable', 'string', 'max:255'],
            'meta_pixel_id' => ['sometimes', 'nullable', 'string', 'max:50'],
            'recaptcha_site_key' => ['sometimes', 'nullable', 'string', 'max:255'],
            'recaptcha_secret_key' => ['sometimes', 'nullable', 'string', 'max:255'],
            'gtm_id' => ['sometimes', 'nullable', 'string', 'max:50'],

            // Apariencia
            'primary_color' => ['sometimes', 'nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'terms_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'privacy_url' => ['sometimes', 'nullable', 'url', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            // Branding
            'site_name' => 'nombre del sistema',
            'site_description' => 'descripción corta',
            'site_logo' => 'logo principal',
            'site_logo_dark' => 'logo oscuro',
            'site_favicon' => 'favicon',
            // SEO
            'seo_title' => 'meta título',
            'seo_description' => 'meta descripción',
            'seo_keywords' => 'palabras clave',
            'seo_og_image' => 'imagen Open Graph',
            'seo_robots' => 'indexación robots',
            // Company
            'company_name' => 'razón social',
            'company_ruc' => 'RUC',
            'company_type' => 'tipo de empresa',
            'company_email' => 'email de contacto',
            'company_phone' => 'teléfono',
            'company_address' => 'dirección',
            'company_website' => 'sitio web',
            'social_facebook' => 'Facebook',
            'social_instagram' => 'Instagram',
            'social_twitter' => 'Twitter/X',
            'social_linkedin' => 'LinkedIn',
            'social_youtube' => 'YouTube',
            'social_tiktok' => 'TikTok',
            'social_whatsapp' => 'WhatsApp',
            // Mail
            'mail_from_name' => 'nombre del remitente',
            'mail_from_address' => 'email remitente',
            'mail_driver' => 'driver de correo',
            'mail_host' => 'host SMTP',
            'mail_port' => 'puerto SMTP',
            'mail_encryption' => 'cifrado',
            'mail_username' => 'usuario SMTP',
            'mail_password' => 'contraseña SMTP',
            // Regional
            'timezone' => 'zona horaria',
            'date_format' => 'formato de fecha',
            'currency_symbol' => 'símbolo de moneda',
            'currency_decimals' => 'decimales',
            'default_language' => 'idioma por defecto',
            'pagination_per_page' => 'registros por página',
            // Seguridad
            'session_lifetime' => 'duración de sesión',
            'login_max_attempts' => 'intentos máximos de login',
            'login_lockout_minutes' => 'tiempo de bloqueo',
            'captcha_site_key' => 'reCAPTCHA site key',
            'captcha_secret_key' => 'reCAPTCHA secret key',
            'allowed_ips_admin' => 'IPs permitidas',
            // Mantenimiento
            'maintenance_message' => 'mensaje de mantenimiento',
            'maintenance_ips' => 'IPs con acceso en mantenimiento',
            // Integraciones
            'google_analytics_id' => 'Google Analytics ID',
            'google_maps_key' => 'Google Maps API key',
            'meta_pixel_id' => 'Meta Pixel ID',
            'recaptcha_site_key' => 'reCAPTCHA site key',
            'recaptcha_secret_key' => 'reCAPTCHA secret key',
            'gtm_id' => 'Google Tag Manager ID',
            // Apariencia
            'primary_color' => 'color primario',
            'terms_url' => 'URL de términos',
            'privacy_url' => 'URL de privacidad',
        ];
    }

    public function messages(): array
    {
        return [
            'primary_color.regex' => 'El color primario debe ser un código hexadecimal válido (ej: #7367F0).',
            'timezone.timezone' => 'La zona horaria seleccionada no es válida.',
            'mail_port.integer' => 'El puerto SMTP debe ser un número.',
            'mail_port.min' => 'El puerto SMTP debe ser mayor a 0.',
            'mail_port.max' => 'El puerto SMTP no puede superar 65535.',
            'seo_robots.in' => 'El valor de indexación robots no es válido.',
            'mail_driver.in' => 'El driver de correo seleccionado no es válido.',
            'mail_encryption.in' => 'El cifrado seleccionado no es válido.',
            'default_language.in' => 'El idioma seleccionado no está disponible.',
            'company_email.email' => 'El email de contacto no tiene un formato válido.',
            'mail_from_address.email' => 'El email remitente no tiene un formato válido.',
            'company_website.url' => 'El sitio web debe ser una URL válida (incluir https://).',
            'terms_url.url' => 'La URL de términos debe ser válida (incluir https://).',
            'privacy_url.url' => 'La URL de privacidad debe ser válida (incluir https://).',
        ];
    }
}
