<?php

namespace App\Livewire\Order\Customer;

use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MyOrderDetail extends Component
{
    public Pedido $pedido;
    public $showCancelModal = false;

    public function mount(Pedido $pedido)
    {
        if (Auth::user()->cliente?->id !== $pedido->cliente_id) abort(403);
        $this->pedido = $pedido->load('detalles.producto', 'empresa', 'cliente');
    }

    private function devolverStockDePedido()
    {
        foreach ($this->pedido->detalles as $detalle) {
            if ($detalle->producto) $detalle->producto->increment('stock', $detalle->cantidad);
        }
    }

    public function openCancelModal()
    {
        if ($this->pedido->estado === 'pendiente') {
            $this->showCancelModal = true;
        }
    }

    public function cancelOrder()
    {
        if ($this->pedido->fresh()->estado !== 'pendiente') {
            $this->showCancelModal = false;
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Este pedido ya no se puede cancelar.']);
            return;
        }

        DB::transaction(function () {
            $this->devolverStockDePedido();
            $this->pedido->update(['estado' => 'cancelado']);
        });
        
        $this->pedido->refresh();
        $this->showCancelModal = false;
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Tu pedido ha sido cancelado.']);
    }

    public function render()
    {
        return view('livewire.order.customer.my-order-detail');
    }
}