<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RememberTwoFactorDevice
{
    public const COOKIE_NAME = 'two_factor_remember';

    public const DAYS = 30;

    public static function isRemembered(Request $request, User $user): bool
    {
        $token = $request->cookie(self::COOKIE_NAME);

        if (! $token || ! $user->two_factor_remember_token) {
            return false;
        }

        return Hash::check($user->getKey().'|'.$token, $user->two_factor_remember_token);
    }

    public static function remember(User $user): \Symfony\Component\HttpFoundation\Cookie
    {
        $token = Str::random(60);

        $user->forceFill([
            'two_factor_remember_token' => Hash::make($user->getKey().'|'.$token),
        ])->save();

        return Cookie::make(self::COOKIE_NAME, $token, self::DAYS * 24 * 60, null, null, null, true);
    }
}
