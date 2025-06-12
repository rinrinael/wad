<?php

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
    ->withMiddleware(function (Middleware $middleware) {
        // Hapus alias Spatie (jika ada) dan ganti dengan middleware kustom Anda
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class, // <-- Pastikan ini menunjuk ke middleware kustom Anda
            // Hapus baris ini jika Anda tidak ingin menggunakan permission atau role_or_permission Spatie:
            // 'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            // 'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();