<?php

namespace App\Http\Requests\Admin\Page;

use App\Enums\PageStatus;
use App\Enums\PageTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'template' => ['required', new Enum(PageTemplate::class)],
            'status' => ['required', new Enum(PageStatus::class)],
            'parent_id' => ['nullable', 'integer', Rule::exists('pages', 'id')],
            'content_by_template' => ['nullable', 'array'],
            'content_by_template.*.*' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:150'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_og_image' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'título',
            'template' => 'plantilla',
            'status' => 'estado',
            'parent_id' => 'página padre',
        ];
    }

    /**
     * Datos listos para el Service: solo el contenido de la plantilla elegida,
     * sin el ruido de los campos ocultos de las demás plantillas del formulario.
     */
    public function dataForPage(): array
    {
        $validated = $this->validated();
        $template = $validated['template'];

        $data = collect($validated)->except('content_by_template')->all();
        $data['content'] = $validated['content_by_template'][$template] ?? [];

        return $data;
    }
}
