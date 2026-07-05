<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\Admin\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_trend_is_up_when_current_week_has_more_users(): void
    {
        User::factory()->withPersonalTeam()->count(3)->create(['created_at' => now()->startOfWeek()->addHours(2)]);
        User::factory()->withPersonalTeam()->count(1)->create(['created_at' => now()->startOfWeek()->subWeek()->addHours(2)]);

        $trend = (new DashboardService)->newUsersWeeklyTrend();

        $this->assertSame(3, $trend['current']);
        $this->assertSame(1, $trend['previous']);
        $this->assertSame('up', $trend['trend']);
        $this->assertEquals(200.0, $trend['change_percent']);
    }

    public function test_trend_is_flat_when_no_users_in_either_week(): void
    {
        $trend = (new DashboardService)->newUsersWeeklyTrend();

        $this->assertSame(0, $trend['current']);
        $this->assertSame(0, $trend['previous']);
        $this->assertSame('flat', $trend['trend']);
        $this->assertNull($trend['change_percent']);
    }

    public function test_trend_is_up_100_percent_when_previous_week_was_zero(): void
    {
        User::factory()->withPersonalTeam()->create(['created_at' => now()]);

        $trend = (new DashboardService)->newUsersWeeklyTrend();

        $this->assertSame(1, $trend['current']);
        $this->assertSame(0, $trend['previous']);
        $this->assertSame('up', $trend['trend']);
        $this->assertEquals(100.0, $trend['change_percent']);
    }
}
