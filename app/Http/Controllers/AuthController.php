<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!$user->activo) {
                Auth::logout();
                return back()->with('error', 'Su cuenta está inactiva. Por favor, contacte con el administrador.');
            }

            $request->session()->regenerate();
            
            // La lógica para roles internos sigue igual, está perfecta.
            if ($user->hasRole(['super_admin', 'admin', 'vendedor', 'repartidor'])) {
                return redirect()->intended('dashboard');
            }

            if ($user->hasRole('cliente')) {
                // --- INICIO DE LA MODIFICACIÓN ---

                // 1. Damos PRIORIDAD a la URL 'redirect' que viene de la barra de navegación.
                $redirectUrl = $request->query('redirect');

                // 2. Revisamos si es una URL válida y segura para nuestro sitio.
                if ($redirectUrl && str_starts_with($redirectUrl, url('/'))) {
                    return redirect($redirectUrl);
                }

                // 3. Si no hay 'redirect', usamos el plan B: la sesión que ya tenías.
                if (session()->has('url.store_before_login')) {
                    $url = session()->pull('url.store_before_login');
                    return redirect($url);
                }
                
                // 4. Si no hay ninguno de los dos, vamos a la bienvenida.
                return redirect()->route('welcome');

                // --- FIN DE LA MODIFICACIÓN ---
            }
            
            return redirect()->route('welcome');
        }
        
        return back()->with('error', 'El correo electrónico o la contraseña no son correctos.')
                     ->withInput($request->only('email'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}