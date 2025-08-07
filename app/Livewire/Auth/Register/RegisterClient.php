<?php

namespace App\Livewire\Auth\Register;

use Illuminate\Validation\Rules;
use Livewire\Component;

class RegisterClient extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $cliente_telefono = '';
    public $isLoading = false;

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cliente_telefono' => ['required', 'string', 'digits:9'],
        ];
    }

    protected $messages = [
        'name.required' => 'Tu nombre completo es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.unique' => 'Este correo electrónico ya está en uso.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'cliente_telefono.required' => 'Tu teléfono es obligatorio.',
        'cliente_telefono.digits' => 'El teléfono debe tener 9 dígitos.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $validatedData = $this->validate();
        $this->dispatch('registrationSubmitted', data: $validatedData);
        $this->isLoading = true;
    }

    public function render()
    {
        return view('livewire.auth.register.register-client');
    }
}