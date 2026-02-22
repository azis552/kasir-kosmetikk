<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'log.user.activity' => \App\Http\Middleware\LogUserActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {

            // 403 - Forbidden
            if (
                $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                && $e->getStatusCode() === 403
            ) {

                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki hak akses.');
            }

            // 404 - Not Found
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return redirect()->back()
                    ->with('error', 'Halaman tidak ditemukan.');
            }

            // 419 - Page Expired (CSRF)
            if ($e instanceof \Illuminate\Session\TokenMismatchException) {
                return redirect()->back()
                    ->with('error', 'Halaman expired, silakan ulangi lagi.');
            }

            // 500 - Server Error
            if (
                $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                && $e->getStatusCode() === 500
            ) {

                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan pada sistem.');
            }

        });
    })->create();
