<?php

use App\Exceptions\Handler as AppExceptionHandler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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
            'admin.content' => \App\Http\Middleware\EnsureAdminOrContentEditor::class,
            'admin.section' => \App\Http\Middleware\EnsureAdminSection::class,
            'presidium' => \App\Http\Middleware\EnsurePresidiumAccess::class,
            'setup.pending' => \App\Http\Middleware\EnsureSetupNotCompleted::class,
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
