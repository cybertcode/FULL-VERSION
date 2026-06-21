<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\HasFlashMessages;

abstract class BaseAdminController extends Controller
{
    use HasFlashMessages;

    /**
     * Número de registros por página (desde config/app-settings.php).
     */
    protected int $perPage;

    public function __construct()
    {
        $this->perPage = config('app-settings.pagination.default', 15);
    }
}
