<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Auth\LoginCustomerRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedCustomerController extends Controller
{
    public function create(): View
    {
        return view('frontend.auth.login');
    }

    public function store(LoginCustomerRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('cuenta.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
