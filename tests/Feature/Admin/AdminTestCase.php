<?php

namespace Tests\Feature\Admin;

use App\Enums\UserStatus;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

abstract class AdminTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $plainUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolesAndPermissions();

        $this->superAdmin = User::factory()->withPersonalTeam()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'status' => UserStatus::Active->value,
        ]);
        $this->superAdmin->assignRole('Super-Admin');

        $this->admin = User::factory()->withPersonalTeam()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'status' => UserStatus::Active->value,
        ]);
        $this->admin->assignRole('admin');

        $this->plainUser = User::factory()->withPersonalTeam()->create([
            'name' => 'Plain User',
            'email' => 'user@test.com',
            'status' => UserStatus::Active->value,
        ]);
        $this->plainUser->assignRole('user');
    }

    protected function seedRolesAndPermissions(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'users.viewAny', 'users.view', 'users.create', 'users.edit',
            'users.delete', 'users.restore', 'users.forceDelete',
            'roles.viewAny', 'roles.view', 'roles.create', 'roles.edit',
            'roles.delete', 'roles.assignPermissions',
            'settings.view', 'settings.edit', 'settings.testMail', 'settings.runArtisan',
            'activitylog.viewAny', 'activitylog.export',
            'dashboard.view', 'dashboard.viewStats',
            'files.viewAny', 'files.upload', 'files.delete', 'files.rename', 'files.folder',
            'logs.view',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'Super-Admin', 'guard_name' => 'web']);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'users.viewAny', 'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.viewAny', 'roles.view',
            'settings.view',
            'activitylog.viewAny',
            'dashboard.view', 'dashboard.viewStats',
            'files.viewAny', 'files.upload', 'files.delete', 'files.rename', 'files.folder',
        ]);

        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editor->syncPermissions(['dashboard.view']);

        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $user->syncPermissions(['dashboard.view']);
    }

    protected function actingAsSuperAdmin(): static
    {
        return $this->actingAs($this->superAdmin);
    }

    protected function actingAsAdmin(): static
    {
        return $this->actingAs($this->admin);
    }

    protected function actingAsUser(): static
    {
        return $this->actingAs($this->plainUser);
    }
}
