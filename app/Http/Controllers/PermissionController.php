<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:permission-list')->only('index');
        $this->middleware('can:permission-create')->only(['create', 'store']);
        $this->middleware('can:permission-edit')->only(['edit', 'update']);
        $this->middleware('can:permission-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $texto = $request->input('texto');
        $registros = Permission::where('name', 'like', "%{$texto}%")
            ->orderBy('name', 'asc')
            ->paginate(12); // Puedes ajustar la paginación

        return view('permission.index', compact('registros', 'texto'));
    }

    public function create()
    {
        return view('permission.action');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            // 'guard_name' => 'nullable|string|max:255', // Si lo tienes en el formulario
        ]);

        Permission::create([
            'name' => $request->input('name'),
            'guard_name' => $request->input('guard_name', 'web') // 'web' por defecto
        ]);

        return redirect()->route('permisos.index')
            ->with('mensaje', 'Permiso "' . $request->input('name') . '" creado satisfactoriamente.');
    }

    // El método show() no suele ser necesario para una lista simple de permisos.
    // public function show(Permission $permission)
    // {
    //     //
    // }

    public function edit(Permission $permiso) // Route Model Binding
    {
        $registro = $permiso;
        return view('permission.action', compact('registro'));
    }

    public function update(Request $request, Permission $permiso)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permiso->id,
            // 'guard_name' => 'nullable|string|max:255', // Si permites editarlo
        ]);

        $permiso->name = $request->input('name');
        if ($request->filled('guard_name')) {
             $permiso->guard_name = $request->input('guard_name');
        }
        $permiso->save();

        return redirect()->route('permisos.index')
            ->with('mensaje', 'Permiso "' . $permiso->name . '" actualizado satisfactoriamente.');
    }

    public function destroy(Permission $permiso)
    {
        $nombrePermiso = $permiso->name;
        $permiso->delete();

        return redirect()->route('permisos.index')
            ->with('mensaje', 'Permiso "' . $nombrePermiso . '" eliminado satisfactoriamente.');
    }
}