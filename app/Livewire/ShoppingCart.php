<?php

namespace App\Livewire;

use App\Models\Producto;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Exception;

class ShoppingCart extends Component
{
    public $cartItems;
    public $cartTotal = 0;
    public $returnUrl;
    public $notas = '';

    // Propiedades para los modales
    public $showConfirmClearModal = false;
    public $showConfirmRemoveModal = false;
    public $itemToRemove = null;

    protected $listeners = ['cartUpdated' => 'mount'];

    public function mount()
    {
        try {
            $user = Auth::user();
            $this->cartItems = $user && $user->cart ? $user->cart->items()->with('producto.empresa')->get() : collect();
            $this->calculateTotals();
            $this->returnUrl = $this->cartItems->isNotEmpty()
                ? route('tienda.public.index', $this->cartItems->first()->producto->empresa->slug)
                : session('url.store_before_login', route('welcome'));
        } catch (Exception $e) {
            Log::error('Error al cargar el carrito: ' . $e->getMessage());
            $this->cartItems = collect();
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Error al cargar el carrito.']);
        }
    }

    public function calculateTotals()
    {
        $this->cartTotal = $this->cartItems->sum(fn($item) => optional($item->producto)->precio_final * $item->cantidad ?? 0);
    }

    // --- TU MÉTODO updateQuantity EXACTO, PERO CON UNA MODIFICACIÓN ---
    public function updateQuantity($itemId, $quantity)
    {
        try {
            $newQuantity = (int) trim($quantity);
            $item = $this->cartItems->firstWhere('id', $itemId);

            if (!$item) return;

            // *** MODIFICACIÓN CLAVE ***
            // Si la cantidad es 0 o menos, en lugar de borrar directamente,
            // llamamos al método que abre el modal de confirmación.
            if ($newQuantity <= 0) {
                return $this->confirmRemoveItem($itemId);
            }

            $stockMaximo = $item->producto->stock;

            if ($newQuantity > $stockMaximo) {
                $this->dispatch('alert', ['type' => 'error', 'message' => "Stock insuficiente. Solo quedan {$stockMaximo} unidades."]);

                $itemEnColeccion = $this->cartItems->find($itemId);
                if ($itemEnColeccion) {
                    $itemEnColeccion->cantidad = $stockMaximo;
                }
                return;
            }

            $item->update(['cantidad' => $newQuantity]);
            $this->mount();
            $this->dispatch('cartUpdated');
        } catch (Exception $e) {
            Log::error('Error al actualizar la cantidad: ' . $e->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'No se pudo actualizar el producto.']);
            $this->mount();
        }
    }

    // --- MÉTODOS PARA MANEJAR LOS MODALES ---
    public function confirmRemoveItem($itemId)
    {
        $this->itemToRemove = $this->cartItems->firstWhere('id', $itemId);
        $this->showConfirmRemoveModal = true;
    }

    public function confirmClearCart()
    {
        $this->showConfirmClearModal = true;
    }

    // --- MÉTODOS DE ACCIÓN (AHORA LLAMADOS DESDE LOS MODALES) ---
    public function removeItem()
    {
        try {
            if ($this->itemToRemove) {
                $this->itemToRemove->delete();
                $this->mount();
                $this->dispatch('cartUpdated');
                $this->dispatch('alert', ['type' => 'info', 'message' => 'Producto eliminado del carrito.']);
            }
        } catch (Exception $e) {
            Log::error('Error al eliminar item: ' . $e->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'No se pudo eliminar el producto.']);
        } finally {
            $this->showConfirmRemoveModal = false;
            $this->itemToRemove = null;
        }
    }

    public function clearCart()
    {
        try {
            if (Auth::user()->cart) {
                Auth::user()->cart->items()->delete();
                $this->mount();
                $this->dispatch('cartUpdated');
                $this->dispatch('alert', ['type' => 'success', 'message' => 'El carrito ha sido vaciado.']);
            }
        } catch (Exception $e) {
            Log::error('Error al vaciar el carrito: ' . $e->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'No se pudo vaciar el carrito.']);
        } finally {
            $this->showConfirmClearModal = false;
        }
    }

    // app/Livewire/ShoppingCart.php

    // ... (resto de tu componente)

    public function checkout()
    {
        $user = Auth::user();
        $cart = $user->cart()->with('items.producto')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('welcome')->with('error', 'Tu carrito está vacío.');
        }

        try {
            $pedido = DB::transaction(function () use ($cart, $user) {

                if (!$cliente = $user->cliente) {
                    throw new Exception('Perfil de cliente no encontrado.');
                }

                $empresa = $cart->items->first()->producto->empresa;
                $total = $cart->items->sum(fn($item) => optional($item->producto)->precio_final * $item->cantidad);

                $nuevoPedido = Pedido::create([
                    'cliente_id' => $cliente->id,
                    'empresa_id' => $empresa->id,
                    'total' => $total,
                    'estado' => 'pendiente',
                    'notas' => $this->notas
                ]);

                foreach ($cart->items as $item) {
                    $producto = Producto::lockForUpdate()->find($item->producto_id);
                    if ($producto->stock < $item->cantidad) {
                        throw new Exception("Stock insuficiente para '{$producto->nombre}'.");
                    }
                    $precioPagado = $producto->precio_final;
                    $nuevoPedido->detalles()->create([
                        'producto_id' => $item->producto_id,
                        'cantidad' => $item->cantidad,
                        'precio_unitario' => $precioPagado,
                        'subtotal' => $precioPagado * $item->cantidad,
                    ]);
                    $producto->decrement('stock', $item->cantidad);
                }

                // <<< ¡NUEVA LÍNEA! AQUÍ ESTÁ LA MAGIA! >>>
                // Una vez confirmado el pedido, asociamos al cliente con la empresa si no lo estaba ya.
                $cliente->empresas()->syncWithoutDetaching($empresa->id);

                $cart->items()->delete();
                $this->dispatch('cartUpdated');
                return $nuevoPedido;
            });

            return redirect()->route('pedido.success', $pedido->id)->with('mensaje', '¡Tu pedido ha sido realizado con éxito!');
        } catch (Exception $e) {
            Log::error('Error en checkout para usuario ' . $user->id . ': ' . $e->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Hubo un error al procesar tu pedido: ' . $e->getMessage()]);
            // Es importante no retornar nada aquí para que el usuario se quede en la página del carrito y vea el error.
        }
    }

    // ... (resto de tu componente)

    public function render()
    {
        return view('livewire.shopping-cart');
    }
}
