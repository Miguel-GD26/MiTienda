<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Empresa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
                // Si el usuario existe, lo logueamos
                Auth::login($user);
                return $this->redirectUser($user);
            }

            // Si el usuario es NUEVO, guardamos sus datos en sesión y lo llevamos a completar el perfil
            session([
                'socialite_user_data' => [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'provider_name' => 'google',
                    'provider_id' => $googleUser->getId(),
                ]
            ]);

            return redirect()->route('login.google.complete');

        } catch (\Exception $e) {
            \Log::error('Error en callback de Google: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Ocurrió un error al intentar iniciar sesión con Google.');
        }
    }

    public function showCompleteForm()
    {
        if (!session()->has('socialite_user_data')) {
            return redirect()->route('login');
        }

        $socialiteData = session('socialite_user_data');
        return view('autenticacion.complete-social-profile', compact('socialiteData'));
    }

    public function processCompleteForm(Request $request): RedirectResponse
    {
        if (!session()->has('socialite_user_data')) {
            return redirect()->route('login')->with('error', 'Tu sesión ha expirado. Inténtalo de nuevo.');
        }

        $socialiteData = session('socialite_user_data');

        $validatedData = $request->validate([
            'tipo_usuario' => ['required', 'in:cliente,empresa'],
            'empresa_nombre' => ['required_if:tipo_usuario,empresa', 'nullable', 'string', 'max:255', 'unique:empresas,nombre'],
            'empresa_telefono_whatsapp' => ['required_if:tipo_usuario,empresa', 'digits:9'],
            'empresa_rubro' => ['required_if:tipo_usuario,empresa', 'nullable', 'string', 'max:255'],
            'cliente_telefono' => ['required_if:tipo_usuario,cliente', 'digits:9'],
        ]);

        $user = null;
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $socialiteData['name'],
                'email' => $socialiteData['email'],
                'provider_name' => $socialiteData['provider_name'],
                'provider_id' => $socialiteData['provider_id'],
                'email_verified_at' => now(),
                'activo' => 1,
            ]);

            if ($validatedData['tipo_usuario'] === 'empresa') {
                $empresa = Empresa::create([
                    'nombre' => $validatedData['empresa_nombre'],
                    'telefono_whatsapp' => $validatedData['empresa_telefono_whatsapp'],
                    'rubro' => $validatedData['empresa_rubro'],
                    'slug' => Str::slug($validatedData['empresa_nombre']),
                ]);
                $user->empresa_id = $empresa->id;
                $user->save();
                $user->assignRole('admin');
            } else {
                Cliente::create([
                    'nombre' => $user->name,
                    'telefono' => $validatedData['cliente_telefono'],
                    'user_id' => $user->id,
                ]);
                $user->assignRole('cliente');
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear usuario social: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'No se pudo completar tu registro. Inténtalo de nuevo.');
        }

        session()->forget('socialite_user_data');
        Auth::login($user);

        return $this->redirectUser($user);
    }

    protected function redirectUser(User $user): RedirectResponse
    {
        if ($user->hasRole(['super_admin', 'admin'])) {
            return redirect()->intended('dashboard')->with('mensaje', '¡Bienvenido!');
        }
        return redirect()->route('welcome')->with('mensaje', '¡Bienvenido!');
    }
}