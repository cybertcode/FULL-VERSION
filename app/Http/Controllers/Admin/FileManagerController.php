<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;

class FileManagerController extends BaseAdminController
{
    public function index(): View
    {
        $this->authorize('files.viewAny');

        return view('admin.files.index');
    }
}
