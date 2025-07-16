<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Empresa;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Importante para transacciones
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;


class PedidoController extends Controller
{
    use AuthorizesRequests;

    /**
     * Muestra una lista de pedidos, adaptada al rol del usuario.
     */
    public function index(Request $request)
    {   
        $this->authorize('pedido-list');
        $user = Auth::user();
        $query = Pedido::with(['cliente', 'empresa'])->latest();

        // Lógica de filtrado por rol
        if ($user->hasRole('cliente')) {
            $query->where('cliente_id', $user->cliente?->id);
        } 
        elseif ($user->hasRole('admin')) {
            $query->where('empresa_id', $user->empresa_id);
        }
        elseif ($user->hasRole('super_admin')) {
            if ($request->filled('empresa_id')) {
                $query->where('empresa_id', $request->empresa_id);
            } else {
                $query->where('id', -1);
            }
        }

        // Lógica de filtros adicionales para admins
        if ($user->hasRole(['super_admin', 'admin'])) {
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
            if ($request->filled('cliente_nombre')) {
                 $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', '%' . $request->cliente_nombre . '%'));
            }
        }
        
        $pedidos = $query->paginate(15)->appends($request->query());
        $empresas = $user->hasRole('super_admin') ? Empresa::orderBy('nombre')->get() : collect();
        $estados = ['pendiente', 'atendido',  'enviado', 'entregado', 'cancelado'];

        // Guardamos los filtros en la sesión para el botón "Volver"
        session(['pedido_filters' => $request->query()]);
        
        // Devolvemos la vista correcta
        if ($user->hasRole(['super_admin', 'admin'])) {
            return view('pedido.admin', compact('pedidos', 'empresas', 'estados'));
        }
        return view('pedido.cliente', compact('pedidos'));
    }

    /**
     * Muestra los detalles de un pedido específico.
     */
    public function show(Pedido $pedido)
    {
        $this->authorize('pedido-view', $pedido);
        $pedido->load('detalles.producto', 'empresa', 'cliente');

        if (auth()->user()->hasRole('cliente')) {
            return view('pedido.detalle', compact('pedido'));
        }
        return view('pedido.detalleAdmin', compact('pedido'));
    }

    /**
     * Actualiza el estado de un pedido (acción de admin).
     * Siguiendo el patrón de tus otros controladores.
     */
    public function update(Request $request, Pedido $pedido)
    {
        $this->authorize('pedido-update-status', $pedido);

        // --- VALIDACIÓN ---
        $validatedData = $request->validate([
            'estado' => 'required|in:pendiente,atendido,procesando,enviado,entregado,completado,cancelado',
        ]);

        // --- ACTUALIZACIÓN (USANDO TRANSACCIÓN POR SEGURIDAD) ---
        DB::beginTransaction();
        try {
            $pedido->estado = $validatedData['estado'];
            $pedido->save();

            // Aquí podrías añadir lógica futura, como enviar una notificación al cliente.
            // Por ejemplo: Notification::send($pedido->cliente->user, new OrderStatusUpdated($pedido));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Registramos el error para depuración
            \Log::error('Error al actualizar estado del pedido #' . $pedido->id . ': ' . $e->getMessage());
            // Devolvemos un mensaje de error al usuario
            return back()->with('error', 'Ocurrió un error inesperado al actualizar el estado.');
        }

        return back()->with('mensaje', 'Estado del pedido #' . $pedido->id . ' actualizado a "'.ucfirst($validatedData['estado']).'".');
    }
    
    /**
     * Marca un pedido como cancelado.
     */
    public function destroy(Pedido $pedido)
    {
        $this->authorize('pedido-cancel', $pedido);
        
        try {
            $pedido->update(['estado' => 'cancelado']);
        } catch (\Exception $e) {
            return redirect()->route('pedidos.index', session('pedido_filters', []))
                ->with('error', 'No se pudo cancelar el pedido.');
        }

        return redirect()->route('pedidos.index', session('pedido_filters', []))
            ->with('mensaje', 'Pedido #' . $pedido->id . ' ha sido cancelado.');
    }
    public function cancelarPorCliente(Request $request, Pedido $pedido)
    {
        // 1. Autorización: ¿Este pedido pertenece al usuario logueado?
        // Usamos la relación para ser más explícitos
        if (Auth::user()->cliente?->id !== $pedido->cliente_id) {
            abort(403, 'No tienes permiso para cancelar este pedido.');
        }

        // 2. Lógica de negocio: ¿El pedido todavía se puede cancelar?
        // Solo se puede cancelar si está 'pendiente'.
        if ($pedido->estado !== 'pendiente') {
            return back()->with('error', 'Este pedido ya no se puede cancelar porque la tienda ha comenzado a procesarlo.');
        }

        // 3. Actualizar el estado
        $pedido->estado = 'cancelado';
        $pedido->save();

        // 4. Redirigir de vuelta con un mensaje de éxito
        return redirect()->route('pedidos.index')->with('mensaje', 'Tu pedido #' . $pedido->id . ' ha sido cancelado exitosamente.');
    }
}