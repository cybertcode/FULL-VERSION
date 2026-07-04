<?php

namespace App\Listeners;

use App\Actions\Fortify\RememberTwoFactorDevice;
use Illuminate\Support\Facades\Cookie;
use Laravel\Fortify\Events\ValidTwoFactorAuthenticationCodeProvided;

class RememberTwoFactorDeviceOnLogin
{
    public function handle(ValidTwoFactorAuthenticationCodeProvided $event): void
    {
        if (! request()->boolean('remember_device')) {
            return;
        }

        Cookie::queue(RememberTwoFactorDevice::remember($event->user));
    }
}
