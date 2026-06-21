<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

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
            'site_name'        => ['sometimes', 'required', 'string', 'max:100'],
            'site_description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'site_logo'        => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'site_logo_dark'   => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'site_favicon'     => ['sometimes', 'nullable', 'file', 'mimes:ico,png,svg', 'max:512'],

            // SEO
            'seo_title'       => ['sometimes', 'nullable', 'string', 'max:160'],
            'seo_description' => ['sometimes', 'nullable', 'string', 'max:320'],
            'seo_keywords'    => ['sometimes', 'nullable', 'string', 'max:255'],
            'seo_og_image'    => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'seo_robots'      => ['sometimes', 'nullable', 'string', 'in:index, follow,noindex, nofollow,noindex, follow,index, nofollow'],

            // Company
            'company_name'         => ['sometimes', 'nullable', 'string', 'max:150'],
            'company_ruc'          => ['sometimes', 'nullable', 'string', 'max:20'],
            'company_type'         => ['sometimes', 'nullable', 'string', 'max:50'],
            'company_email'        => ['sometimes', 'nullable', 'email', 'max:150'],
            'company_phone'        => ['sometimes', 'nullable', 'string', 'max:30'],
            'company_address'      => ['sometimes', 'nullable', 'string', 'max:255'],
            'company_website'      => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_facebook'      => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_instagram'     => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_twitter'       => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_linkedin'      => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_youtube'       => ['sometimes', 'nullable', 'url', 'max:255'],
            'social_whatsapp'      => ['sometimes', 'nullable', 'string', 'max:20'],
            'social_tiktok'        => ['sometimes', 'nullable', 'url', 'max:255'],

            // Mail
            'mail_from_name'       => ['sometimes', 'nullable', 'string', 'max:100'],
            'mail_from_address'    => ['sometimes', 'nullable', 'email', 'max:150'],
            'mail_driver'          => ['sometimes', 'nullable', 'string', 'in:smtp,sendmail,mailgun,ses,log'],
            'mail_host'            => ['sometimes', 'nullable', 'string', 'max:255'],
            'mail_port'            => ['sometimes', 'nullable', 'integer', 'min:1', 'max:65535'],
            'mail_encryption'      => ['sometimes', 'nullable', 'string', 'in:tls,ssl,'],
            'mail_username'        => ['sometimes', 'nullable', 'string', 'max:255'],
            'mail_password'        => ['sometimes', 'nullable', 'string', 'max:255'],

            // Regional
            'timezone'             => ['sometimes', 'nullable', 'string', 'timezone'],
            'date_format'          => ['sometimes', 'nullable', 'string', 'max:20'],
            'currency_symbol'      => ['sometimes', 'nullable', 'string', 'max:10'],
            'currency_decimals'    => ['sometimes', 'nullable', 'integer', 'min:0', 'max:4'],
            'default_language'     => ['sometimes', 'nullable', 'string', 'in:en,es'],
            'pagination_per_page'  => ['sometimes', 'nullable', 'integer', 'min:5', 'max:200'],

            // Seguridad
            'session_lifetime'     => ['sometimes', 'nullable', 'integer', 'min:5', 'max:10080'],
            'login_max_attempts'   => ['sometimes', 'nullable', 'integer', 'min:1', 'max:20'],
            'login_lockout_minutes'=> ['sometimes', 'nullable', 'integer', 'min:1', 'max:1440'],
            'force_2fa'            => ['sometimes', 'nullable', 'boolean'],
            'captcha_enabled'      => ['sometimes', 'nullable', 'boolean'],
            'captcha_site_key'     => ['sometimes', 'nullable', 'string', 'max:255'],
            'captcha_secret_key'   => ['sometimes', 'nullable', 'string', 'max:255'],
            'allowed_ips_admin'    => ['sometimes', 'nullable', 'string', 'max:2000'],

            // Mantenimiento
            'maintenance_mode'     => ['sometimes', 'nullable', 'boolean'],
            'maintenance_message'  => ['sometimes', 'nullable', 'string', 'max:500'],
            'maintenance_ips'      => ['sometimes', 'nullable', 'string', 'max:2000'],

            // Integraciones
            'google_analytics_id'  => ['sometimes', 'nullable', 'string', 'max:50'],
            'google_maps_key'      => ['sometimes', 'nullable', 'string', 'max:255'],
            'meta_pixel_id'        => ['sometimes', 'nullable', 'string', 'max:50'],
            'recaptcha_site_key'   => ['sometimes', 'nullable', 'string', 'max:255'],
            'recaptcha_secret_key' => ['sometimes', 'nullable', 'string', 'max:255'],
            'gtm_id'               => ['sometimes', 'nullable', 'string', 'max:50'],

            // Apariencia
            'primary_color'        => ['sometimes', 'nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'terms_url'            => ['sometimes', 'nullable', 'url', 'max:255'],
            'privacy_url'          => ['sometimes', 'nullable', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'primary_color.regex' => 'El color primario debe ser un código hexadecimal válido (ej: #7367F0).',
            'timezone.timezone'   => 'La zona horaria seleccionada no es válida.',
            'mail_port.integer'   => 'El puerto debe ser un número.',
        ];
    }
}
