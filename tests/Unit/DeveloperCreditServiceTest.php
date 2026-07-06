<?php

namespace Tests\Unit;

use App\Services\System\DeveloperCreditService;
use Tests\TestCase;

class DeveloperCreditServiceTest extends TestCase
{
    public function test_name_decodes_correctly(): void
    {
        $this->assertSame('DevelopTech', (new DeveloperCreditService)->name());
    }

    public function test_url_decodes_correctly(): void
    {
        $this->assertSame('https://www.linkedin.com/in/mkhh/', (new DeveloperCreditService)->url());
    }

    public function test_signature_is_stable_and_matches_verify(): void
    {
        $credit = new DeveloperCreditService;

        $signature = $credit->signature();

        $this->assertSame(64, \strlen($signature));
        $this->assertTrue($credit->verify($signature));
    }

    public function test_verify_fails_for_tampered_signature(): void
    {
        $credit = new DeveloperCreditService;

        $this->assertFalse($credit->verify('firma-invalida'));
        $this->assertFalse($credit->verify(str_repeat('a', 64)));
    }

    public function test_verify_fails_for_null_signature(): void
    {
        $this->assertFalse((new DeveloperCreditService)->verify(null));
    }
}
