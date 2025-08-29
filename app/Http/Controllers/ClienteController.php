<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
   
    public function index()
    {
        $this->authorize('user-list'); 
        $clientes = User::role('cliente')->paginate(10);
        return view('clientes.index', compact('clientes')); 
    }


    public function miTienda(Request $request)
    {
        
        $empresa = auth()->user()->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')->with('error', 'No tienes una tienda asignada.');
        }
        
        $texto = $request->input('texto');

        $clientes = User::query()
            ->where('empresa_id', $empresa->id) 
            ->when($texto, function ($query, $texto) {
                return $query->where(function($q) use ($texto) {
                    $q->where('name', 'LIKE', '%' . $texto . '%')
                    ->orWhere('email', 'LIKE', '%' . $texto . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('clientes.mitienda', compact('empresa', 'clientes'));
    }

    public function misClientes(Request $request)
{
    $user = Auth::user();
    $empresa = $user->empresa;

    if (!$empresa) {
        return redirect()->route('dashboard')->with('error', 'No tienes una empresa asignada.');
    }

    $clienteIds = $empresa->clientes()->pluck('user_id');
    
    $query = User::whereIn('id', $clienteIds);

    // ##### CAMBIO AQUÍ: Usar 'texto' en lugar de 'q' #####
    if ($request->filled('texto')) { 
        $texto = $request->texto;
        $query->where(function($q) use ($texto) {
            $q->where('name', 'like', '%' . $texto . '%')
              ->orWhere('email', 'like', '%' . $texto . '%');
        });
    }
    
    $clientes = $query->paginate(15);
    
    // Pasamos el texto de búsqueda a la vista para mantenerlo en el input
    return view('clientes.mis-clientes', compact('clientes', 'empresa'))->with('texto', $request->texto);
}

    public function show(User $cliente)
    {
        if (!$cliente->hasRole('cliente')) {
            abort(404);
        }
        return view('clientes.show', compact('cliente'));
    }

    public function destroy(User $cliente)
    {
        if (!$cliente->hasRole('cliente')) {
            abort(403);
        }
        $cliente->delete();
        return redirect()->back()->with('mensaje', 'Cliente eliminado');
    }
}