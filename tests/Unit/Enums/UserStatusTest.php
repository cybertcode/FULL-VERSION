<?php

namespace Tests\Unit\Enums;

use App\Enums\UserStatus;
use PHPUnit\Framework\TestCase;

class UserStatusTest extends TestCase
{
    public function test_active_has_correct_value(): void
    {
        $this->assertSame('active', UserStatus::Active->value);
    }

    public function test_inactive_has_correct_value(): void
    {
        $this->assertSame('inactive', UserStatus::Inactive->value);
    }

    public function test_banned_has_correct_value(): void
    {
        $this->assertSame('banned', UserStatus::Banned->value);
    }

    public function test_label_returns_spanish_string(): void
    {
        $this->assertNotEmpty(UserStatus::Active->label());
        $this->assertNotEmpty(UserStatus::Inactive->label());
        $this->assertNotEmpty(UserStatus::Banned->label());
    }

    public function test_all_statuses_have_badge_class(): void
    {
        foreach (UserStatus::cases() as $status) {
            $this->assertNotEmpty($status->badgeClass(), "badgeClass() vacío para: {$status->value}");
        }
    }

    public function test_badge_class_contains_bootstrap_bg_class(): void
    {
        foreach (UserStatus::cases() as $status) {
            $this->assertStringStartsWith('bg-', $status->badgeClass());
        }
    }

    public function test_active_has_success_badge(): void
    {
        $this->assertSame('bg-success', UserStatus::Active->badgeClass());
    }

    public function test_inactive_has_secondary_badge(): void
    {
        $this->assertSame('bg-secondary', UserStatus::Inactive->badgeClass());
    }

    public function test_banned_has_danger_badge(): void
    {
        $this->assertSame('bg-danger', UserStatus::Banned->badgeClass());
    }

    public function test_can_create_from_value(): void
    {
        $this->assertSame(UserStatus::Active, UserStatus::from('active'));
        $this->assertSame(UserStatus::Inactive, UserStatus::from('inactive'));
        $this->assertSame(UserStatus::Banned, UserStatus::from('banned'));
    }

    public function test_try_from_returns_null_for_invalid_value(): void
    {
        $this->assertNull(UserStatus::tryFrom('desconocido'));
        $this->assertNull(UserStatus::tryFrom(''));
    }

    public function test_cases_returns_all_three_statuses(): void
    {
        $this->assertCount(3, UserStatus::cases());
    }
}
