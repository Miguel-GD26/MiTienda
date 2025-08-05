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

            // --- INICIO DE LA MODIFICACIÓN ---
            // Hemos agrupado todos los roles "internos" que deben ir al dashboard.
            if ($user->hasRole(['super_admin', 'admin', 'vendedor', 'repartidor'])) {
                return redirect()->intended('dashboard');
            }
            // --- FIN DE LA MODIFICACIÓN ---

            if ($user->hasRole('cliente')) {
                if (session()->has('url.store_before_login')) {
                    $url = session()->pull('url.store_before_login');
                    return redirect($url);
                }
                
                return redirect()->route('welcome');
            }
            
            // Si el usuario tiene un rol no definido aquí (muy raro), lo enviamos a la bienvenida.
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