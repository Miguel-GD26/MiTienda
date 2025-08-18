<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        // Usaremos la clave 'url.intended' que es el estándar de Laravel para redirecciones post-login.
        $intendedUrlKey = 'url.intended';

        // 1. Si la URL actual ya es la de login o registro, no hacemos nada.
        // Esto evita que se guarde la página de login como destino.
        if ($request->routeIs('login', 'registro')) {
            return $next($request);
        }

        // 2. Si es una petición GET (no un envío de formulario) y el usuario es un invitado...
        if ($request->isMethod('get') && !auth()->check()) {
            
            // 3. Guardamos la URL completa en la sesión.
            // Esto "recordará" cualquier página, incluyendo la de la tienda con el parámetro `&add_product=X`.
            session([$intendedUrlKey => $request->fullUrl()]);
        }
        
        return $next($request);
    }
}