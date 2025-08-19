<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request; // Importación necesaria

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // 1. Configurar los proxies de confianza 
        // Pasamos los parámetros posicionalmente en lugar de por nombre.
        $middleware->trustProxies(
            '*', // 1er Parámetro: los proxies
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB 
        );

        // 2. Añadir tu middleware personalizado (se mantiene igual)
        $middleware->web(append: [
            \App\Http\Middleware\CheckTrialStatus::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();