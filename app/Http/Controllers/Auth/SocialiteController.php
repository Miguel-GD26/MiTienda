<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                Auth::login($user);
                return $this->redirectUser($user);
            }

            // Si es nuevo, guarda los datos en sesión
            session(['socialite_user_data' => [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'provider_name' => 'google',
                'provider_id' => $googleUser->getId(),
            ]]);

            // Y redirige a la ruta del componente Livewire
            return redirect()->route('login.google.complete');

        } catch (\Exception $e) {
            \Log::error('Error en callback de Google: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Ocurrió un error al intentar iniciar sesión con Google.');
        }
    }

    protected function redirectUser(User $user): RedirectResponse
    {
        if ($user->hasRole(['super_admin', 'admin'])) {
            return redirect()->intended('dashboard')->with('mensaje', '¡Bienvenido!');
        }
        return redirect()->route('welcome')->with('mensaje', '¡Bienvenido!');
    }
}