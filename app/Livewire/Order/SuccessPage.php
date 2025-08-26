<?php

namespace App\Livewire\Order;

use App\Models\Pedido;
use Livewire\Component;
use Livewire\Attributes\Locked; 

class SuccessPage extends Component
{
    #[Locked]
    public Pedido $pedido;

    public $whatsappMessage;
    public $tiendaWhatsapp;
    
    public function mount(Pedido $pedido)
    {
        // 1. Autorización: Asegurarse de que el usuario solo vea su propio pedido.
        if (auth()->id() !== $pedido->cliente->user_id) {
            abort(403, 'Acceso no autorizado.');
        }

        $this->pedido = $pedido->load('detalles.producto', 'empresa', 'cliente');

        $this->tiendaWhatsapp = $this->pedido->empresa->telefono_whatsapp;

        $this->buildWhatsappMessage();
    }

    private function buildWhatsappMessage()
    {
        $resumen = "*¡Nuevo Pedido Realizado!* 🛍️\n\n" .
                   "*Pedido N°:* " . $this->pedido->id . "\n" .
                   "*Cliente:* " . $this->pedido->cliente->nombre . "\n" .
                   "*Fecha:* " . $this->pedido->created_at->format('d/m/Y') . "\n" .
                   "-----------------------------------\n" .
                   "*Productos:*\n";

        foreach ($this->pedido->detalles as $detalle) {
            $nombreProducto = $detalle->producto->nombre ?? 'Producto desconocido';
            $resumen .= "- {$detalle->cantidad}x {$nombreProducto} = S/." . number_format($detalle->subtotal, 2) . "\n";
        }

        $resumen .= "-----------------------------------\n" .
                    "*TOTAL: S/." . number_format($this->pedido->total, 2) . "*";

        if ($this->pedido->notas) {
            $resumen .= "\n\n*Notas Adicionales:* " . $this->pedido->notas;
        }
        
        $this->whatsappMessage = $resumen;
    }

    /**
     * Renderiza la vista y le asigna el layout.
     */
    public function render()
    {
        return view('livewire.order.success-page');
    }
}