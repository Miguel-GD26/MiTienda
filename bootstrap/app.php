<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request; // Importación necesaria
use Illuminate\Database\QueryException; 

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
        $exceptions->render(function (QueryException $e, $request) {
            if (str_contains($e->getMessage(), 'SQLSTATE[HY000] [2006] MySQL server has gone away')) {
                return response()->view('errors.database-connecting', [], 503);
            }
        });
    })->create();