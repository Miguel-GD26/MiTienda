<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Sidebar extends Component
{
    // Propiedades públicas que la vista utilizará
    public string $logoPath = '';
    public string $colorFondo = '#00406d'; 
    public string $colorTexto = 'text-white';

    // Aquí está la clave: El componente "escucha" el evento 'profileUpdated'.
    // Cuando lo oye, ejecuta su propio método llamado 'refreshData'.
    protected $listeners = ['profileUpdated' => 'refreshData'];

    // mount() se ejecuta una vez, cuando el componente se carga por primera vez en la página.
    public function mount()
    {
        $this->loadInitialData();
    }

    // Este método carga los datos iniciales.
    public function loadInitialData()
    {
        $user = Auth::user();

        if ($user && $user->empresa && $user->empresa->logo_url) {
            $this->logoPath = cloudinary()->image($user->empresa->logo_url)->toUrl();
        } else {
            $this->logoPath = asset('assets/img/MiTienda.png');
        }
    }

    // Este método es llamado por el listener para refrescar los datos dinámicamente.
    public function refreshData()
    {
        // Forzamos a recargar la información del usuario desde la base de datos
        // para asegurarnos de que tenemos la URL del nuevo logo.
        Auth::user()->refresh();
        
        // Volvemos a ejecutar la misma lógica de carga.
        $this->loadInitialData();
    }

    public function render()
    {
        return view('livewire.sidebar');
    }
}