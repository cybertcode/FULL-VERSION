<?php

namespace Tests\Unit;

use App\Rules\SafeMenuUrl;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SafeMenuUrlRuleTest extends TestCase
{
    #[DataProvider('dangerousUrls')]
    public function test_rejects_dangerous_schemes(string $url): void
    {
        $failed = false;

        (new SafeMenuUrl)->validate('url', $url, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed, "Expected '{$url}' to be rejected");
    }

    #[DataProvider('safeUrls')]
    public function test_allows_safe_urls(?string $url): void
    {
        $failed = false;

        (new SafeMenuUrl)->validate('url', $url, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed, "Expected '{$url}' to be allowed");
    }

    public static function dangerousUrls(): array
    {
        return [
            ['javascript:alert(1)'],
            ['JavaScript:alert(1)'],
            ['data:text/html,<script>alert(1)</script>'],
            ['vbscript:msgbox(1)'],
        ];
    }

    public static function safeUrls(): array
    {
        return [
            [null],
            [''],
            ['/servicios'],
            ['#footer'],
            ['https://example.com'],
            ['http://example.com/path?x=1'],
            ['mailto:hola@example.com'],
            ['tel:+51999999999'],
        ];
    }
}
