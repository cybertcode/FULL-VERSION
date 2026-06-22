<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Exceptions\BusinessException;
use App\Exceptions\UnauthorizedException;
use App\Http\Middleware\Enforce2FAMiddleware;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\MaintenanceModeMiddleware;
use App\Http\Middleware\TrackLastLoginMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/frontend.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(LocaleMiddleware::class);
        $middleware->web(MaintenanceModeMiddleware::class);
        $middleware->web(Enforce2FAMiddleware::class);
        $middleware->web(TrackLastLoginMiddleware::class);

        $middleware->alias([
            'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
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
