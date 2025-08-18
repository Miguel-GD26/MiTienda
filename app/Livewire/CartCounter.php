<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartCounter extends Component
{
    public $cartCount = 0;

    // Escucha el evento 'cartUpdated' que se emitirá desde otros componentes
    protected $listeners = ['cartUpdated' => 'updateCartCount'];

    // Se ejecuta al cargar el componente por primera vez
    public function mount()
    {
        $this->updateCartCount();
    }

    // Actualiza la propiedad $cartCount con el valor actual del carrito
    public function updateCartCount()
    {
        if (Auth::check() && Auth::user()->cart) {
            $this->cartCount = Auth::user()->cart->items()->sum('cantidad');
        } else {
            $this->cartCount = 0;
        }
    }

    public function render()
    {
        return view('livewire.cart-counter');
    }
}