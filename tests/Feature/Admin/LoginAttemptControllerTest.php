<?php

namespace Tests\Feature\Admin;

use App\Models\LoginAttempt;

class LoginAttemptControllerTest extends AdminTestCase
{
    public function test_admin_with_permission_can_list_login_attempts(): void
    {
        LoginAttempt::create([
            'email' => 'someone@test.com',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'successful' => false,
            'created_at' => now(),
        ]);

        $this->actingAsAdmin()
            ->get(route('admin.login-attempts.index'))
            ->assertOk()
            ->assertViewIs('admin.login-attempts.index')
            ->assertViewHas('attempts')
            ->assertViewHas('stats');
    }

    public function test_super_admin_can_list_login_attempts(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.login-attempts.index'))
            ->assertOk();
    }

    public function test_user_without_permission_cannot_list_login_attempts(): void
    {
        $this->actingAsUser()
            ->get(route('admin.login-attempts.index'))
            ->assertForbidden();
    }
}
