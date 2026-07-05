<?php

use App\Exceptions\BusinessException;
use App\Exceptions\UnauthorizedException;
use App\Http\Middleware\BlockRegistrationMiddleware;
use App\Http\Middleware\Enforce2FAMiddleware;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\MaintenanceModeMiddleware;
use App\Http\Middleware\ResolveLoginIdentifierMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\TrackLastLoginMiddleware;
use App\Http\Middleware\ValidateRegistrationCaptchaMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/frontend.php'));

            Route::prefix('api/v1')
                ->middleware('api')
                ->name('api.v1.')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(LocaleMiddleware::class);
        $middleware->web(MaintenanceModeMiddleware::class);
        $middleware->web(BlockRegistrationMiddleware::class);
        $middleware->web(ValidateRegistrationCaptchaMiddleware::class);
        $middleware->web(ResolveLoginIdentifierMiddleware::class);
        $middleware->web(Enforce2FAMiddleware::class);
        $middleware->web(TrackLastLoginMiddleware::class);

        $middleware->web(append: SecurityHeadersMiddleware::class);
        $middleware->api(append: SecurityHeadersMiddleware::class);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (NotFoundHttpException $_, Request $request) {
            if (! $request->expectsJson()) {
                return response()->view('errors.404', [], 404);
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $_, Request $request) {
            if (! $request->expectsJson()) {
                return response()->view('errors.403', [], 403);
            }
        });

        $exceptions->render(function (UnauthorizedException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 403);
            }

            return response()->view('errors.403', [], 403);
        });

        $exceptions->render(function (BusinessException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        });

    })->create();
