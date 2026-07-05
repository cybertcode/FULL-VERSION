<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Auth\RegisterCustomerRequest;
use App\Models\Customer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredCustomerController extends Controller
{
    public function create(): View
    {
        return view('frontend.auth.register');
    }

    public function store(RegisterCustomerRequest $request): RedirectResponse
    {
        $customer = Customer::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            'password' => Hash::make($request->string('password')),
        ]);

        event(new Registered($customer));

        Auth::guard('customer')->login($customer);

        return redirect()->route('cuenta.dashboard');
    }
}
