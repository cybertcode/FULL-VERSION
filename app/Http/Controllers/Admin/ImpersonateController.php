<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends BaseAdminController
{
    public function take(User $user): RedirectResponse
    {
        $this->authorize('impersonate', $user);

        $impersonator = auth()->user();

        session()->put('impersonator_id', $impersonator->id);

        activity('impersonacion')
            ->causedBy($impersonator)
            ->performedOn($user)
            ->withProperties([
                'impersonator_id' => $impersonator->id,
                'impersonator_name' => $impersonator->name,
                'target_id' => $user->id,
                'target_name' => $user->name,
            ])
            ->log("{$impersonator->name} impersonó a {$user->name}.");

        Auth::guard('web')->login($user);

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

        $impersonatedUser = auth()->user();
        $impersonator = User::findOrFail($impersonatorId);

        activity('impersonacion')
            ->causedBy($impersonator)
            ->performedOn($impersonatedUser)
            ->withProperties([
                'impersonator_id' => $impersonator->id,
                'impersonator_name' => $impersonator->name,
                'target_id' => $impersonatedUser->id,
                'target_name' => $impersonatedUser->name,
            ])
            ->log("{$impersonator->name} dejó de impersonar a {$impersonatedUser->name}.");

        Auth::guard('web')->login($impersonator);

        return redirect()->route('admin.users.index')
            ->with('flash', [
                'type' => 'success',
                'message' => 'Volviste a tu sesión original.',
            ]);
    }
}
