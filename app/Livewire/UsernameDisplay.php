<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class UsernameDisplay extends Component
{
    // Escucha el evento que emitimos cuando el perfil se actualiza
    #[On('profileUpdated')] 
    public function refreshName()
    {
        // Este método no necesita hacer nada.
        // El simple hecho de llamarlo hace que Livewire re-renderice el componente.
    }

    public function render()
    {
        // En lugar de un archivo Blade, usamos una vista "inline" por su simplicidad.
        // Su única responsabilidad es mostrar el nombre del usuario autenticado.
        return <<<'HTML'
            <span>
                {{ Auth::user()->name }}
            </span>
        HTML;
    }
}