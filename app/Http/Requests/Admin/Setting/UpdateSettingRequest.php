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
            'company_name'    => ['sometimes', 'nullable', 'string', 'max:150'],
            'company_email'   => ['sometimes', 'nullable', 'email', 'max:150'],
            'company_phone'   => ['sometimes', 'nullable', 'string', 'max:30'],
            'company_address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'company_website' => ['sometimes', 'nullable', 'url', 'max:255'],

            // Mail
            'mail_from_name'    => ['sometimes', 'nullable', 'string', 'max:100'],
            'mail_from_address' => ['sometimes', 'nullable', 'email', 'max:150'],

            // Regional
            'timezone'          => ['sometimes', 'nullable', 'string', 'timezone'],
            'date_format'       => ['sometimes', 'nullable', 'string', 'max:20'],
            'currency_symbol'   => ['sometimes', 'nullable', 'string', 'max:10'],
            'currency_decimals' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:4'],
            'default_language'  => ['sometimes', 'nullable', 'string', 'in:en,es'],
        ];
    }
}
