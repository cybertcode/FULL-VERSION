<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(config('fortify.home'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_authenticate_using_their_username(): void
    {
        $user = User::factory()->create(['username' => 'jperez']);

        $this->post('/login', [
            'email' => 'jperez',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_users_can_authenticate_using_their_dni(): void
    {
        $user = User::factory()->hasPerfil(['dni' => '12345678'])->create();

        $this->post('/login', [
            'email' => '12345678',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_account_locks_after_max_failed_attempts(): void
    {
        $user = User::factory()->create();

        Setting::query()->updateOrCreate(['key' => 'login_max_attempts'], ['value' => '3', 'group' => 'security']);
        Setting::query()->updateOrCreate(['key' => 'login_lockout_minutes'], ['value' => '15', 'group' => 'security']);

        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $user->refresh();
        $this->assertTrue($user->isLocked());

        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $this->assertGuest();
    }

    public function test_failed_attempts_reset_after_successful_login(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);
        $user->refresh();
        $this->assertSame(1, $user->failed_login_attempts);

        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $user->refresh();
        $this->assertSame(0, $user->failed_login_attempts);
    }
}
