<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View; // <-- 1. IMPORTANTE: Añade esta línea
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrap();

        View::composer('welcome.navbar', function ($view) {
            
            // --- VALORES POR DEFECTO ---
            $cartItemCount = 0;
            $returnUrl = session('url.store_before_login');
            $misTiendas = collect(); // Por defecto, es una colección vacía.
            
            $user = Auth::user();

            if ($user && $user->hasRole('cliente')) {
                // Obtener el carrito y sus items
                $cart = $user->cart()->with('items.producto.empresa')->first();
                if ($cart) {
                    $cartItemCount = $cart->items->count();
                    $firstItem = $cart->items->first();
                    if ($firstItem) {
                        $returnUrl = route('tienda.public.index', $firstItem->producto->empresa->slug);
                    }
                }

                // --- NUEVO: OBTENER LAS TIENDAS DEL CLIENTE ---
                // Asumiendo que tienes una relación 'empresas' en tu modelo Cliente
                // y que esta relación está correctamente definida (belongsToMany).
                if ($user->cliente) {
                    // Obtenemos las tiendas y las ordenamos por nombre para el menú.
                    // Usamos ->get() para obtener la colección.
                    $misTiendas = $user->cliente->empresas()->orderBy('nombre')->get();
                }
            }
            
            // Pasamos TODAS las variables necesarias a la vista del navbar.
            $view->with('cartItemCount', $cartItemCount)
                ->with('returnUrl', $returnUrl)
                ->with('misTiendas', $misTiendas); // Pasamos la nueva colección de tiendas
        });
    }

}
