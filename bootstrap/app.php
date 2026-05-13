<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',  // <-- agrega esto
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\ShareCartData::class,
        ]);

        $middleware->preventRequestForgery(except: [   // ← cambiar
            'webhooks/mercadopago',
            'webhooks/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpException $e, $request) {
            // Si es un 403 y la petición venía hacia el panel de Filament
            if ($e->getStatusCode() === 403 && $request->is('admin') || $request->is('admin/*')) {
                return redirect('/')->with(
                    'status',
                    'No tienes permisos para acceder al panel administrativo.'
                );
            }
        });
    })->create();
