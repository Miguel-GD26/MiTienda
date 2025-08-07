<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            // Limita a 5 intentos de login por minuto.
            // La clave se genera a partir del email del formulario y la IP del usuario.
            return Limit::perMinute(5)->by(strtolower($request->email) . '|' . $request->ip());
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrap();

        View::composer('welcome.navbar', function ($view) {
            
            $cartItemCount = 0;
            $returnUrl = session('url.store_before_login');
            $misTiendas = collect(); 
            
            $user = Auth::user();

            if ($user && $user->hasRole('cliente')) {
                $cart = $user->cart()->with('items.producto.empresa')->first();
                if ($cart) {
                    $cartItemCount = $cart->items->count();
                    $firstItem = $cart->items->first();
                    if ($firstItem) {
                        $returnUrl = route('tienda.public.index', $firstItem->producto->empresa->slug);
                    }
                }

                if ($user->cliente) {
                    $misTiendas = $user->cliente->empresas()->orderBy('nombre')->get();
                }
            }
            
            $view->with('cartItemCount', $cartItemCount)
                ->with('returnUrl', $returnUrl)
                ->with('misTiendas', $misTiendas);
        });
    }

}
