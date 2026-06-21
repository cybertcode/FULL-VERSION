<?php

namespace Tests\Feature\Admin;

use Spatie\Permission\Models\Role;

class RoleControllerTest extends AdminTestCase
{
    // ──────────────────────────────────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────────────────────────────────

    public function test_super_admin_can_view_roles_index(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.roles.index'))
            ->assertOk()
            ->assertViewIs('admin.roles.index')
            ->assertViewHas('roles')
            ->assertViewHas('permissionsGrouped');
    }

    public function test_admin_with_permission_can_view_roles_index(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.roles.index'))
            ->assertOk();
    }

    public function test_user_without_permission_cannot_view_roles(): void
    {
        $this->actingAsUser()
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_roles_index(): void
    {
        $this->get(route('admin.roles.index'))
            ->assertRedirect(route('login'));
    }

    public function test_permissions_are_grouped_by_module(): void
    {
        $response = $this->actingAsSuperAdmin()
            ->get(route('admin.roles.index'));

        $grouped = $response->viewData('permissionsGrouped');

        $this->assertArrayHasKey('users', $grouped->toArray());
        $this->assertArrayHasKey('roles', $grouped->toArray());
        $this->assertArrayHasKey('settings', $grouped->toArray());
        $this->assertArrayHasKey('dashboard', $grouped->toArray());
    }

    // ──────────────────────────────────────────────────────────────────────────
    // STORE
    // ──────────────────────────────────────────────────────────────────────────

    public function test_super_admin_can_create_role(): void
    {
        $this->actingAsSuperAdmin()
            ->post(route('admin.roles.store'), [
                'name'        => 'moderador',
                'permissions' => ['users.viewAny', 'users.view'],
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', ['name' => 'moderador']);

        $role = Role::findByName('moderador', 'web');
        $this->assertTrue($role->hasPermissionTo('users.viewAny'));
        $this->assertTrue($role->hasPermissionTo('users.view'));
        $this->assertFalse($role->hasPermissionTo('users.create'));
    }

    public function test_store_validates_required_name(): void
    {
        $this->actingAsSuperAdmin()
            ->post(route('admin.roles.store'), [])
            ->assertSessionHasErrors(['name']);
    }

    public function test_store_validates_unique_role_name(): void
    {
        $this->actingAsSuperAdmin()
            ->post(route('admin.roles.store'), ['name' => 'admin'])
            ->assertSessionHasErrors(['name']);
    }

    public function test_store_validates_max_length_name(): void
    {
        $this->actingAsSuperAdmin()
            ->post(route('admin.roles.store'), ['name' => str_repeat('a', 101)])
            ->assertSessionHasErrors(['name']);
    }

    public function test_store_validates_permissions_exist(): void
    {
        $this->actingAsSuperAdmin()
            ->post(route('admin.roles.store'), [
                'name'        => 'nuevo-rol',
                'permissions' => ['permiso.inventado'],
            ])
            ->assertSessionHasErrors(['permissions.0']);
    }

    public function test_user_without_create_permission_cannot_store_role(): void
    {
        $this->actingAsUser()
            ->post(route('admin.roles.store'), ['name' => 'hackeado'])
            ->assertForbidden();

        $this->assertDatabaseMissing('roles', ['name' => 'hackeado']);
    }

    public function test_role_can_be_created_without_permissions(): void
    {
        $this->actingAsSuperAdmin()
            ->post(route('admin.roles.store'), ['name' => 'vacio'])
            ->assertRedirect(route('admin.roles.index'));

        $role = Role::findByName('vacio', 'web');
        $this->assertCount(0, $role->permissions);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // UPDATE
    // ──────────────────────────────────────────────────────────────────────────

    public function test_super_admin_can_update_role(): void
    {
        $role = Role::create(['name' => 'temporal', 'guard_name' => 'web']);

        $this->actingAsSuperAdmin()
            ->put(route('admin.roles.update', $role), [
                'name'        => 'temporal-actualizado',
                'permissions' => ['dashboard.view'],
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', ['name' => 'temporal-actualizado']);
        $this->assertTrue($role->fresh()->hasPermissionTo('dashboard.view'));
    }

    public function test_update_syncs_permissions_removing_old_ones(): void
    {
        $role = Role::create(['name' => 'con-permisos', 'guard_name' => 'web']);
        $role->givePermissionTo(['users.viewAny', 'users.view']);

        $this->actingAsSuperAdmin()
            ->put(route('admin.roles.update', $role), [
                'name'        => 'con-permisos',
                'permissions' => ['dashboard.view'],
            ]);

        $fresh = $role->fresh();
        $this->assertTrue($fresh->hasPermissionTo('dashboard.view'));
        $this->assertFalse($fresh->hasPermissionTo('users.viewAny'));
        $this->assertFalse($fresh->hasPermissionTo('users.view'));
    }

    public function test_cannot_update_super_admin_role(): void
    {
        $superAdminRole = Role::findByName('Super-Admin', 'web');

        $this->actingAsSuperAdmin()
            ->put(route('admin.roles.update', $superAdminRole), [
                'name'        => 'Super-Admin',
                'permissions' => ['dashboard.view'],
            ])
            ->assertRedirect(); // BusinessException → redirect con error

        // El rol sigue sin permisos explícitos (bypass total)
        $this->assertCount(0, $superAdminRole->fresh()->permissions);
    }

    public function test_update_validates_unique_name_ignores_own(): void
    {
        $role = Role::create(['name' => 'mi-rol', 'guard_name' => 'web']);

        $this->actingAsSuperAdmin()
            ->put(route('admin.roles.update', $role), [
                'name' => 'mi-rol', // mismo nombre — no debe fallar
            ])
            ->assertRedirect(route('admin.roles.index'));
    }

    public function test_update_validates_unique_name_conflict(): void
    {
        Role::create(['name' => 'otro-rol', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'mi-rol2', 'guard_name' => 'web']);

        $this->actingAsSuperAdmin()
            ->put(route('admin.roles.update', $role), [
                'name' => 'otro-rol', // nombre de otro rol existente
            ])
            ->assertSessionHasErrors(['name']);
    }

    public function test_user_without_edit_permission_cannot_update_role(): void
    {
        $role = Role::create(['name' => 'protegido', 'guard_name' => 'web']);

        $this->actingAsUser()
            ->put(route('admin.roles.update', $role), ['name' => 'hackeado'])
            ->assertForbidden();

        $this->assertDatabaseHas('roles', ['name' => 'protegido']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // DESTROY
    // ──────────────────────────────────────────────────────────────────────────

    public function test_super_admin_can_delete_custom_role(): void
    {
        $role = Role::create(['name' => 'eliminable', 'guard_name' => 'web']);

        $this->actingAsSuperAdmin()
            ->delete(route('admin.roles.destroy', $role))
            ->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseMissing('roles', ['name' => 'eliminable']);
    }

    public function test_cannot_delete_protected_roles(): void
    {
        foreach (['Super-Admin', 'admin', 'user'] as $roleName) {
            $role = Role::findByName($roleName, 'web');

            $this->actingAsSuperAdmin()
                ->delete(route('admin.roles.destroy', $role))
                ->assertRedirect(); // BusinessException

            $this->assertDatabaseHas('roles', ['name' => $roleName]);
        }
    }

    public function test_cannot_delete_role_with_users(): void
    {
        // 'admin' tiene $this->admin asignado
        $adminRole = Role::findByName('admin', 'web');

        $this->actingAsSuperAdmin()
            ->delete(route('admin.roles.destroy', $adminRole))
            ->assertRedirect(); // BusinessException: tiene usuarios

        $this->assertDatabaseHas('roles', ['name' => 'admin']);
    }

    public function test_user_without_delete_permission_cannot_delete_role(): void
    {
        $role = Role::create(['name' => 'a-salvo', 'guard_name' => 'web']);

        $this->actingAsUser()
            ->delete(route('admin.roles.destroy', $role))
            ->assertForbidden();

        $this->assertDatabaseHas('roles', ['name' => 'a-salvo']);
    }
}
