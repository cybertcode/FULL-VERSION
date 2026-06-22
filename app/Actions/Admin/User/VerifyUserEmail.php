<?php

namespace App\Actions\Admin\User;

use App\Models\User;

class VerifyUserEmail
{
    public function handle(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->forceFill(['email_verified_at' => now()])->save();
    }
}
