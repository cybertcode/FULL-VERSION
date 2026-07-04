<?php

namespace App\Actions\Admin\User;

use App\Models\Perfil;
use App\Models\User;
use App\Services\Admin\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class UpdateUser
{
    public function __construct(private readonly ImageService $imageService) {}

    public function handle(User $user, array $data, ?UploadedFile $avatar = null): User
    {
        $perfil = $data['perfil'] ?? [];
        $name = Perfil::buildName(
            $perfil['apellido_paterno'] ?? null,
            $perfil['apellido_materno'] ?? null,
            $perfil['nombres'] ?? null,
        ) ?: ($user->name);

        $payload = [
            'name' => $name,
            'username' => $data['username'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'],
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        if ($avatar) {
            $payload['avatar'] = $this->imageService->store(
                $avatar, 'uploads/users', $user->avatar, 85, 400
            );
        }

        $user->update($payload);

        if (! empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        if (array_key_exists('perfil', $data) && is_array($data['perfil'])) {
            $perfilData = array_filter($data['perfil'], fn ($v) => $v !== null && $v !== '');
            $user->perfil()->updateOrCreate(['user_id' => $user->id], $perfilData);
        }

        return $user->fresh(['perfil']);
    }
}
