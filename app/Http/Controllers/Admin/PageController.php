<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PageStatus;
use App\Enums\PageTemplate;
use App\Http\Requests\Admin\Page\StorePageRequest;
use App\Http\Requests\Admin\Page\UpdatePageRequest;
use App\Models\Page;
use App\Services\Admin\PageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends BaseAdminController
{
    public function __construct(
        private readonly PageService $pageService,
    ) {
        parent::__construct();
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Page::class);

        $pages = $this->pageService->paginate($request, $this->perPage);
        $stats = $this->pageService->stats();
        $statuses = PageStatus::cases();
        $templates = PageTemplate::cases();

        return view('admin.pages.index', compact('pages', 'stats', 'statuses', 'templates'));
    }

    public function create(): View
    {
        $this->authorize('create', Page::class);

        return view('admin.pages.create', [
            'templates' => PageTemplate::cases(),
            'statuses' => PageStatus::cases(),
            'parents' => $this->pageService->selectableParents(),
        ]);
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        $this->authorize('create', Page::class);

        $page = $this->pageService->create($request->dataForPage());

        $this->flashSuccess('Página creada correctamente.');

        return redirect()->route('admin.pages.edit', $page);
    }

    public function edit(Page $page): View
    {
        $this->authorize('update', $page);

        return view('admin.pages.edit', [
            'page' => $page,
            'templates' => PageTemplate::cases(),
            'statuses' => PageStatus::cases(),
            'parents' => $this->pageService->selectableParents($page),
        ]);
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $this->authorize('update', $page);

        $this->pageService->update($page, $request->dataForPage());

        $this->flashSuccess('Página actualizada correctamente.');

        return redirect()->route('admin.pages.edit', $page);
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->authorize('delete', $page);

        $this->pageService->delete($page);

        $this->flashSuccess('Página movida a la papelera.');

        return redirect()->route('admin.pages.index');
    }

    public function restore(int $page): RedirectResponse
    {
        $model = Page::onlyTrashed()->findOrFail($page);
        $this->authorize('restore', $model);

        $this->pageService->restore($model);

        $this->flashSuccess('Página restaurada correctamente.');

        return redirect()->route('admin.pages.index');
    }

    public function forceDelete(int $page): RedirectResponse
    {
        $model = Page::onlyTrashed()->findOrFail($page);
        $this->authorize('forceDelete', $model);

        $this->pageService->forceDelete($model);

        $this->flashSuccess('Página eliminada permanentemente.');

        return redirect()->route('admin.pages.index');
    }
}
