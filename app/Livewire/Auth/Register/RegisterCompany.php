<?php

namespace App\Livewire\Auth\Register;

use Illuminate\Validation\Rules;
use Livewire\Component;

class RegisterCompany extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $empresa_nombre = '';
    public $empresa_telefono_whatsapp = '';
    public $empresa_rubro = '';
    public $isLoading = false;

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'empresa_nombre' => ['required', 'string', 'max:255', 'unique:empresas,nombre'],
            'empresa_telefono_whatsapp' => ['required', 'string', 'digits:9'],
            'empresa_rubro' => ['required', 'string', 'max:255'],
        ];
    }
    
    protected $messages = [
        'name.required' => 'Tu nombre completo es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.unique' => 'Este correo electrónico ya está en uso.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'empresa_nombre.required' => 'El nombre de la empresa es obligatorio.',
        'empresa_nombre.unique' => 'El nombre de esta empresa ya está registrado.',
        'empresa_telefono_whatsapp.required' => 'El WhatsApp de la empresa es obligatorio.',
        'empresa_rubro.required' => 'El rubro de la empresa es obligatorio.',
        '*.digits' => 'El teléfono debe tener 9 dígitos.',
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
        return view('livewire.auth.register.register-company');
    }
}