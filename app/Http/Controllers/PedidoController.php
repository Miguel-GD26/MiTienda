<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Empresa;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;


class PedidoController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {   
        $this->authorize('pedido-list');
        $user = Auth::user();
        $query = Pedido::with(['cliente', 'empresa'])->latest();

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

        session(['pedido_filters' => $request->query()]);
        
        if ($user->hasRole(['super_admin', 'admin'])) {
            return view('pedido.admin', compact('pedidos', 'empresas', 'estados'));
        }
        return view('pedido.cliente', compact('pedidos'));
    }

    public function show(Pedido $pedido)
    {
        $this->authorize('pedido-view', $pedido);
        $pedido->load('detalles.producto', 'empresa', 'cliente');

        if (auth()->user()->hasRole('cliente')) {
            return view('pedido.detalle', compact('pedido'));
        }
        return view('pedido.detalleAdmin', compact('pedido'));
    
    }

    private function devolverStockDePedido(Pedido $pedido)
    {
        $pedido->load('detalles.producto');

        foreach ($pedido->detalles as $detalle) {
            if ($detalle->producto) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }
        }
    }

    public function update(Request $request, Pedido $pedido)
    {
        $this->authorize('pedido-update-status', $pedido);

        $validatedData = $request->validate([
            'estado' => ['required', Rule::in(['pendiente', 'atendido', 'enviado', 'entregado', 'cancelado'])],
        ]);

        if ($validatedData['estado'] === 'cancelado' && in_array($pedido->getOriginal('estado'), ['enviado', 'entregado', 'completado'])) {
            return back()->with('error', 'No se puede cancelar un pedido que ya ha sido enviado o entregado.');
        }

        DB::beginTransaction();
        try {
            $nuevoEstado = $validatedData['estado'];
            
            if ($pedido->getOriginal('estado') !== 'cancelado' && $nuevoEstado === 'cancelado') {
                $this->devolverStockDePedido($pedido);
            }

            $pedido->estado = $nuevoEstado;
            $pedido->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar estado del pedido #' . $pedido->id . ': ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al actualizar el estado.');
        }

        return back()->with('mensaje', 'Estado del pedido #' . $pedido->id . ' actualizado a "'.ucfirst($validatedData['estado']).'".');
    }

    public function destroy(Pedido $pedido)
    {
        $this->authorize('pedido-cancel', $pedido);
        
        DB::beginTransaction();
        try {
            $this->devolverStockDePedido($pedido);
            $pedido->estado = 'cancelado';
            $pedido->save();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error del admin al cancelar el pedido #' . $pedido->id . ': ' . $e->getMessage());
            
            return redirect()->route('pedidos.show', $pedido) 
                ->with('error', 'No se pudo cancelar el pedido debido a un error interno.');
        }

        return redirect()->route('pedidos.index', session('pedido_filters', []))
            ->with('mensaje', 'Pedido #' . $pedido->id . ' ha sido cancelado y el stock ha sido devuelto.');
    }
    
    
    public function cancelarPorCliente(Request $request, Pedido $pedido)
    {
        if (Auth::user()->cliente?->id !== $pedido->cliente_id) {
            abort(403, 'No tienes permiso para cancelar este pedido.');
        }

        if ($pedido->estado !== 'pendiente') {
            return back()->with('error', 'Este pedido ya no se puede cancelar porque la tienda ha comenzado a procesarlo.');
        }

        try {
            DB::transaction(function () use ($pedido) {
                $this->devolverStockDePedido($pedido);
                $pedido->estado = 'cancelado';
                $pedido->save();
            });
        } catch (\Exception $e) {
            \Log::error("Error del cliente al cancelar el pedido #{$pedido->id}: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al cancelar tu pedido. Por favor, contacta con soporte.');
        }

        return redirect()->route('pedidos.index')->with('mensaje', 'Tu pedido #' . $pedido->id . ' ha sido cancelado exitosamente. Los productos han sido devueltos al stock.');
    }
}