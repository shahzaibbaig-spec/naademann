<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\ApiCors::class);
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

$configuredPublicPath = null;

foreach ([
    $_SERVER['APP_PUBLIC_PATH'] ?? null,
    $_ENV['APP_PUBLIC_PATH'] ?? null,
    getenv('APP_PUBLIC_PATH') ?: null,
] as $candidate) {
    if (is_string($candidate) && $candidate !== '') {
        $configuredPublicPath = rtrim($candidate, '\\/');
        break;
    }
}

if ($configuredPublicPath === null) {
    $publicPathFile = __DIR__.'/public_path.php';

    if (file_exists($publicPathFile)) {
        $resolvedPublicPath = require $publicPathFile;

        if (is_string($resolvedPublicPath) && $resolvedPublicPath !== '') {
            $configuredPublicPath = rtrim($resolvedPublicPath, '\\/');
        }
    }
}

if ($configuredPublicPath !== null) {
    $app->usePublicPath($configuredPublicPath);
}

return $app;
