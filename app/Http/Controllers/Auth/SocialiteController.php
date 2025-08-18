<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Producto; // <-- 1. Importa el modelo Producto
use App\Models\Cart;     // <-- 2. Importa el modelo Cart
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request; // <-- 3. Importa Request
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect(): RedirectResponse
    {
        // Redirige al usuario a la página de autenticación de Google
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse // <-- 4. Inyecta el Request
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Si el usuario ya existe, lo logueamos
                Auth::login($user);
                
                // --- ¡AQUÍ OCURRE LA MAGIA DE AÑADIR EL PRODUCTO! ---
                // 5. Verificamos si la sesión 'intended' tiene el parámetro 'add_product'
                if ($request->session()->has('url.intended') && strpos($request->session()->get('url.intended'), 'add_product=') !== false) {
                    
                    // Extraemos el ID del producto de la URL guardada
                    parse_str(parse_url($request->session()->get('url.intended'), PHP_URL_QUERY), $queryParams);
                    $productId = $queryParams['add_product'] ?? null;

                    if ($productId && $producto = Producto::find($productId)) {
                        // Creamos una instancia de CartController para usar su lógica de 'add'
                        $cartController = new \App\Http\Controllers\CartController();
                        // Creamos una nueva Request para simular la cantidad (por defecto 1)
                        $addRequest = new Request(['quantity' => 1]);
                        // Llamamos al método 'add'
                        $cartController->add($addRequest, $producto);
                    }
                }
                // --- FIN DE LA MAGIA ---
                
                // Redirigimos al usuario a la página a la que intentaba ir
                return $this->redirectUser($user);
            }

            // Si es un usuario nuevo, guardamos sus datos en la sesión para completar el registro
            session(['socialite_user_data' => [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'provider_name' => 'google',
                'provider_id' => $googleUser->getId(),
            ]]);

            // Lo redirigimos a la página para que elija su rol (cliente, etc.)
            return redirect()->route('login.google.complete');

        } catch (\Exception $e) {
            \Log::error('Error en callback de Google: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Ocurrió un error al intentar iniciar sesión con Google.');
        }
    }

    /**
     * Redirige al usuario según su rol después de iniciar sesión.
     * Ahora usa redirect()->intended() para ser más inteligente.
     */
    protected function redirectUser(User $user): RedirectResponse
    {
        session()->regenerate(); // Regeneramos la sesión por seguridad

        if ($user->hasRole(['super_admin', 'admin', 'vendedor', 'repartidor'])) {
            // Para admins, el dashboard es la prioridad
            return redirect()->intended('dashboard')->with('mensaje', '¡Bienvenido!');
        }
        
        // Para clientes (y otros roles), los redirige a la URL que intentaban visitar.
        // Si no había ninguna URL guardada, los lleva a 'welcome' como respaldo.
        return redirect()->intended(route('welcome'))->with('mensaje', '¡Bienvenido!');
    }

    
}