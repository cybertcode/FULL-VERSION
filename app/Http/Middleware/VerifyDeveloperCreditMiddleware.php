<?php

namespace App\Http\Middleware;

use App\Services\System\DeveloperCreditService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyDeveloperCreditMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldVerify($response)) {
            $this->verify($response, app(DeveloperCreditService::class));
        }

        return $response;
    }

    private function shouldVerify(Response $response): bool
    {
        return $response->getStatusCode() === 200
            && str_contains((string) $response->headers->get('Content-Type'), 'text/html');
    }

    private function verify(Response $response, DeveloperCreditService $credit): void
    {
        $content = (string) $response->getContent();

        if (! preg_match('/data-dev-credit="([a-f0-9]{64})"/', $content, $matches)) {
            Log::warning('Crédito de desarrollador ausente en la respuesta renderizada.', [
                'url' => request()->fullUrl(),
            ]);

            return;
        }

        if (! $credit->verify($matches[1])) {
            Log::warning('Crédito de desarrollador con firma inválida — posible manipulación del footer.', [
                'url' => request()->fullUrl(),
            ]);
        }
    }
}
