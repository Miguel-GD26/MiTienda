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
use Illuminate\Support\Facades\App;
use App\Http\Middleware\CheckTrialStatus;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {

        App::booted(function () {
            app('router')->pushMiddlewareToGroup('web', CheckTrialStatus::class);
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(strtolower($request->email) . '|' . $request->ip());
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrap();

        // View Composer ahora es más simple y eficiente
        View::composer('welcome.navbar', function ($view) {
            
            // Inicializamos las variables que SÍ necesitamos
            $returnUrl = session('url.store_before_login');
            $misTiendas = collect(); 
            
            $user = Auth::user();

            // La lógica para $misTiendas y $returnUrl se mantiene
            if ($user && $user->hasRole('cliente')) {
                // Esta consulta es solo si el usuario es un cliente
                if ($user->cliente) {
                    $misTiendas = $user->cliente->empresas()->orderBy('nombre')->get();
                }

                // Intentamos obtener la URL de la tienda desde el carrito,
                // pero ya no necesitamos contar los ítems aquí.
                $cart = $user->cart()->with('items.producto.empresa')->first();
                if ($cart && $cart->items->isNotEmpty()) {
                    $firstItem = $cart->items->first();
                    if ($firstItem && $firstItem->producto) { // Verificación extra
                        $returnUrl = route('tienda.public.index', $firstItem->producto->empresa->slug);
                    }
                }
            }
            
            // Pasamos solo las variables que la vista de navbar aún necesita.
            // $cartItemCount ya no se pasa desde aquí.
            $view->with('returnUrl', $returnUrl)
                 ->with('misTiendas', $misTiendas);
        });
    }

}