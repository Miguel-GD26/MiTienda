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
use App\Models\Empresa;
use Illuminate\Support\Facades\Route;
use App\Models\Pedido;

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


        $storeRouteName = 'tienda.public.index';

        View::composer('welcome.navbar', function ($view) use ($storeRouteName) {

            // --- TODA LA LÓGICA DE DETECCIÓN DE 'activeStore' SE QUEDA IGUAL ---
            $user = Auth::user();
            $misTiendas = collect();
            $activeStore = null;
            // ... (código para encontrar $activeStore que ya funciona) ...
            $currentRoute = Route::current();
            if ($currentRoute) {
                foreach ($currentRoute->parameters() as $param) {
                    if ($param instanceof Empresa) { $activeStore = $param; break; }
                    if ($param instanceof Pedido) { $activeStore = $param->empresa; break; }
                }
            }
            if (!$activeStore) {
                $slug = request()->segment(1);
                if ($slug && !in_array($slug, ['carrito', 'login', 'register', 'dashboard', 'pedido-exitoso'])) {
                    $activeStore = Empresa::where('slug', $slug)->first();
                }
            }
            
            // Detección 3: A través de la Sesión
            if (!$activeStore && session()->has('last_visited_store_slug')) {
                $activeStore = Empresa::where('slug', session('last_visited_store_slug'))->first();
            }

            // Detección 4: A través del Carrito
            if (!$activeStore && $user && $user->hasRole('cliente')) {
                $cart = $user->cart()->with('items.producto.empresa')->first();
                if ($cart && $cart->items->isNotEmpty()) {
                    $activeStore = $cart->items->first()?->producto?->empresa;
                }
            }

            // --- INICIO DE LA NUEVA LÍNEA CLAVE ---
            // Si después de toda la lógica encontramos una tienda, la guardamos en la sesión
            // para que esté disponible en la siguiente página (ej. al ir al carrito).
            if ($activeStore) {
                session(['last_visited_store_slug' => $activeStore->slug]);
            }

            // --- DEFINICIÓN DE VARIABLES PARA LA VISTA (AQUÍ HACEMOS EL CAMBIO) ---
            $activeStoreName = "";
            $logoPath = asset('assets/img/MiTienda2.png');
            $returnUrl = route('welcome');

            // Definimos las URLs base para los enlaces
            $soporteUrl = route('soporte'); // URL por defecto
            $acercaUrl = route('acerca');   // URL por defecto

            if ($activeStore) {
                // -- SI ESTAMOS EN EL CONTEXTO DE UNA TIENDA --
                $activeStoreName = $activeStore?->nombre;
                $returnUrl = route($storeRouteName, $activeStore->slug);
                if ($activeStore->logo_url) {
                    $logoPath = cloudinary()->image($activeStore->logo_url)->toUrl();
                }

                // Reconstruimos las URLs para que incluyan el slug de la tienda
                $soporteUrl = url($activeStore->slug . '/soporte');
                $acercaUrl = url($activeStore->slug . '/acerca');
            } else if ($user && $user->empresa && $user->empresa->logo_url) {
                $logoPath = cloudinary()->image($user->empresa->logo_url)->toUrl();
            }

            if ($user && $user->hasRole('cliente') && $user->cliente) {
                $misTiendas = $user->cliente->empresas()->orderBy('nombre')->get();
            }

            // Pasamos las nuevas variables a la vista
            $view->with([
                'returnUrl' => $returnUrl,
                'misTiendas' => $misTiendas,
                'logoPath' => $logoPath,
                'soporteUrl' => $soporteUrl, // <-- Nueva variable
                'acercaUrl' => $acercaUrl,   // <-- Nueva variable
                'activeStoreName' => $activeStoreName,
            ]);
        });
    }
}
