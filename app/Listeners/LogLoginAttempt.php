<?php

namespace App\Listeners;

use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class LogLoginAttempt
{
    public function handleLogin(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;

        LoginAttempt::create([
            'email' => $user->email,
            'ip_address' => Request::ip(),
            'user_agent' => (string) Request::userAgent(),
            'successful' => true,
        ]);
    }

    public function handleFailed(Failed $event): void
    {
        LoginAttempt::create([
            'email' => (string) ($event->credentials['email'] ?? $event->credentials['username'] ?? ''),
            'ip_address' => Request::ip(),
            'user_agent' => (string) Request::userAgent(),
            'successful' => false,
        ]);
    }
}
