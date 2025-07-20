<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cart = $user->cart()->with('items.producto.empresa')->first();
        $cartItems = $cart ? $cart->items : collect();
        
        $cartTotal = $cartItems->sum(function ($item) {
            // Asegurarse de que el producto exista antes de calcular.
            return optional($item->producto)->precio * $item->cantidad ?? 0;
        });

        $empresa = $cartItems->isNotEmpty() ? $cartItems->first()->producto->empresa : null;
        
        return view('tienda.cart', compact('cartItems', 'cartTotal', 'empresa'));
    }

    public function add(Request $request, Producto $producto)
    {
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
            return back()->with('error', "No puedes añadir más. Stock disponible: " . ($producto->stock - $currentQuantityInCart));
        }

        if ($cartItem) {
            $cartItem->increment('cantidad', $cantidadToAdd);
        } else {
            $cart->items()->create(['producto_id' => $producto->id, 'cantidad' => $cantidadToAdd]);
        }

        return back()->with('mensaje', '"'.$producto->nombre.'" añadido al carrito!');
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $cart = $user->cart;

        if (!$cart || !$request->id || $request->quantity === null) {
            return back()->with('error', 'No se pudo actualizar el carrito.');
        }

        $cartItem = $cart->items()->where('producto_id', $request->id)->firstOrFail();
        $newQuantity = (int)$request->quantity;

        if ($newQuantity <= 0) {
            $cartItem->delete();
            return back()->with('mensaje', 'Producto eliminado del carrito.');
        }

        if ($newQuantity > $cartItem->producto->stock) {
            return back()->with('error', "Stock insuficiente. Solo quedan {$cartItem->producto->stock} unidades.");
        }

        $cartItem->update(['cantidad' => $newQuantity]);
        return back()->with('mensaje', 'Carrito actualizado.');
    }

    public function remove(Request $request)
    {
        if($request->id && Auth::user()->cart) {
            Auth::user()->cart->items()->where('producto_id', $request->id)->delete();
        }
        return back()->with('mensaje', 'Producto eliminado del carrito.');
    }

    public function clear()
    {
        if(Auth::user()->cart) {
            Auth::user()->cart->items()->delete();
        }
        return back()->with('mensaje', 'El carrito ha sido vaciado.');
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        $cart = $user->cart()->with('items.producto')->first();
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('welcome')->with('error', 'Tu carrito está vacío.');
        }
        
        try {
            $pedido = DB::transaction(function () use ($cart, $user, $request) {
                $cliente = $user->cliente;
                if (!$cliente) throw new \Exception('Perfil de cliente no encontrado.');
                
                $empresa = $cart->items->first()->producto->empresa;
                $total = $cart->items->sum(fn($item) => $item->producto->precio * $item->cantidad);

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
                    $nuevoPedido->detalles()->create([
                        'producto_id' => $item->producto_id,
                        'cantidad' => $item->cantidad,
                        'precio_unitario' => $producto->precio,
                        'subtotal' => $producto->precio * $item->cantidad,
                    ]);
                    $producto->decrement('stock', $item->cantidad);
                }

                $cliente->empresas()->syncWithoutDetaching($empresa->id);
                $cart->items()->delete(); // Vaciar carrito de la BD
                return $nuevoPedido;
            });

            return redirect()->route('pedido.success', $pedido);

        } catch (\Exception $e) {
            \Log::error('Error en checkout: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }
}