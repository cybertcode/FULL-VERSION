<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;

class PermissionControllerTest extends AdminTestCase
{
    public function test_super_admin_can_list_permissions(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.permissions.index'))
            ->assertOk()
            ->assertViewIs('admin.permissions.index');
    }

    public function test_admin_with_permission_can_list_permissions(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.permissions.index'))
            ->assertOk();
    }

    public function test_user_without_permission_cannot_list_permissions(): void
    {
        $this->actingAsUser()
            ->get(route('admin.permissions.index'))
            ->assertForbidden();
    }

    public function test_data_endpoint_returns_json_with_permissions(): void
    {
        $this->actingAsSuperAdmin()
            ->getJson(route('admin.permissions.data'))
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'label', 'module', 'action', 'roles']]]);
    }

    public function test_data_endpoint_filters_by_module(): void
    {
        $response = $this->actingAsSuperAdmin()
            ->getJson(route('admin.permissions.data', ['module' => 'users']))
            ->assertOk();

        $modules = collect($response->json('data'))->pluck('module')->unique();

        $this->assertEqualsCanonicalizing(['users'], $modules->all());
    }

    public function test_user_without_permission_cannot_access_data_endpoint(): void
    {
        $this->actingAsUser()
            ->getJson(route('admin.permissions.data'))
            ->assertForbidden();
    }

    public function test_super_admin_can_export_permissions_csv(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.permissions.export.csv'))
            ->assertOk();
    }

    public function test_super_admin_can_create_permission(): void
    {
        $this->actingAsSuperAdmin()
            ->post(route('admin.permissions.store'), [
                'module' => 'facturas',
                'action' => 'viewAny',
                'label' => 'Ver listado de facturas',
            ])
            ->assertRedirect(route('admin.permissions.index'));

        $this->assertDatabaseHas('permissions', [
            'name' => 'facturas.viewAny',
            'label' => 'Ver listado de facturas',
        ]);
    }

    public function test_cannot_create_duplicate_permission(): void
    {
        $countBefore = Permission::count();

        $this->actingAsSuperAdmin()
            ->post(route('admin.permissions.store'), [
                'module' => 'users',
                'action' => 'viewAny',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('permissions', $countBefore);
    }

    public function test_user_without_permission_cannot_create_permission(): void
    {
        $this->actingAsUser()
            ->post(route('admin.permissions.store'), [
                'module' => 'facturas',
                'action' => 'viewAny',
            ])
            ->assertForbidden();
    }

    public function test_super_admin_can_update_permission_label(): void
    {
        $permission = Permission::where('name', 'users.viewAny')->first();

        $this->actingAsSuperAdmin()
            ->put(route('admin.permissions.update', $permission), [
                'label' => 'Nuevo label',
            ])
            ->assertRedirect(route('admin.permissions.index'));

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'label' => 'Nuevo label',
        ]);
    }

    public function test_can_delete_unassigned_permission(): void
    {
        $permission = Permission::create(['name' => 'facturas.delete', 'guard_name' => 'web']);

        $this->actingAsSuperAdmin()
            ->delete(route('admin.permissions.destroy', $permission))
            ->assertRedirect(route('admin.permissions.index'));

        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }

    public function test_cannot_delete_permission_assigned_to_role(): void
    {
        $permission = Permission::where('name', 'dashboard.view')->first();

        $this->actingAsSuperAdmin()
            ->delete(route('admin.permissions.destroy', $permission))
            ->assertRedirect();

        $this->assertDatabaseHas('permissions', ['id' => $permission->id]);
    }
}
