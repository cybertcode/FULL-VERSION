<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ActivityLogRetentionTest extends TestCase
{
    use RefreshDatabase;

    public function test_activitylog_clean_command_purges_old_records(): void
    {
        $old = Activity::create([
            'log_name' => 'usuarios',
            'description' => 'registro viejo',
            'created_at' => now()->subDays(400),
            'updated_at' => now()->subDays(400),
        ]);

        $recent = Activity::create([
            'log_name' => 'usuarios',
            'description' => 'registro reciente',
        ]);

        $this->artisan('activitylog:clean --force')->assertSuccessful();

        $this->assertDatabaseMissing('activity_log', ['id' => $old->id]);
        $this->assertDatabaseHas('activity_log', ['id' => $recent->id]);
    }
}
