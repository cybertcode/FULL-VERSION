<?php

namespace Tests\Unit\Policies;

use App\Models\Role;
use App\Models\User;
use App\Policies\RolePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RolePolicyTest extends TestCase
{
    use RefreshDatabase;

    private RolePolicy $policy;

    private User $adminUser;

    private User $plainUser;

    private Role $customRole;

    private Role $superAdminRole;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $perms = [
            'roles.viewAny', 'roles.view', 'roles.create',
            'roles.edit', 'roles.delete', 'roles.assignPermissions',
        ];
        foreach ($perms as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $this->superAdminRole = Role::firstOrCreate(['name' => 'Super-Admin', 'guard_name' => 'web']);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($perms);

        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userRole->syncPermissions([]);

        $this->customRole = Role::create(['name' => 'personalizado', 'guard_name' => 'web']);

        $this->adminUser = User::factory()->withPersonalTeam()->create();
        $this->adminUser->assignRole('admin');

        $this->plainUser = User::factory()->withPersonalTeam()->create();
        $this->plainUser->assignRole('user');

        $this->policy = new RolePolicy;
    }

    public function test_view_any_granted_with_permission(): void
    {
        $this->assertTrue($this->policy->viewAny($this->adminUser));
    }

    public function test_view_any_denied_without_permission(): void
    {
        $this->assertFalse($this->policy->viewAny($this->plainUser));
    }

    public function test_view_granted_with_permission(): void
    {
        $this->assertTrue($this->policy->view($this->adminUser, $this->customRole));
    }

    public function test_view_denied_without_permission(): void
    {
        $this->assertFalse($this->policy->view($this->plainUser, $this->customRole));
    }

    public function test_create_granted_with_permission(): void
    {
        $this->assertTrue($this->policy->create($this->adminUser));
    }

    public function test_create_denied_without_permission(): void
    {
        $this->assertFalse($this->policy->create($this->plainUser));
    }

    public function test_update_granted_on_custom_role(): void
    {
        $this->assertTrue($this->policy->update($this->adminUser, $this->customRole));
    }

    public function test_update_denied_on_super_admin_role(): void
    {
        // Super-Admin es intocable incluso con permiso
        $this->assertFalse($this->policy->update($this->adminUser, $this->superAdminRole));
    }

    public function test_update_denied_without_permission(): void
    {
        $this->assertFalse($this->policy->update($this->plainUser, $this->customRole));
    }

    public function test_delete_granted_on_custom_role(): void
    {
        $this->assertTrue($this->policy->delete($this->adminUser, $this->customRole));
    }

    public function test_delete_denied_on_super_admin_role(): void
    {
        $this->assertFalse($this->policy->delete($this->adminUser, $this->superAdminRole));
    }

    public function test_delete_denied_on_admin_role(): void
    {
        $adminRole = Role::findByName('admin');
        $this->assertFalse($this->policy->delete($this->adminUser, $adminRole));
    }

    public function test_delete_denied_on_user_role(): void
    {
        $userRole = Role::findByName('user');
        $this->assertFalse($this->policy->delete($this->adminUser, $userRole));
    }

    public function test_delete_denied_without_permission(): void
    {
        $this->assertFalse($this->policy->delete($this->plainUser, $this->customRole));
    }

    public function test_assign_permissions_granted_on_custom_role(): void
    {
        $this->assertTrue($this->policy->assignPermissions($this->adminUser, $this->customRole));
    }

    public function test_assign_permissions_denied_on_super_admin_role(): void
    {
        $this->assertFalse($this->policy->assignPermissions($this->adminUser, $this->superAdminRole));
    }

    public function test_assign_permissions_denied_without_permission(): void
    {
        $this->assertFalse($this->policy->assignPermissions($this->plainUser, $this->customRole));
    }
}
