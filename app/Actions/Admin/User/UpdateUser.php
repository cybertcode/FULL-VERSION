<?php

namespace App\Actions\Admin\User;

use App\Models\User;
use App\Services\Admin\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class UpdateUser
{
    public function __construct(private readonly ImageService $imageService) {}

    public function handle(User $user, array $data, ?UploadedFile $avatar = null): User
    {
        $payload = [
            'name'   => $data['name'],
            'email'  => $data['email'],
            'phone'  => $data['phone'] ?? null,
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

        if (array_key_exists('role', $data)) {
            $user->syncRoles([$data['role']]);
        }

        return $user->fresh();
    }
}
