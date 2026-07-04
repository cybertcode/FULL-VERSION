<?php

namespace Tests\Feature\Admin;

use App\Enums\UserStatus;
use App\Models\User;
use App\Services\Admin\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserControllerTest extends AdminTestCase
{
    // ──────────────────────────────────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────────────────────────────────

    public function test_super_admin_can_list_users(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertViewIs('admin.users.index')
            ->assertViewHas('stats');
    }

    public function test_admin_with_permission_can_list_users(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_user_without_permission_cannot_list_users(): void
    {
        $this->actingAsUser()
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_users_index(): void
    {
        $this->get(route('admin.users.index'))
            ->assertRedirect(route('login'));
    }

    public function test_data_endpoint_returns_users(): void
    {
        User::factory()->withPersonalTeam()->count(3)->create();

        $response = $this->actingAsSuperAdmin()
            ->getJson(route('admin.users.data'));

        $response->assertOk()
                 ->assertJsonStructure(['data' => [['id', 'name', 'email', 'role', 'status']]]);

        $this->assertGreaterThanOrEqual(3, count($response->json('data')));
    }

    public function test_stats_are_correct(): void
    {
        User::factory()->withPersonalTeam()->create(['status' => UserStatus::Inactive->value]);
        User::factory()->withPersonalTeam()->create(['status' => UserStatus::Banned->value]);

        $response = $this->actingAsSuperAdmin()
            ->get(route('admin.users.index'));

        $stats = $response->viewData('stats');
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('active', $stats);
        $this->assertArrayHasKey('inactive', $stats);
        $this->assertArrayHasKey('banned', $stats);
        $this->assertGreaterThanOrEqual(1, $stats['inactive']);
        $this->assertGreaterThanOrEqual(1, $stats['banned']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CREATE
    // ──────────────────────────────────────────────────────────────────────────

    public function test_super_admin_can_view_create_form(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.users.create'))
            ->assertOk()
            ->assertViewIs('admin.users.create')
            ->assertViewHas('roles')
            ->assertViewHas('statuses');
    }

    public function test_user_without_permission_cannot_view_create_form(): void
    {
        $this->actingAsUser()
            ->get(route('admin.users.create'))
            ->assertForbidden();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // STORE
    // ──────────────────────────────────────────────────────────────────────────

    public function test_super_admin_can_create_user(): void
    {
        $this->actingAsSuperAdmin()
            ->post(route('admin.users.store'), [
                'name'                  => 'Nuevo Usuario',
                'email'                 => 'nuevo@test.com',
                'phone'                 => '+51 999 000 001',
                'password'              => 'Password123',
                'password_confirmation' => 'Password123',
                'status'                => UserStatus::Active->value,
                'role'                  => 'admin',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'name'  => 'Nuevo Usuario',
            'email' => 'nuevo@test.com',
            'phone' => '+51 999 000 001',
        ]);
    }

    public function test_created_user_has_correct_role(): void
    {
        $this->actingAsSuperAdmin()
            ->post(route('admin.users.store'), [
                'name'                  => 'Usuario Con Rol',
                'email'                 => 'conrol@test.com',
                'password'              => 'Password123',
                'password_confirmation' => 'Password123',
                'status'                => UserStatus::Active->value,
                'role'                  => 'editor',
            ]);

        $user = User::where('email', 'conrol@test.com')->first();
        $this->assertTrue($user->hasRole('editor'));
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAsSuperAdmin()
            ->post(route('admin.users.store'), [])
            ->assertSessionHasErrors(['email', 'password', 'status', 'role']);
    }

    public function test_store_validates_unique_email(): void
    {
        $this->actingAsSuperAdmin()
            ->post(route('admin.users.store'), [
                'name'                  => 'Duplicado',
                'email'                 => $this->admin->email,
                'password'              => 'Password123',
                'password_confirmation' => 'Password123',
                'status'                => UserStatus::Active->value,
                'role'                  => 'user',
            ])
            ->assertSessionHasErrors(['email']);
    }

    public function test_store_validates_password_confirmation(): void
    {
        $this->actingAsSuperAdmin()
            ->post(route('admin.users.store'), [
                'name'                  => 'Test',
                'email'                 => 'test2@test.com',
                'password'              => 'Password123',
                'password_confirmation' => 'OtherPassword',
                'status'                => UserStatus::Active->value,
                'role'                  => 'user',
            ])
            ->assertSessionHasErrors(['password']);
    }

    public function test_store_validates_invalid_role(): void
    {
        $this->actingAsSuperAdmin()
            ->post(route('admin.users.store'), [
                'name'                  => 'Test',
                'email'                 => 'test3@test.com',
                'password'              => 'Password123',
                'password_confirmation' => 'Password123',
                'status'                => UserStatus::Active->value,
                'role'                  => 'rol-que-no-existe',
            ])
            ->assertSessionHasErrors(['role']);
    }

    public function test_store_validates_avatar_is_image(): void
    {
        $file = UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf');

        $this->actingAsSuperAdmin()
            ->post(route('admin.users.store'), [
                'name'                  => 'Test',
                'email'                 => 'test4@test.com',
                'password'              => 'Password123',
                'password_confirmation' => 'Password123',
                'status'                => UserStatus::Active->value,
                'role'                  => 'user',
                'avatar'                => $file,
            ])
            ->assertSessionHasErrors(['avatar']);
    }

    public function test_user_without_create_permission_cannot_store(): void
    {
        $this->actingAsUser()
            ->post(route('admin.users.store'), [
                'name'                  => 'Infiltrado',
                'email'                 => 'infiltrado@test.com',
                'password'              => 'Password123',
                'password_confirmation' => 'Password123',
                'status'                => UserStatus::Active->value,
                'role'                  => 'user',
            ])
            ->assertForbidden();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // EDIT
    // ──────────────────────────────────────────────────────────────────────────

    public function test_super_admin_can_view_edit_form(): void
    {
        $target = User::factory()->withPersonalTeam()->create();

        $this->actingAsSuperAdmin()
            ->get(route('admin.users.edit', $target))
            ->assertOk()
            ->assertViewIs('admin.users.edit')
            ->assertViewHas('user');
    }

    public function test_user_without_edit_permission_cannot_view_edit_form(): void
    {
        $target = User::factory()->withPersonalTeam()->create();

        $this->actingAsUser()
            ->get(route('admin.users.edit', $target))
            ->assertForbidden();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // UPDATE
    // ──────────────────────────────────────────────────────────────────────────

    public function test_super_admin_can_update_user(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $target->assignRole('user');

        $this->actingAsSuperAdmin()
            ->put(route('admin.users.update', $target), [
                // El name se construye desde el perfil (Perfil::buildName)
                'perfil' => [
                    'apellido_paterno' => 'Actualizado',
                    'nombres'          => 'Nombre',
                ],
                'email'  => 'actualizado@test.com',
                'phone'  => '+51 911 222 333',
                'status' => UserStatus::Active->value,
                'role'   => 'editor',
            ])
            ->assertRedirect(route('admin.users.index'));

        $target->refresh();
        $this->assertSame('actualizado@test.com', $target->email);
        $this->assertSame('+51 911 222 333', $target->phone);
        $this->assertStringContainsString('Actualizado', $target->name);
    }

    public function test_update_changes_role(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $target->assignRole('user');

        $this->actingAsSuperAdmin()
            ->put(route('admin.users.update', $target), [
                'name'   => $target->name,
                'email'  => $target->email,
                'status' => UserStatus::Active->value,
                'role'   => 'editor',
            ]);

        $this->assertTrue($target->fresh()->hasRole('editor'));
        $this->assertFalse($target->fresh()->hasRole('user'));
    }

    public function test_update_password_is_optional(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $target->assignRole('user');

        $oldHash = $target->password;

        $this->actingAsSuperAdmin()
            ->put(route('admin.users.update', $target), [
                'name'   => $target->name,
                'email'  => $target->email,
                'status' => UserStatus::Active->value,
                'role'   => 'user',
                // sin password
            ]);

        $this->assertEquals($oldHash, $target->fresh()->password);
    }

    public function test_update_validates_unique_email_ignores_own(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $target->assignRole('user');

        // Mismo email del propio usuario — no debe fallar
        $this->actingAsSuperAdmin()
            ->put(route('admin.users.update', $target), [
                'name'   => $target->name,
                'email'  => $target->email,
                'status' => UserStatus::Active->value,
                'role'   => 'user',
            ])
            ->assertRedirect(route('admin.users.index'));
    }

    public function test_update_email_conflict_with_other_user(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $target->assignRole('user');

        $this->actingAsSuperAdmin()
            ->put(route('admin.users.update', $target), [
                'name'   => $target->name,
                'email'  => $this->admin->email, // email de otro usuario
                'status' => UserStatus::Active->value,
                'role'   => 'user',
            ])
            ->assertSessionHasErrors(['email']);
    }

    public function test_user_without_edit_permission_cannot_update(): void
    {
        $target = User::factory()->withPersonalTeam()->create();

        $this->actingAsUser()
            ->put(route('admin.users.update', $target), [
                'name'   => 'Hack',
                'email'  => 'hack@test.com',
                'status' => UserStatus::Active->value,
                'role'   => 'user',
            ])
            ->assertForbidden();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // DESTROY
    // ──────────────────────────────────────────────────────────────────────────

    public function test_super_admin_can_soft_delete_user(): void
    {
        $target = User::factory()->withPersonalTeam()->create();

        $this->actingAsSuperAdmin()
            ->delete(route('admin.users.destroy', $target))
            ->assertRedirect(route('admin.users.index'));

        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }

    public function test_cannot_delete_own_account(): void
    {
        $this->actingAsSuperAdmin()
            ->delete(route('admin.users.destroy', $this->superAdmin))
            ->assertRedirect() // BusinessException → redirect with errors
            ->assertSessionHasErrors();

        $this->assertNotSoftDeleted('users', ['id' => $this->superAdmin->id]);
    }

    public function test_user_without_delete_permission_cannot_destroy(): void
    {
        $target = User::factory()->withPersonalTeam()->create();

        $this->actingAsUser()
            ->delete(route('admin.users.destroy', $target))
            ->assertForbidden();

        $this->assertNotSoftDeleted('users', ['id' => $target->id]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // RESTORE
    // ──────────────────────────────────────────────────────────────────────────

    public function test_super_admin_can_restore_deleted_user(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $target->delete();

        $this->actingAsSuperAdmin()
            ->post(route('admin.users.restore', $target->id))
            ->assertRedirect(route('admin.users.index'));

        $this->assertNotSoftDeleted('users', ['id' => $target->id]);
    }

    public function test_user_without_restore_permission_cannot_restore(): void
    {
        $target = User::factory()->withPersonalTeam()->create();
        $target->delete();

        $this->actingAsAdmin()
            ->post(route('admin.users.restore', $target->id))
            ->assertForbidden();

        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // AVATAR UPLOAD
    // ──────────────────────────────────────────────────────────────────────────

    public function test_avatar_is_stored_on_create(): void
    {
        Storage::fake('public');

        // Mock ImageService so we don't depend on Spatie/Image GD processing in tests
        $this->mock(ImageService::class, function ($mock) {
            $mock->shouldReceive('store')
                ->once()
                ->andReturn('uploads/users/test-avatar.webp');
        });

        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $this->actingAsSuperAdmin()
            ->post(route('admin.users.store'), [
                'name'                  => 'Con Avatar',
                'email'                 => 'avatar@test.com',
                'password'              => 'Password123',
                'password_confirmation' => 'Password123',
                'status'                => UserStatus::Active->value,
                'role'                  => 'user',
                'avatar'                => $file,
            ])
            ->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'avatar@test.com')->first();
        $this->assertNotNull($user->avatar);
        $this->assertEquals('uploads/users/test-avatar.webp', $user->avatar);
    }
}
