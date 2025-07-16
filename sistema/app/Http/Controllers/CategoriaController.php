<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $texto=$request->input('texto');
        $registros= Categoria::where('nombre','like','%'.$texto.'%')->paginate(2);
        return view('categoria.index', compact('registros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view ('categoria.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $registro = new Categoria();
        $registro -> nombre= $request->input('nombre');
        $registro ->descripcion=$request->input('descripcion');
        $registro ->save();
        return redirect()->route('categorias.index')
        ->with('mensaje','Categoria registrada satisfactoriamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $registros = Categoria::findOrFail($id);
        return view('categoria.edit',compact('registros'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $registro = Categoria::findOrFail($id);
        $registro->nombre=$request->input('nombre');
        $registro->descripcion=$request->input('descripcion');
        $registro->save();
        return redirect()->route('categorias.index')
        ->with('mensaje','Registro actualizado satisfactoriamente');


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $registro = Categoria::findOrFail($id);
        $registro->delete();
        return redirect()->route('categorias.index')
        ->with('mensaje','Eliminado satisfactoriamente');

    }

    public function listar(Request $request)
{
    
    $texto = $request->input('texto');
    $registros = Categoria::where('nombre', 'like', '%'.$texto.'%')->paginate(2);
    return view('categoria.list', compact('registros'));
}
}
