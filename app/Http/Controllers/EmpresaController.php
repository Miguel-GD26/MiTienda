<?php

namespace App\Http\Controllers;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $texto = $request->input('texto');

        $empresas = Empresa::query()
            ->when($texto, function ($query, $texto) {
                return $query->where('nombre', 'LIKE', '%' . $texto . '%')
                             ->orWhere('slug', 'LIKE', '%' . $texto . '%');
            })
            ->latest() 
            ->paginate(10); 
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
