<?php

namespace App\Http\Controllers\layouts;

use App\Http\Controllers\Controller;

class NavbarFullSidebar extends Controller
{
    public function index()
    {
        return view('content.layouts-example.layouts-navbar-full-with-sidebar');
    }
}
