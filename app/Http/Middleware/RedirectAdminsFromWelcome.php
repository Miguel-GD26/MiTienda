<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectAdminsFromWelcome
{
    public function handle(Request $request, Closure $next): Response
    {
        // La lógica es más simple porque ya sabemos que estamos en la ruta 'welcome'
        if (Auth::check() && Auth::user()->hasRole(['super_admin', 'admin'])) {
            return redirect()->route('dashboard');
        } 

        return $next($request);
    }
}