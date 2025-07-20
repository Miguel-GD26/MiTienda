<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 

class RoleController extends Controller
{
    use AuthorizesRequests;
    
    public function index(Request $request)
    {
        $this->authorize('rol-list');
        $user = Auth::user(); 
        
        $texto = $request->input('texto');
        $query = Role::with('permissions'); 

        if (!$user->hasRole('super_admin')) {
            $query->where('name', '!=', 'super_admin');
        }
        
        if ($texto) {
            $query->where('name', 'like', '%' . $texto . '%');
        }

        $registros = $query->orderBy('id', 'asc')->paginate(9); 

        return view('role.index', compact('registros', 'texto'));
    }

    public function create()
    {
        $this->authorize('rol-create'); 
        $permissions = Permission::all()->sortBy('name');
        return view('role.action', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('rol-create'); 
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array',
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

    public function show(string $id)
    {
        return redirect()->route('roles.index');
    }

    public function edit(Role $role) 
    {
        $this->authorize('rol-edit'); 
        
        if (!auth()->user()->hasRole('super_admin') && $role->name === 'super_admin') {
            abort(403, 'No tienes permiso para editar este rol.');
        }
        
        $permissions = Permission::all()->sortBy('name');
        return view('role.action', ['registro' => $role, 'permissions' => $permissions]);
    }

    public function update(Request $request, Role $role) 
    {
        $this->authorize('rol-edit');
        
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->permissions ?? []);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al actualizar el rol.')->withInput();
        }

        return redirect()->route('roles.index')->with('mensaje', 'Rol '.$role->name. ' actualizado correctamente');
    }

    public function destroy(Role $role)
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