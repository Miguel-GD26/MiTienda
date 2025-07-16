<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Cliente;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{

    public function index(Empresa $empresa = null)
    {
        $cartItems = session('cart', []);
        $cartTotal = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('tienda.cart', compact('cartItems', 'cartTotal', 'empresa'));
    }

    public function add(Request $request, Producto $producto)
    {
        $producto->load('empresa');
        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);
        $imageUrl = null;
        if ($producto->imagen_url) {
            try {
                $imageUrl = cloudinary()->image($producto->imagen_url)->toUrl();
            } catch (\Exception $e) {
                \Log::error("Error al generar URL de Cloudinary para el producto {$producto->id}: " . $e->getMessage());
                $imageUrl = null;
            }
        }
        if(isset($cart[$producto->id])) {
            $cart[$producto->id]['quantity'] += $quantity;
        } else {
            $cart[$producto->id] = [
                "id" => $producto->id,
                "name" => $producto->nombre,
                "quantity" => $quantity,
                "price" => $producto->precio,
                "image" => $imageUrl,
                "tienda_slug" => $producto->empresa->slug,
                "tienda_nombre" => $producto->empresa->nombre,
            ];
        }
        session()->put('cart', $cart);
        return back()->with('mensaje', '¡"'.$producto->nombre.'" añadido al carrito!');
    }

    public function update(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
        }
        return back()->with('mensaje', 'Carrito actualizado.');
    }

    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
        }
        return back()->with('mensaje', 'Producto eliminado del carrito.');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('mensaje', 'El carrito ha sido vaciado.');
    }


    /**
     * Procesa el carrito, crea el pedido y asocia al cliente con la empresa.
     */
    public function checkout(Request $request)
    {
        $cartItems = session('cart', []);
        if (empty($cartItems)) {
            // Si el carrito está vacío, no hay nada que procesar.
            return redirect()->route('welcome')->with('error', 'Tu carrito está vacío.');
        }

        // Usamos una transacción para garantizar que todas las operaciones de la base de datos
        // se completen con éxito. Si alguna falla, todo se revierte.
        try {
            $pedido = DB::transaction(function () use ($cartItems, $request) {
                // 1. Obtener los modelos necesarios
                $user = Auth::user();
                $cliente = $user->cliente; // Usamos la relación definida en el modelo User

                // Verificación de seguridad: si el usuario no tiene un perfil de cliente, no puede comprar.
                if (!$cliente) {
                    // Este error no debería ocurrir en un flujo normal, pero es una buena protección.
                    throw new \Exception('No se encontró el perfil de cliente para el usuario autenticado.');
                }
                
                // 2. Determinar la empresa y calcular el total
                $firstItem = reset($cartItems);
                // Obtenemos el modelo del producto para acceder a su relación con la empresa de forma segura
                $productoDeReferencia = Producto::findOrFail($firstItem['id']);
                $empresa = $productoDeReferencia->empresa;
                
                $total = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);

                // 3. Crear el Pedido principal
                $nuevoPedido = Pedido::create([
                    'cliente_id' => $cliente->id,
                    'empresa_id' => $empresa->id, // Usamos el ID de la empresa obtenida
                    'total' => $total,
                    'estado' => 'pendiente',
                    'notas' => $request->input('notas') // Notas opcionales del cliente
                ]);

                // 4. Crear los detalles del pedido (los productos comprados)
                foreach ($cartItems as $item) {
                    $nuevoPedido->detalles()->create([
                        'producto_id' => $item['id'],
                        'cantidad' => $item['quantity'],
                        'precio_unitario' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);
                }

                // 5. ¡AQUÍ ESTÁ LA LÓGICA CLAVE!
                // Asociamos al cliente con la empresa. Si la relación ya existe, no hace nada.
                // Si no existe, la crea en la tabla pivote `cliente_empresa`.
                $cliente->empresas()->syncWithoutDetaching($empresa->id);

                // 6. Devolver el pedido creado para usarlo después de la transacción
                return $nuevoPedido;
            });

            // 7. Si todo fue exitoso, limpiamos el carrito y redirigimos
            session()->forget('cart');
            return redirect()->route('pedido.success', $pedido);

        } catch (\Exception $e) {
            // Si algo falla dentro de la transacción, se captura el error aquí.
            \Log::error('Error en el proceso de checkout: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Ocurrió un error inesperado al procesar tu pedido. Por favor, inténtalo de nuevo.');
        }
    }
}