<?php

namespace Tests\Feature\Admin;

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
}
