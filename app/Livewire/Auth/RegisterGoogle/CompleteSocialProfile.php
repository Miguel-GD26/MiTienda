<?php

namespace App\Livewire\Auth\RegisterGoogle; // <-- El namespace es correcto

use App\Models\User;
use App\Models\Cliente;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class CompleteSocialProfile extends Component
{
    // Propiedades para los datos del usuario de Google
    public $name;
    public $email;

    // Propiedades bindeadas al formulario
    public $tipoUsuario = '';
    public $empresa_nombre;
    public $empresa_telefono_whatsapp;
    public $empresa_rubro;
    public $cliente_telefono;

    // Se ejecuta al cargar el componente
    // En app/Livewire/Auth/RegisterGoogle/CompleteSocialProfile.php
    public function mount()
    {
        if (!session()->has('socialite_user_data')) {
            // En lugar de redirigir, vamos a abortar con un mensaje claro.
            // Esto te mostrará un error visible si la sesión no está.
            abort(403, 'Acceso no autorizado. Debes iniciar sesión con Google primero.');
        }

        $socialiteData = session('socialite_user_data');
        
        // Verificación adicional para asegurarnos de que los datos existen
        $this->name = $socialiteData['name'] ?? 'Usuario Desconocido';
        $this->email = $socialiteData['email'] ?? 'email@desconocido.com';
    }

    // Reglas de validación
    protected function rules()
    {
        return [
            'tipoUsuario' => ['required', 'in:cliente,empresa'],
            'empresa_nombre' => ['required_if:tipoUsuario,empresa', 'nullable', 'string', 'max:255', 'unique:empresas,nombre'],
            'empresa_telefono_whatsapp' => ['required_if:tipoUsuario,empresa', 'nullable', 'string', 'size:9'],
            'empresa_rubro' => ['required_if:tipoUsuario,empresa', 'nullable', 'string', 'max:255'],
            'cliente_telefono' => ['required_if:tipoUsuario,cliente', 'nullable', 'string', 'size:9'],
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    
    // Método para finalizar el registro
    public function finalizeRegistration()
    {
        $this->validate();

        if (!session()->has('socialite_user_data')) {
            return redirect()->route('login')->with('error', 'Tu sesión ha expirado. Inténtalo de nuevo.');
        }

        $socialiteData = session('socialite_user_data');
        
        $user = null;
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $socialiteData['name'],
                'email' => $socialiteData['email'],
                'provider_name' => $socialiteData['provider_name'],
                'provider_id' => $socialiteData['provider_id'],
                'email_verified_at' => now(),
                'activo' => 1,
            ]);

            if ($this->tipoUsuario === 'empresa') {
                $empresa = Empresa::create([
                    'nombre' => $this->empresa_nombre,
                    'telefono_whatsapp' => $this->empresa_telefono_whatsapp,
                    'rubro' => $this->empresa_rubro,
                    'slug' => Str::slug($this->empresa_nombre),
                ]);
                $user->update(['empresa_id' => $empresa->id]);
                $user->assignRole('admin');
            } else {
                Cliente::create(['nombre' => $user->name, 'telefono' => $this->cliente_telefono, 'user_id' => $user->id]);
                $user->assignRole('cliente');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear usuario social: ' . $e->getMessage());
            $this->addError('general', 'No se pudo completar tu registro. Por favor, inténtalo de nuevo.');
            return;
        }

        session()->forget('socialite_user_data');
        Auth::login($user);

        if ($user->hasRole(['super_admin', 'admin'])) {
            return redirect()->intended('dashboard')->with('mensaje', '¡Bienvenido!');
        }
        return redirect()->route('welcome')->with('mensaje', '¡Bienvenido!');
    }
    
    // Renderiza la vista y le asigna el layout principal
    public function render()
    {
        return view('livewire.auth.register-google.complete-social-profile');
    }
}