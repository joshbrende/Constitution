<?php

use App\Exceptions\Handler as AppExceptionHandler;
use App\Http\Middleware\EnsureAdminOrContentEditor;
use App\Http\Middleware\EnsureAdminSection;
use App\Http\Middleware\EnsurePresidiumAccess;
use App\Http\Middleware\EnsureSetupNotCompleted;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\RequestContextMiddleware::class);

        $middleware->throttleApi();

        $middleware->alias([
            'admin.content' => EnsureAdminOrContentEditor::class,
            'admin.section' => EnsureAdminSection::class,
            'presidium' => EnsurePresidiumAccess::class,
            'setup.pending' => EnsureSetupNotCompleted::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function ($request, \Throwable $_e) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })
    ->withSingletons([
        ExceptionHandler::class => AppExceptionHandler::class,
    ])
    ->create();
