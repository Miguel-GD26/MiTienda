<?php

namespace App\Http\Controllers;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtener el término de búsqueda de la URL (?texto=valor)
        $texto = $request->input('texto');

        // 2. Iniciar la consulta al modelo Empresa
        // Usamos when() para aplicar el filtro solo si $texto tiene un valor.
        // Esto hace el código más limpio que un if/else.
        $empresas = Empresa::query()
            ->when($texto, function ($query, $texto) {
                // Si hay texto, buscar en la columna 'nombre' o 'slug'
                return $query->where('nombre', 'LIKE', '%' . $texto . '%')
                             ->orWhere('slug', 'LIKE', '%' . $texto . '%');
            })
            ->latest() // Ordena por los más recientes primero
            ->paginate(10); // Pagina los resultados

        // 3. Devolver la vista con los datos (filtrados o no) y el término de búsqueda
        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        return view('empresas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $slug = Str::slug($request->nombre);

        $empresa = Empresa::create([
            'nombre' => $request->nombre,
            'slug' => $slug,
        ]);

        return redirect()->route('empresas.index')->with('mensaje', 'Empresa registrada');
    }

    public function edit(Empresa $empresa)
    {
        return view('empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
        ]);

        $empresa->update([
            'nombre' => $request->nombre,
            'slug' => Str::slug($request->nombre),
        ]);

        return redirect()->route('empresas.index')->with('mensaje', 'Empresa actualizada');
    }

    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return redirect()->route('empresas.index')->with('mensaje', 'Empresa eliminada');
    }
}
