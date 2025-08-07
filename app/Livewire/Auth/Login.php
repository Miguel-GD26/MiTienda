<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class Login extends Component
{
    public $email = '';
    public $password = '';

    protected $rules = [
        'email'    => 'required|email',
        'password' => 'required',
    ];

    protected $messages = [
        'email.required' => 'El campo correo electrónico es obligatorio.',
        'email.email' => 'El formato del correo electrónico no es válido.',
        'password.required' => 'El campo contraseña es obligatorio.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function authenticate()
    {

        $credentials = $this->validate();
        $throttleKey = Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => "Demasiados intentos. Por favor, inténtalo de nuevo en {$seconds} segundos."
            ]);
            return;
        }

        if (!Auth::attempt($credentials)) {
            RateLimiter::hit($throttleKey);
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'El correo electrónico o la contraseña no son correctos.'
            ]);
            return;
        }
        
        RateLimiter::clear($throttleKey);

        $user = Auth::user();

        if (!$user->activo) {
            Auth::logout();
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Su cuenta está inactiva. Por favor, contacte con el administrador.'
            ]);
            return;
        }

        session()->regenerate();

        if ($user->hasRole(['super_admin', 'admin', 'vendedor', 'repartidor'])) {
            return $this->redirect('dashboard');
        }

        if ($user->hasRole('cliente')) {
            if (session()->has('url.store_before_login')) {
                $url = session()->pull('url.store_before_login');
                return $this->redirect($url);
            }
            return $this->redirectRoute('welcome');
        }
        
        return $this->redirectRoute('welcome');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}