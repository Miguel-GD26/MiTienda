<?php

namespace App\Livewire\Order\Customer;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyOrders extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $pedidos = Auth::user()->cliente?->pedidos()->with('empresa', 'detalles')->latest()->paginate(10);
        return view('livewire.order.customer.my-orders', compact('pedidos'));
    }
}