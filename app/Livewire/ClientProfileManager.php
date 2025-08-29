<?php

namespace App\Livewire;

use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class ClientProfileManager extends Component
{
    public bool $showModal = false;

    public $user;
    public string $name = '';
    public string $email = '';
    public bool $isSocialUser = false;
    public string $password = '';
    public string $password_confirmation = '';

    // Campos específicos del modelo Cliente
    public $cliente;
    public string $cliente_nombre = '';
    public string $cliente_telefono = '';

    // Ojo: El listener tiene un nombre diferente para no chocar con el otro componente
    protected $listeners = ['openClientProfileModal' => 'open'];

    protected function rules()
    {
        $rules = [
            // Usamos cliente_nombre para el nombre en el modelo Cliente
            'cliente_nombre' => 'required|string|max:255',
            'cliente_telefono' => ['required', 'regex:/^\d{9}$/'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
        ];

        if (!$this->isSocialUser && !empty($this->password)) {
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        return $rules;
    }

    public function open()
    {
        $this->resetInputFields();
        $this->user = Auth::user()->load('cliente'); // Cargamos la relación 'cliente'
        $this->name = $this->user->name; // El 'name' de User puede ser el de registro inicial
        $this->email = $this->user->email;
        $this->isSocialUser = !is_null($this->user->provider_name);

        if ($this->user->cliente) {
            $this->cliente = $this->user->cliente;
            $this->cliente_nombre = $this->cliente->nombre;
            $this->cliente_telefono = $this->cliente->telefono;
        } else {
             // Si por alguna razón el cliente no se creó en el registro, lo creamos ahora
            $this->cliente = new Cliente(['user_id' => $this->user->id]);
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->resetErrorBag();
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function updateProfile()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            // Actualizar el modelo User (solo email y password)
            $this->user->name = $this->cliente_nombre;
            $this->user->email = $this->email;
            if (!$this->isSocialUser && !empty($this->password)) {
                $this->user->password = Hash::make($this->password);
            }
            $this->user->save();
            
            // Actualizar o crear el modelo Cliente
            $this->cliente->nombre = $this->cliente_nombre;
            $this->cliente->telefono = $this->cliente_telefono;
            $this->cliente->user_id = $this->user->id; // Asegurarse que el user_id está asignado
            $this->cliente->save();

            DB::commit();

            $this->dispatch('alert', ['type' => 'success', 'message' => 'Perfil actualizado correctamente.']);
            $this->dispatch('profileUpdated');
            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Ocurrió un error: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.client-profile-manager');
    }
}