<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Asumo que usas User por tu controlador original
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    /**
     * Muestra una lista de TODOS los usuarios con rol 'cliente' (para Super Admin).
     */
    public function index()
    {
        $this->authorize('user-list'); // Reutilizamos el permiso de listar usuarios
        $clientes = User::role('cliente')->paginate(10);
        return view('clientes.index', compact('clientes')); // Esta vista es para el Super Admin
    }

    /**
     * Muestra los clientes asociados a la tienda del Admin logueado.
     */

    public function miTienda(Request $request)
    {
        // Asumiendo que obtienes la empresa del usuario autenticado
        $empresa = auth()->user()->empresa;

        if (!$empresa) {
            // Manejar el caso en que el usuario no tenga empresa
            return redirect()->route('dashboard')->with('error', 'No tienes una tienda asignada.');
        }

        // 1. Obtener el término de búsqueda
        $texto = $request->input('texto');

        // 2. Obtener los clientes de esa empresa
        // Aquí la lógica puede variar. Este es un ejemplo:
        // Si los clientes son simplemente usuarios asociados a la empresa.
        $clientes = User::query()
            ->where('empresa_id', $empresa->id) // Filtra por la empresa del usuario actual
            // ->where('role', 'cliente') // Si tienes un rol específico para clientes
            ->when($texto, function ($query, $texto) {
                // Aplica el filtro de búsqueda si existe
                return $query->where(function($q) use ($texto) {
                    $q->where('name', 'LIKE', '%' . $texto . '%')
                    ->orWhere('email', 'LIKE', '%' . $texto . '%');
                });
            })
            ->latest()
            ->paginate(10);

        // 3. Devolver la vista con los datos
        return view('clientes.mitienda', compact('empresa', 'clientes'));
    }

    public function misClientes(Request $request)
    {
        $user = Auth::user();
        $empresa = $user->empresa;

        // Si el admin no tiene empresa, redirigir con un aviso.
        if (!$empresa) {
            return redirect()->route('dashboard')->with('error', 'No tienes una empresa asignada.');
        }

        // Obtener los IDs de los usuarios que son clientes de esta empresa.
        $clienteIds = $empresa->clientes()->pluck('user_id');
        
        // Construir la consulta base sobre el modelo User.
        $query = User::whereIn('id', $clienteIds);

        // Permitir búsqueda por nombre o email
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }
        
        $clientes = $query->paginate(15);
        
        return view('clientes.mis-clientes', compact('clientes', 'empresa'));
    }

    /**
     * Muestra los detalles de un usuario cliente.
     */
    public function show(User $cliente)
    {
        if (!$cliente->hasRole('cliente')) {
            abort(404);
        }
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Elimina un usuario cliente.
     */
    public function destroy(User $cliente)
    {
        if (!$cliente->hasRole('cliente')) {
            abort(403);
        }
        $cliente->delete();
        return redirect()->back()->with('mensaje', 'Cliente eliminado');
    }
}