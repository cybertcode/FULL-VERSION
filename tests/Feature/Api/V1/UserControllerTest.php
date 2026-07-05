<?php

namespace Tests\Feature\Api\V1;

use App\Enums\UserStatus;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'users.viewAny', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'users.view', 'guard_name' => 'web']);

        $role = Role::firstOrCreate(['name' => 'api-viewer', 'guard_name' => 'web']);
        $role->syncPermissions(['users.viewAny', 'users.view']);
    }

    public function test_authenticated_user_with_permission_can_list_users(): void
    {
        $this->seedPermissions();

        $user = User::factory()->withPersonalTeam()->create(['status' => UserStatus::Active->value]);
        $user->assignRole('api-viewer');

        User::factory()->withPersonalTeam()->count(3)->create();

        Sanctum::actingAs($user);

        $response = $this->getJson(route('api.v1.users.index'));

        $response->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'email', 'status']], 'links', 'meta']);
    }

    public function test_per_page_is_capped_at_100(): void
    {
        $this->seedPermissions();

        $user = User::factory()->withPersonalTeam()->create();
        $user->assignRole('api-viewer');

        Sanctum::actingAs($user);

        $response = $this->getJson(route('api.v1.users.index', ['per_page' => 500]));

        $response->assertOk();
        $this->assertSame(100, $response->json('meta.per_page'));
    }

    public function test_user_without_permission_cannot_list_users(): void
    {
        $user = User::factory()->withPersonalTeam()->create();

        Sanctum::actingAs($user);

        $this->getJson(route('api.v1.users.index'))->assertForbidden();
    }

    public function test_can_show_single_user(): void
    {
        $this->seedPermissions();

        $viewer = User::factory()->withPersonalTeam()->create();
        $viewer->assignRole('api-viewer');
        $target = User::factory()->withPersonalTeam()->create(['name' => 'Usuario Objetivo']);

        Sanctum::actingAs($viewer);

        $this->getJson(route('api.v1.users.show', $target))
            ->assertOk()
            ->assertJsonPath('data.name', 'Usuario Objetivo');
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $this->getJson(route('api.v1.users.index'))->assertUnauthorized();
    }
}
