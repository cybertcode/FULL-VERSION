<?php

namespace Tests\Feature\Admin;

class ImpersonateControllerTest extends AdminTestCase
{
    public function test_admin_with_permission_can_impersonate_plain_user(): void
    {
        $this->actingAsAdmin()
            ->post(route('admin.users.impersonate', $this->plainUser))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($this->plainUser, 'web');
        $this->assertEquals($this->admin->id, session('impersonator_id'));
    }

    public function test_user_without_permission_cannot_impersonate(): void
    {
        $this->actingAsUser()
            ->post(route('admin.users.impersonate', $this->admin))
            ->assertForbidden();
    }

    public function test_cannot_impersonate_self(): void
    {
        $this->actingAsAdmin()
            ->post(route('admin.users.impersonate', $this->admin))
            ->assertForbidden();
    }

    public function test_cannot_impersonate_super_admin(): void
    {
        $this->actingAsAdmin()
            ->post(route('admin.users.impersonate', $this->superAdmin))
            ->assertForbidden();
    }

    public function test_impersonator_can_leave_and_return_to_original_session(): void
    {
        $this->actingAsAdmin()
            ->post(route('admin.users.impersonate', $this->plainUser))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($this->plainUser, 'web');

        $this->post(route('admin.impersonate.leave'))
            ->assertRedirect(route('admin.users.index'));

        $this->assertAuthenticatedAs($this->admin, 'web');
        $this->assertNull(session('impersonator_id'));
    }

    public function test_leave_without_active_impersonation_is_forbidden(): void
    {
        $this->actingAsAdmin()
            ->post(route('admin.impersonate.leave'))
            ->assertForbidden();
    }
}
