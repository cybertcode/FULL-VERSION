<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ModelActivityLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_creation_is_logged_automatically(): void
    {
        $user = User::factory()->create(['name' => 'Ana Torres']);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'log_name' => 'usuarios',
            'event' => 'created',
        ]);
    }

    public function test_user_update_is_logged_with_dirty_attributes_only(): void
    {
        $user = User::factory()->create(['name' => 'Ana Torres', 'status' => UserStatus::Active->value]);

        Activity::query()->delete();

        $user->update(['name' => 'Ana Torres Gómez']);

        $activity = Activity::where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->where('event', 'updated')
            ->first();

        $this->assertNotNull($activity);
        $this->assertArrayHasKey('name', $activity->properties['attributes']);
        $this->assertArrayNotHasKey('password', $activity->properties['attributes'] ?? []);
    }

    public function test_role_creation_is_logged_automatically(): void
    {
        $role = Role::create(['name' => 'editor-test', 'guard_name' => 'web']);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => Role::class,
            'subject_id' => $role->id,
            'log_name' => 'roles',
            'event' => 'created',
        ]);
    }
}
