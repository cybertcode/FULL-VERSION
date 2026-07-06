<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\VerifyDeveloperCreditMiddleware;
use App\Services\System\DeveloperCreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyDeveloperCreditMiddlewareTest extends AdminTestCase
{
    public function test_dashboard_response_contains_valid_credit_signature(): void
    {
        $credit = app(DeveloperCreditService::class);

        $response = $this->actingAsSuperAdmin()->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('data-dev-credit="'.$credit->signature().'"', false);
    }

    public function test_does_not_log_warning_when_credit_is_intact(): void
    {
        Log::spy();

        $this->actingAsSuperAdmin()->get(route('admin.dashboard'));

        Log::shouldNotHaveReceived('warning');
    }

    private function runMiddleware(string $htmlBody): void
    {
        $middleware = new VerifyDeveloperCreditMiddleware;

        $middleware->handle(
            Request::create('/panel-simulado', 'GET'),
            fn () => response($htmlBody, 200, ['Content-Type' => 'text/html'])
        );
    }

    public function test_logs_warning_when_credit_markup_is_missing(): void
    {
        Log::spy();

        $this->runMiddleware('<html><body>sin footer</body></html>');

        Log::shouldHaveReceived('warning')
            ->withArgs(fn (string $message, array $_context = []) => str_contains($message, 'ausente'))
            ->once();
    }

    public function test_logs_warning_when_credit_signature_is_tampered(): void
    {
        Log::spy();

        $fakeSignature = str_repeat('a', 64);

        $this->runMiddleware('<html><body><div data-dev-credit="'.$fakeSignature.'">DevelopTech</div></body></html>');

        Log::shouldHaveReceived('warning')
            ->withArgs(fn (string $message, array $_context = []) => str_contains($message, 'inválida'))
            ->once();
    }

    public function test_does_not_verify_non_html_responses(): void
    {
        Log::spy();

        $middleware = new VerifyDeveloperCreditMiddleware;
        $middleware->handle(
            Request::create('/api-simulada', 'GET'),
            fn () => response()->json(['ok' => true])
        );

        Log::shouldNotHaveReceived('warning');
    }
}
