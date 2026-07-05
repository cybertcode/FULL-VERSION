<?php

namespace App\Http\Controllers\Frontend\Account;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('frontend.account.dashboard');
    }
}
