<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Empresa; // 1. Importar Empresa para la vista de Super Admin
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoriaController extends Controller
{

    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('categoria-list');
        $user = Auth::user();
        $texto = $request->input('texto');
        $query = Categoria::with('empresa');

        if (!$user->hasRole('super_admin')) {
            $query->where('empresa_id', $user->empresa_id);
        }

        if ($texto) {
            $query->where('nombre', 'like', '%' . $texto . '%');
        }

        $registros = $query->orderBy('nombre', 'asc')->paginate(10);
        $empresas = $user->hasRole('super_admin') ? Empresa::all() : collect();

        return view('categorias.index', compact('registros', 'empresas', 'texto'));
    }

    public function create()
    {
        $this->authorize('categoria-create'); // Autorización añadida
        $empresas = auth()->user()->hasRole('super_admin') ? Empresa::all() : collect();
        return view('categorias.action', compact('empresas'));
    }

    public function store(Request $request)
    {
        $this->authorize('categoria-create');
        $user = Auth::user();

        // Para Super Admin, se requiere seleccionar una empresa
        if ($user->hasRole('super_admin')) {
             $request->validate(['empresa_id' => 'required|exists:empresas,id']);
             $empresa_id = $request->empresa_id;
        } else {
             $empresa_id = $user->empresa_id;
             if (!$empresa_id) {
                return back()->with('error', 'No tienes una empresa asignada para crear categorías.');
            }
        }
        
        $request->validate([
            'nombre' => ['required', 'string', 'max:50', Rule::unique('categorias')->where('empresa_id', $empresa_id)],
            'descripcion' => 'nullable|string|max:255',
        ]);

        Categoria::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'empresa_id' => $empresa_id,
        ]);

        return redirect()->route('categorias.index')->with('mensaje', 'Categoría registrada satisfactoriamente.');
    }

    public function edit(Categoria $categoria)
    {
        $this->authorize('categoria-edit', $categoria);
        $empresas = auth()->user()->hasRole('super_admin') ? Empresa::all() : collect();
        return view('categorias.action', ['registro' => $categoria, 'empresas' => $empresas]);
    }

    public function update(Request $request, Categoria $categoria)
    {
        $this->authorize('categoria-edit', $categoria);
        $empresa_id = $categoria->empresa_id;

        $request->validate([
            'nombre' => ['required', 'string', 'max:50', Rule::unique('categorias')->where('empresa_id', $empresa_id)->ignore($categoria->id)],
            'descripcion' => 'nullable|string|max:255',
        ]);

        $categoria->update($request->only(['nombre', 'descripcion']));

        return redirect()->route('categorias.index')->with('mensaje', 'Categoría actualizada satisfactoriamente.');
    }

    public function destroy(Categoria $categoria)
    {
        $this->authorize('categoria-delete', $categoria);
        $nombreCategoria = $categoria->nombre;
        // Lógica para comprobar si la categoría está en uso antes de borrar
        if ($categoria->productos()->exists()) {
            return redirect()->route('categorias.index')->with('warning', 'No se puede eliminar la categoría "'.$nombreCategoria.'" porque tiene productos asociados.');
        }
        $categoria->delete();
        return redirect()->route('categorias.index')->with('mensaje', 'Categoría "' . $nombreCategoria . '" eliminada.');
    }

    public function listar(Request $request)
    {
        $texto = $request->input('texto');
        $registros = Categoria::where('nombre', 'like', '%' . $texto . '%')->paginate(2);
        return view('categoria.list', compact('registros'));
    }

    public function buscarPublico(Request $request, Empresa $empresa)
    {
        $request->validate(['q' => 'required|string|min:1']);
        $termino = $request->input('q');

        $categorias = $empresa->categorias()
                            ->where('nombre', 'LIKE', '%' . $termino . '%')
                            ->select('id', 'nombre')
                            ->limit(10)
                            ->get();

        return response()->json($categorias);
    }
}
