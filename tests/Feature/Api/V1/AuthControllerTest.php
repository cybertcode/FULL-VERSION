<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\RecoveryCode;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_two_factor_can_login_with_email_and_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'secret123',
            'device_name' => 'phpunit',
        ]);

        $response->assertOk()->assertJsonStructure(['token', 'user']);
    }

    public function test_user_with_confirmed_two_factor_cannot_login_without_a_code(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);
        $this->enableConfirmedTwoFactor($user);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'secret123',
            'device_name' => 'phpunit',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('code');
    }

    public function test_user_with_confirmed_two_factor_can_login_with_a_valid_code(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);
        $secret = $this->enableConfirmedTwoFactor($user);

        $validCode = app(Google2FA::class)->getCurrentOtp($secret);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'secret123',
            'device_name' => 'phpunit',
            'code' => $validCode,
        ]);

        $response->assertOk()->assertJsonStructure(['token', 'user']);
    }

    public function test_user_with_confirmed_two_factor_can_login_with_a_recovery_code(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);
        $this->enableConfirmedTwoFactor($user);

        $recoveryCode = $user->fresh()->recoveryCodes()[0];

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'secret123',
            'device_name' => 'phpunit',
            'recovery_code' => $recoveryCode,
        ]);

        $response->assertOk()->assertJsonStructure(['token', 'user']);
    }

    private function enableConfirmedTwoFactor(User $user): string
    {
        $secret = app(Google2FA::class)->generateSecretKey();

        $user->forceFill([
            'two_factor_secret' => Fortify::currentEncrypter()->encrypt($secret),
            'two_factor_recovery_codes' => Fortify::currentEncrypter()->encrypt(json_encode(
                Collection::times(8, fn () => RecoveryCode::generate())->all()
            )),
            'two_factor_confirmed_at' => now(),
        ])->save();

        return $secret;
    }
}
