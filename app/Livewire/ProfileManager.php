<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileManager extends Component
{
    use WithFileUploads;

    public bool $showModal = false;
    // public ?string $successMessage = null; // <-- ELIMINADO

    public $user;
    public string $name = '';
    public string $email = '';
    public bool $isSocialUser = false;
    public string $password = '';
    public string $password_confirmation = '';

    public $empresa;
    public string $empresa_nombre = '';
    public string $empresa_rubro = '';
    public string $empresa_telefono_whatsapp = '';
    public $empresa_logo;

    protected $listeners = ['openProfileModal' => 'open'];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
        ];

        if (!$this->isSocialUser && !empty($this->password)) {
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        if ($this->user && $this->user->hasRole(['super_admin', 'admin']) && $this->empresa) {
            $rules['empresa_nombre'] = ['required', 'string', 'max:255', Rule::unique('empresas', 'nombre')->ignore($this->empresa->id)];
            $rules['empresa_rubro'] = 'nullable|string|max:255';
            $rules['empresa_telefono_whatsapp'] = ['required', 'regex:/^\d{9}$/'];
            $rules['empresa_logo'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        }

        return $rules;
    }
    
    public function open()
    {
        $this->resetInputFields();
        $this->user = Auth::user()->load('empresa');
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->isSocialUser = !is_null($this->user->provider_name);

        if ($this->user->empresa) {
            $this->empresa = $this->user->empresa;
            $this->empresa_nombre = $this->empresa->nombre;
            $this->empresa_rubro = $this->empresa->rubro;
            $this->empresa_telefono_whatsapp = $this->empresa->telefono_whatsapp;
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
        // $this->successMessage = null; // <-- ELIMINADO
        $this->password = '';
        $this->password_confirmation = '';
        $this->empresa_logo = null;
    }

    public function updateProfile()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $this->user->name = $this->name;
            $this->user->email = $this->email;
            
            if (!$this->isSocialUser && !empty($this->password)) {
                $this->user->password = Hash::make($this->password);
            }
            $this->user->save();
            
            if ($this->empresa) {
                $empresaData = [
                    'nombre' => $this->empresa_nombre,
                    'rubro' => $this->empresa_rubro,
                    'telefono_whatsapp' => $this->empresa_telefono_whatsapp,
                ];
                
                if ($this->empresa_logo) {
                    if ($this->empresa->logo_url) {
                        cloudinary()->uploadApi()->destroy($this->empresa->logo_url);
                    }
                    $uploadedFile = cloudinary()->uploadApi()->upload($this->empresa_logo->getRealPath(), ['folder' => 'logos_empresa']);
                    $empresaData['logo_url'] = $uploadedFile['public_id'];
                    $this->empresa_logo = null;
                }
                $this->empresa->update($empresaData);
                $this->empresa = $this->empresa->fresh();
            }

            DB::commit();

            // ##### CAMBIO PRINCIPAL AQUÍ #####
            // ANTES: $this->successMessage = 'Perfil actualizado correctamente.';
            // AHORA:
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Perfil actualizado correctamente.']);
            
            $this->reset('password', 'password_confirmation');
            $this->dispatch('profileUpdated'); // Este evento puede ser útil para actualizar el navbar, etc.
            $this->closeModal(); // Opcional: Cierra el modal automáticamente después del éxito.

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Ocurrió un error: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.profile-manager');
    }
}