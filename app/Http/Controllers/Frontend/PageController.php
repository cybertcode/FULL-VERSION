<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\PageStatus;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::where('slug', $slug)
            ->where('status', PageStatus::Published->value)
            ->firstOrFail();

        return view($page->view(), compact('page'));
    }
}
