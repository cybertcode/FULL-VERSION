<?php

namespace App\Actions\Admin\User;

use App\Enums\UserStatus;
use App\Models\Perfil;
use App\Models\User;
use App\Services\Admin\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUser
{
    public function __construct(private readonly ImageService $imageService) {}

    public function handle(array $data, ?UploadedFile $avatar = null): User
    {
        $perfil = $data['perfil'] ?? [];
        $name = Perfil::buildName(
            $perfil['apellido_paterno'] ?? null,
            $perfil['apellido_materno'] ?? null,
            $perfil['nombres'] ?? null,
        ) ?: ($data['name'] ?? 'Sin nombre');

        $inviteByEmail = ! empty($data['invite_by_email']);

        $user = User::create([
            'name' => $name,
            'username' => $data['username'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($inviteByEmail ? Str::random(32) : $data['password']),
            'status' => $data['status'] ?? UserStatus::Active->value,
            'avatar' => $avatar
                ? $this->imageService->store($avatar, 'uploads/users', null, 85, 400)
                : null,
        ]);

        if (! empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        if (! empty($data['perfil'])) {
            $perfilData = array_filter($data['perfil'], fn ($v) => $v !== null && $v !== '');
            if (! empty($perfilData)) {
                $user->perfil()->create($perfilData);
            }
        }

        return $user;
    }
}
