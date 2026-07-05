<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\TwoFactorAuthenticatable;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string'],
            'code' => ['nullable', 'string'],
            'recovery_code' => ['nullable', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $this->ensureTwoFactorPasses($user, $credentials);

        $token = $user->createToken($credentials['device_name'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Si el usuario tiene 2FA confirmado, exige un código TOTP o de
     * recuperación válido antes de emitir el token — replica el mismo
     * control que Fortify aplica al login por sesión, para que la API no
     * sea una vía de bypass del segundo factor.
     *
     * @param  array{email: string, password: string, device_name: string, code: ?string, recovery_code: ?string}  $credentials
     */
    private function ensureTwoFactorPasses(User $user, array $credentials): void
    {
        if (! $user->two_factor_secret ||
            is_null($user->two_factor_confirmed_at) ||
            ! in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user), true)) {
            return;
        }

        if ($recoveryCode = $credentials['recovery_code'] ?? null) {
            $validCode = collect($user->recoveryCodes())->first(
                fn ($code) => hash_equals($code, $recoveryCode)
            );

            if ($validCode) {
                $user->replaceRecoveryCode($validCode);

                return;
            }
        } elseif ($code = $credentials['code'] ?? null) {
            $valid = app(TwoFactorAuthenticationProvider::class)->verify(
                Fortify::currentEncrypter()->decrypt($user->two_factor_secret),
                $code
            );

            if ($valid) {
                return;
            }
        }

        throw ValidationException::withMessages([
            'code' => ['Se requiere un código de autenticación de dos factores válido.'],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }
}
