<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable as BaseRedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class RedirectIfTwoFactorAuthenticatable extends BaseRedirectIfTwoFactorAuthenticatable
{
    public function handle($request, $next)
    {
        $user = $this->validateCredentials($request);

        if (optional($user)->two_factor_secret &&
            in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user)) &&
            RememberTwoFactorDevice::isRemembered($request, $user)) {
            Auth::login($user, $request->boolean('remember'));

            $request->session()->regenerate();

            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
