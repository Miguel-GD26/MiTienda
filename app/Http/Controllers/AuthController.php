<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse; // Importante para el type-hinting

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

        if ($user->hasRole(['super_admin', 'admin'])) {
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