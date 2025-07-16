<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth; // Importar el Facade de Auth
use Illuminate\Support\Facades\DB; // Importar para transacciones

class RoleController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('rol-list');
        $user = Auth::user(); // Obtenemos el usuario autenticado
        
        $texto = $request->input('texto');
        $query = Role::with('permissions'); // Eager loading de permisos

        // --- LÓGICA DE ROLES AÑADIDA ---
        // Si el usuario NO es super_admin, excluimos el rol 'super_admin' de la lista.
        if (!$user->hasRole('super_admin')) {
            $query->where('name', '!=', 'super_admin');
        }
        
        // Aplicamos el filtro de búsqueda por texto
        if ($texto) {
            $query->where('name', 'like', '%' . $texto . '%');
        }

        $registros = $query->orderBy('id', 'asc')->paginate(9); // Paginación de 9 para cuadrícula de 3

        return view('role.index', compact('registros', 'texto'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('rol-create'); 
        $permissions = Permission::all()->sortBy('name');
        return view('role.action', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('rol-create'); 
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array', // Los permisos pueden ser opcionales
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->name]);
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al crear el rol.')->withInput();
        }

        return redirect()->route('roles.index')->with('mensaje', 'Rol '.$role->name. ' creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // No se usa, redirigir al index.
        return redirect()->route('roles.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role) // Usamos Route Model Binding
    {
        $this->authorize('rol-edit'); 
        
        // Un admin no puede editar al super_admin
        if (!auth()->user()->hasRole('super_admin') && $role->name === 'super_admin') {
            abort(403, 'No tienes permiso para editar este rol.');
        }
        
        $permissions = Permission::all()->sortBy('name');
        return view('role.action', ['registro' => $role, 'permissions' => $permissions]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role) // Usamos Route Model Binding
    {
        $this->authorize('rol-edit');
        
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $role->update(['name' => $request->name]);
            // Si no se envían permisos, se eliminan todos los asociados. Si se envían, se sincronizan.
            $role->syncPermissions($request->permissions ?? []);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al actualizar el rol.')->withInput();
        }

        return redirect()->route('roles.index')->with('mensaje', 'Rol '.$role->name. ' actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role) // Usamos Route Model Binding
    {
        $this->authorize('rol-delete');
        
        if ($role->name === 'super_admin') {
            return redirect()->route('roles.index')->with('error', 'El rol Super Admin no puede ser eliminado.');
        }

        $nombreRol = $role->name;
        $role->delete();

        return redirect()->route('roles.index')->with('mensaje', 'Rol ' . $nombreRol . ' eliminado correctamente.');
    }
}