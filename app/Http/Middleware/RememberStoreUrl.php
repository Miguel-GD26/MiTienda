<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; // Importante para comprobar la ruta
use Symfony\Component\HttpFoundation\Response;

class RememberStoreUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()->getName();
        
        // Lista de rutas de tienda que queremos "recordar"
        $storeRoutes = [
            'tienda.public.index',
            'tienda.public.categoria',
        ];

        // Solo guardamos la URL si el usuario está visitando una de las rutas de la tienda.
        // Esto evita que se sobrescriba la URL de la tienda si el usuario navega a /carrito.
        if (in_array($routeName, $storeRoutes)) {
            session(['url.store_before_login' => $request->fullUrl()]);
        } else if (!auth()->check()) {
            session(['url.store_before_login' => $request->fullUrl()]);

        }
        
        return $next($request);
    }
}