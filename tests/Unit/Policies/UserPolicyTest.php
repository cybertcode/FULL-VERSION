<?php

namespace Tests\Unit\Policies;

use App\Enums\UserStatus;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    private UserPolicy $policy;
    private User $superAdmin;
    private User $adminUser;
    private User $plainUser;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $perms = [
            'users.viewAny', 'users.view', 'users.create', 'users.edit',
            'users.delete', 'users.restore', 'users.forceDelete',
        ];
        foreach ($perms as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'Super-Admin', 'guard_name' => 'web']);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($perms);

        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userRole->syncPermissions([]);

        $this->superAdmin = User::factory()->withPersonalTeam()->create();
        $this->superAdmin->assignRole('Super-Admin');

        $this->adminUser = User::factory()->withPersonalTeam()->create();
        $this->adminUser->assignRole('admin');

        $this->plainUser = User::factory()->withPersonalTeam()->create();
        $this->plainUser->assignRole('user');

        $this->policy = new UserPolicy();
    }

    public function test_viewAny_granted_with_permission(): void
    {
        $this->assertTrue($this->policy->viewAny($this->adminUser));
    }

    public function test_viewAny_denied_without_permission(): void
    {
        $this->assertFalse($this->policy->viewAny($this->plainUser));
    }

    public function test_view_granted_with_permission(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $this->assertTrue($this->policy->view($this->adminUser, $target));
    }

    public function test_view_denied_without_permission(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $this->assertFalse($this->policy->view($this->plainUser, $target));
    }

    public function test_create_granted_with_permission(): void
    {
        $this->assertTrue($this->policy->create($this->adminUser));
    }

    public function test_create_denied_without_permission(): void
    {
        $this->assertFalse($this->policy->create($this->plainUser));
    }

    public function test_update_granted_with_permission(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $this->assertTrue($this->policy->update($this->adminUser, $target));
    }

    public function test_update_denied_without_permission(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $this->assertFalse($this->policy->update($this->plainUser, $target));
    }

    public function test_delete_granted_with_permission_on_other_user(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $this->assertTrue($this->policy->delete($this->adminUser, $target));
    }

    public function test_delete_denied_on_own_account(): void
    {
        // Un usuario con permiso no puede eliminarse a sí mismo
        $this->assertFalse($this->policy->delete($this->adminUser, $this->adminUser));
    }

    public function test_delete_denied_without_permission(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $this->assertFalse($this->policy->delete($this->plainUser, $target));
    }

    public function test_restore_granted_with_permission(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $this->assertTrue($this->policy->restore($this->adminUser, $target));
    }

    public function test_restore_denied_without_permission(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $this->assertFalse($this->policy->restore($this->plainUser, $target));
    }

    public function test_forceDelete_granted_with_permission_on_other(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $this->assertTrue($this->policy->forceDelete($this->adminUser, $target));
    }

    public function test_forceDelete_denied_on_own_account(): void
    {
        $this->assertFalse($this->policy->forceDelete($this->adminUser, $this->adminUser));
    }

    public function test_forceDelete_denied_without_permission(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $this->assertFalse($this->policy->forceDelete($this->plainUser, $target));
    }
}
