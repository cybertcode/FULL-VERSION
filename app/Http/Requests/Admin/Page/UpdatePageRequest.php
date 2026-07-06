<?php

namespace App\Http\Requests\Admin\Page;

use App\Enums\PageStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $page = $this->route('page');

        return [
            'title' => ['required', 'string', 'max:150'],
            'status' => ['required', new Enum(PageStatus::class)],
            'parent_id' => [
                'nullable', 'integer',
                Rule::exists('pages', 'id'),
                Rule::notIn([$page?->id]),
            ],
            'seo_title' => ['nullable', 'string', 'max:150'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_og_image' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'título',
            'status' => 'estado',
            'parent_id' => 'página padre',
        ];
    }
}
