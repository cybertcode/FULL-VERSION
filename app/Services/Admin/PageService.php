<?php

namespace App\Services\Admin;

use App\Enums\PageStatus;
use App\Enums\PageTemplate;
use App\Models\Page;
use App\Services\HtmlSanitizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PageService
{
    public function stats(): array
    {
        return [
            'total' => Page::count(),
            'published' => Page::where('status', PageStatus::Published->value)->count(),
            'draft' => Page::where('status', PageStatus::Draft->value)->count(),
            'trashed' => Page::onlyTrashed()->count(),
        ];
    }

    public function paginate(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $soloDeleted = $request->input('solo_deleted') === '1';

        $query = $soloDeleted
            ? Page::onlyTrashed()
            : Page::withTrashed();

        if ($request->filled('status') && ! $soloDeleted) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('template')) {
            $query->where('template', $request->input('template'));
        }

        if (! $soloDeleted) {
            $query->whereNull('deleted_at');
        }

        if ($search = $request->get('search')) {
            $query->where(fn (Builder $q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
            );
        }

        return $query->with(['parent', 'creator'])
            ->withDepth()
            ->defaultOrder()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Páginas disponibles para elegir como padre — excluye la página actual
     * y sus propios descendientes (no se puede ser padre de uno mismo).
     */
    public function selectableParents(?Page $excluding = null): Collection
    {
        $query = Page::orderBy('title');

        if ($excluding) {
            $descendantIds = $excluding->descendants()->pluck('id')->push($excluding->id);
            $query->whereNotIn('id', $descendantIds);
        }

        return $query->get();
    }

    public function create(array $data): Page
    {
        $data['content'] = $this->sanitizeRichTextFields($data['template'], $data['content'] ?? []);

        $page = new Page($data);

        if (! empty($data['parent_id'])) {
            $parent = Page::findOrFail($data['parent_id']);
            $page->appendToNode($parent)->save();
        } else {
            $page->saveAsRoot();
        }

        return $page;
    }

    public function update(Page $page, array $data): Page
    {
        $newParentId = $data['parent_id'] ?? null;
        unset($data['parent_id']);

        $template = $data['template'] ?? $page->template;
        $data['content'] = $this->sanitizeRichTextFields($template, $data['content'] ?? []);

        $page->fill($data);

        if ($newParentId && $newParentId !== $page->parent_id) {
            $parent = Page::findOrFail($newParentId);
            $page->appendToNode($parent)->save();
        } elseif (! $newParentId && $page->parent_id) {
            $page->saveAsRoot();
        } else {
            $page->save();
        }

        return $page;
    }

    public function delete(Page $page): void
    {
        $page->delete();
    }

    public function restore(Page $page): void
    {
        $page->restore();
    }

    public function forceDelete(Page $page): void
    {
        $page->forceDelete();
    }

    /**
     * Limpia con HtmlSanitizer únicamente los campos declarados como
     * "richtext" por la plantilla — el resto del contenido (texto plano,
     * URLs, rutas de imagen) se guarda tal cual, ya validado por el FormRequest.
     *
     * @param  array<string, string|null>  $content
     * @return array<string, string|null>
     */
    private function sanitizeRichTextFields(PageTemplate|string $template, array $content): array
    {
        $template = $template instanceof PageTemplate ? $template : PageTemplate::from($template);

        $richTextKeys = collect($template->fields())
            ->where('type', 'richtext')
            ->pluck('key');

        foreach ($richTextKeys as $key) {
            if (array_key_exists($key, $content)) {
                $content[$key] = HtmlSanitizer::clean($content[$key]);
            }
        }

        return $content;
    }
}
