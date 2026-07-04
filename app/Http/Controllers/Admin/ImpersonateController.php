<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function take(User $user): RedirectResponse
    {
        $this->authorize('impersonate', $user);

        session()->put('impersonator_id', auth()->id());

        Auth::login($user);

        return redirect()->route('admin.dashboard')
            ->with('flash', [
                'type' => 'info',
                'message' => "Ahora estás navegando como {$user->name}.",
            ]);
    }

    public function leave(): RedirectResponse
    {
        $impersonatorId = session()->pull('impersonator_id');

        abort_unless($impersonatorId, 403);

        $impersonator = User::findOrFail($impersonatorId);

        Auth::login($impersonator);

        return redirect()->route('admin.users.index')
            ->with('flash', [
                'type' => 'success',
                'message' => 'Volviste a tu sesión original.',
            ]);
    }
}
