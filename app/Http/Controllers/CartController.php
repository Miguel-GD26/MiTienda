<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Muestra la página del carrito de compras.
     */
    public function index()
    {
        $user = Auth::user();
        $cartItems = $user->cart ? $user->cart->items()->with('producto.empresa')->get() : collect();
        
        $cartTotal = $cartItems->sum(function ($item) {
            return optional($item->producto)->precio_final * $item->cantidad ?? 0;
        });
        
        return view('tienda.cart', compact('cartItems', 'cartTotal'));
    }

    /**
     * Añade un producto al carrito.
     */
    public function add(Request $request, Producto $producto)
    {
        $request->validate(['quantity' => 'sometimes|integer|min:1']);
        
        $user = Auth::user();
        $cantidadToAdd = $request->input('quantity', 1);
        $cart = $user->cart()->firstOrCreate();

        $firstItem = $cart->items()->with('producto')->first();
        if ($firstItem && $firstItem->producto->empresa_id !== $producto->empresa_id) {
            return back()->with('error', 'Solo puedes comprar en una tienda a la vez. Vacía tu carrito para añadir productos de esta tienda.');
        }

        $cartItem = $cart->items()->where('producto_id', $producto->id)->first();
        $currentQuantityInCart = $cartItem ? $cartItem->cantidad : 0;

        if (($currentQuantityInCart + $cantidadToAdd) > $producto->stock) {
            $stockRestante = $producto->stock - $currentQuantityInCart;
            return back()->with('error', "No puedes añadir más. Stock disponible: $stockRestante.");
        }

        if ($cartItem) {
            $cartItem->increment('cantidad', $cantidadToAdd);
        } else {
            $cart->items()->create(['producto_id' => $producto->id, 'cantidad' => $cantidadToAdd]);
        }

        return back()->with('mensaje', '"'.$producto->nombre.'" añadido al carrito!');
    }

    /**
     * Actualiza la cantidad de un producto y devuelve la información de precios más reciente.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:productos,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $user = Auth::user();
        if (!$cart = $user->cart) {
            return $this->handleCartError($request, 'Carrito no encontrado.', 404);
        }

        if (!$cartItem = $cart->items()->with('producto')->where('producto_id', $request->id)->first()) {
             return $this->handleCartError($request, 'Producto no encontrado en el carrito.', 404);
        }
        
        $newQuantity = (int)$request->quantity;
        $producto = $cartItem->producto;

        if ($newQuantity > $producto->stock) {
            $errorMsg = "Stock insuficiente. Solo quedan {$producto->stock} unidades.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $errorMsg], 422);
            }
            return back()->with('error', $errorMsg);
        }

        if ($newQuantity <= 0) {
            $cartItem->delete();
            $message = 'Producto eliminado del carrito.';
        } else {
            $cartItem->update(['cantidad' => $newQuantity]);
            $message = 'Carrito actualizado.';
        }

        if ($request->wantsJson()) {
            $cartTotal = $cart->fresh()->items->sum(fn($item) => optional($item->producto)->precio_final * $item->cantidad);
            $itemSubtotal = $producto->precio_final * $newQuantity;

            // ¡CRUCIAL! Preparamos un objeto con la información de precios más reciente.
            $priceInfo = [
                'isOnSale' => $producto->is_on_sale,
                'displayPrice' => 'S/.' . number_format($producto->precio_final, 2),
                'regularPrice' => 'S/.' . number_format($producto->precio, 2)
            ];

            return response()->json([
                'success' => true,
                'message' => $message,
                'cartTotal' => 'S/.' . number_format($cartTotal, 2),
                'itemSubtotal' => 'S/.' . number_format($itemSubtotal, 2),
                'itemPriceInfo' => $priceInfo // ¡Enviamos el nuevo objeto de precios!
            ]);
        }

        return back()->with('mensaje', $message);
    }
    
    /**
     * Elimina un producto del carrito.
     */
    public function remove(Request $request)
    {
        if($request->id && Auth::user()->cart) {
            Auth::user()->cart->items()->where('producto_id', $request->id)->delete();
        }
        return back()->with('mensaje', 'Producto eliminado del carrito.');
    }

    /**
     * Vacía todo el carrito de compras.
     */
    public function clear()
    {
        if(Auth::user()->cart) {
            Auth::user()->cart->items()->delete();
        }
        return back()->with('mensaje', 'El carrito ha sido vaciado.');
    }

    /**
     * Procesa el pedido, descuenta el stock y vacía el carrito.
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        $cart = $user->cart()->with('items.producto')->first();
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('welcome')->with('error', 'Tu carrito está vacío.');
        }
        
        try {
            $pedido = DB::transaction(function () use ($cart, $user, $request) {
                if (!$cliente = $user->cliente) throw new \Exception('Perfil de cliente no encontrado.');
                
                $empresa = $cart->items->first()->producto->empresa;
                $total = $cart->items->sum(fn($item) => optional($item->producto)->precio_final * $item->cantidad);

                $nuevoPedido = Pedido::create([
                    'cliente_id' => $cliente->id,
                    'empresa_id' => $empresa->id,
                    'total' => $total,
                    'estado' => 'pendiente',
                    'notas' => $request->input('notas')
                ]);

                foreach ($cart->items as $item) {
                    $producto = Producto::lockForUpdate()->find($item->producto_id);
                    if ($producto->stock < $item->cantidad) {
                        throw new \Exception("Stock insuficiente para '{$producto->nombre}'.");
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

                $cart->items()->delete();
                return $nuevoPedido;
            });

            return redirect()->route('pedido.success', $pedido->id)->with('mensaje', '¡Tu pedido ha sido realizado con éxito!');

        } catch (\Exception $e) {
            Log::error('Error en checkout para usuario ' . $user->id . ': ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Hubo un error al procesar tu pedido: ' . $e->getMessage());
        }
    }

    /**
     * Función de ayuda para manejar errores de forma consistente.
     */
    private function handleCartError(Request $request, string $message, int $statusCode)
    {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'error' => $message], $statusCode);
        }
        return back()->with('error', $message);
    }
}