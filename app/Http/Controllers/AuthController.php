<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse; // Importante para el type-hinting

class AuthController extends Controller
{
    /**
     * Maneja el intento de autenticación del usuario.
     */
    public function login(Request $request): RedirectResponse
    {
        // 1. Validación de los datos de entrada.
        // validate() devuelve los datos validados si tiene éxito.
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


        // PRIORIDAD 1: Los administradores SIEMPRE van al dashboard.
            if ($user->hasRole(['super_admin', 'admin'])) {
                // redirect()->intended() es inteligente. Si el admin intentó entrar
                // a una página protegida, lo llevará allí. Si no, a 'dashboard'.
                return redirect()->intended('dashboard');
            }

            // 2. Clientes van a la tienda de la que vinieron o a la página principal.
            if ($user->hasRole('cliente')) {
                if (session()->has('url.store_before_login')) {
                    $url = session()->pull('url.store_before_login');
                    return redirect($url);
                }
                // Si no hay URL guardada, van al welcome.
                return redirect()->route('welcome');
            }

            // PRIORIDAD 3: Redirección por defecto para cualquier otro caso (ej. un cliente logueándose desde la página principal).
            return redirect()->route('welcome');
        }
        // 7. Si la autenticación falla, devolvemos a la página anterior con un error.
        return back()->with('error', 'El correo electrónico o la contraseña no son correctos.')
                     ->withInput($request->only('email')); // Solo devolvemos el email.
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}