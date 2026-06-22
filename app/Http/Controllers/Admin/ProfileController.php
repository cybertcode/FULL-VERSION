<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Profile\UpdateProfileRequest;
use App\Models\Perfil;
use App\Services\Admin\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends BaseAdminController
{
    public function __construct(private readonly ImageService $imageService)
    {
        parent::__construct();
    }

    public function show(): View
    {
        $user = auth()->user()->load('perfil', 'roles');
        return view('admin.profile.show', compact('user'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        $perfilData = $data['perfil'] ?? [];

        $name = Perfil::buildName(
            $perfilData['apellido_paterno'] ?? null,
            $perfilData['apellido_materno'] ?? null,
            $perfilData['nombres'] ?? null,
        ) ?: $user->name;

        $payload = [
            'name'     => $name,
            'username' => $data['username'] ?? $user->username,
            'phone'    => $data['phone'] ?? null,
        ];

        if ($request->hasFile('avatar')) {
            $payload['avatar'] = $this->imageService->store(
                $request->file('avatar'), 'uploads/users', $user->avatar, 85, 400
            );
        }

        $user->update($payload);

        if ($request->hasFile('banner')) {
            $perfilData['banner'] = $this->imageService->store(
                $request->file('banner'), 'uploads/banners', $user->perfil?->banner, 80, 1600
            );
        }

        $filtered = array_filter($perfilData, fn ($v) => $v !== null && $v !== '');
        if (! empty($filtered)) {
            $user->perfil()->updateOrCreate(['user_id' => $user->id], $filtered);
        }

        $this->flashSuccess('Perfil actualizado correctamente.');

        return redirect()->route('admin.profile.show');
    }
}
