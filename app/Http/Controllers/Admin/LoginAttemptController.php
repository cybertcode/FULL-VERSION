<?php

namespace App\Http\Controllers\Admin;

use App\Services\Admin\LoginAttemptService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginAttemptController extends BaseAdminController
{
    public function __construct(protected LoginAttemptService $service)
    {
        parent::__construct();
    }

    public function index(Request $request): View
    {
        $this->authorize('login-attempts.viewAny');

        return view('admin.login-attempts.index', [
            'attempts' => $this->service->paginate($request),
            'stats' => $this->service->stats(),
        ]);
    }
}
