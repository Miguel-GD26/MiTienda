<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Empresa;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use AuthorizesRequests;
    
    // app/Http/Controllers/UserController.php
    public function index()
    {
        $this->authorize('user-list');
        return view('usuario.index'); // Solo esto es necesario
    }

    // public function index(Request $request)
    // {
    //     $this->authorize('user-list');
    //     $user = Auth::user();
    //     $texto = $request->input('texto');

    //     $query = User::with('roles', 'empresa');

    //     if (!$user->hasRole('super_admin')) {
    //         $query->where('empresa_id', $user->empresa_id);
    //     }

    //     if ($texto) {
    //         $query->where(function($q) use ($texto) {
    //             $q->where('name', 'like', "%{$texto}%")
    //               ->orWhere('email', 'like', "%{$texto}%");
    //         });
    //     }

    //     $registros = $query->orderBy('id', 'asc')->paginate(10);
        
    //     return view('usuario.index', compact('registros', 'texto'));
    // }


    // public function create()
    // {
    //     $this->authorize('user-create');
    //     $user = Auth::user();

    //     $rolesQuery = Role::query();
    //     if (!$user->hasRole('super_admin')) {
    //         $rolesQuery->where('name', '!=', 'super_admin');
    //     }
    //     $roles = $rolesQuery->get();
        
    //     $empresas = $user->hasRole('super_admin') ? Empresa::all() : collect();

    //     return view('usuario.action', compact('roles', 'empresas'));
    // }


    // public function store(Request $request)
    // {
    //     $this->authorize('user-create');

    //     $rules = [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8|confirmed',
    //         'role' => ['required', Rule::exists('roles', 'name')],
    //     ];
    //     if (auth()->user()->hasRole('super_admin') && in_array($request->input('role'), ['admin', 'vendedor', 'repartidor'])) {
    //         if ($request->input('empresa_id') === 'crear_nueva') {
    //             $rules['empresa_nombre'] = 'required|string|max:255|unique:empresas,nombre';
    //             $rules['empresa_rubro'] = 'nullable|string|max:255';
    //             $rules['empresa_telefono_whatsapp'] = 'nullable|string|max:20';
    //         } else {
    //             $rules['empresa_id'] = 'required|exists:empresas,id';
    //         }
    //     }
    //     $validatedData = $request->validate($rules);

    //     DB::beginTransaction();
    //     try {
    //         $empresaId = null;
    //         if (auth()->user()->hasRole('super_admin')) {
    //             if ($request->input('empresa_id') === 'crear_nueva') {
    //                 $empresa = Empresa::create([
    //                     'nombre' => $validatedData['empresa_nombre'],
    //                     'slug' => Str::slug($validatedData['empresa_nombre']),
    //                     'rubro' => $request->input('empresa_rubro'),
    //                     'telefono_whatsapp' => $request->input('empresa_telefono_whatsapp'),
    //                 ]);
    //                 $empresaId = $empresa->id;
    //             } else {
    //                 $empresaId = $request->input('empresa_id');
    //             }
    //         } else {
    //             $empresaId = auth()->user()->empresa_id;
    //         }

    //         $user = User::create([
    //             'name' => $validatedData['name'],
    //             'email' => $validatedData['email'],
    //             'password' => Hash::make($validatedData['password']),
    //             'activo' => true,
    //             'empresa_id' => $empresaId,
    //         ]);

    //         $user->assignRole($validatedData['role']);

    //         if ($validatedData['role'] === 'cliente') {
    //             Cliente::create([
    //                 'nombre' => $user->name,
    //                 'user_id' => $user->id,
    //             ]);
    //         }

    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Ocurrió un error: ' . $e->getMessage())->withInput();
    //     }

    //     return redirect()->route('usuarios.index')->with('mensaje', 'Usuario ' . $user->name . ' ha sido creado correctamente.');
    // }


    // public function edit(User $usuario)
    // {
    //     $this->authorize('user-edit', $usuario);
    //     $user = Auth::user();

    //     $rolesQuery = Role::query();
    //     if (!$user->hasRole('super_admin')) {
    //         $rolesQuery->where('name', '!=', 'super_admin');
    //     }
    //     $roles = $rolesQuery->get();

    //     $empresas = $user->hasRole('super_admin') ? Empresa::all() : collect();
    //     $empresaDelPerfil = $usuario->empresa;
        
    //     return view('usuario.action', ['registro' => $usuario, 'roles' => $roles, 'empresas' => $empresas, 'empresaDelPerfil' => $empresaDelPerfil]);
    // }

    

    // public function update(Request $request, User $usuario)
    // {
    //     $this->authorize('user-edit', $usuario);
        
    //     $rules = [
    //         'name' => 'required|string|max:255',
    //         'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
    //         'password' => 'nullable|string|min:8|confirmed',
    //         'role' => ['required', Rule::exists('roles', 'name')],
    //     ];
    //     if (auth()->user()->hasRole('super_admin') && $usuario->empresa_id && $request->has('empresa_nombre')) {
    //         $rules['empresa_nombre'] = ['required', 'string', 'max:255', Rule::unique('empresas', 'nombre')->ignore($usuario->empresa_id)];
    //     }
    //     $validatedData = $request->validate($rules);
        
    //     DB::beginTransaction();
    //     try {
    //         $usuario->name = $validatedData['name'];
    //         $usuario->email = $validatedData['email'];
    //         if ($request->filled('password')) {
    //             $usuario->password = Hash::make($validatedData['password']);
    //         }
    //         $usuario->activo = $request->has('activo') ? 1 : 0;
            
    //         $usuario->save();
    //         $usuario->syncRoles($validatedData['role']);

    //         if ($validatedData['role'] === 'cliente' && !$usuario->cliente) {
    //             Cliente::create([
    //                 'nombre' => $usuario->name,
    //                 'user_id' => $usuario->id,
    //             ]);
    //         }

    //         if (auth()->user()->hasRole('super_admin') && $usuario->empresa && $request->has('empresa_nombre')) {
    //             $usuario->empresa->update([
    //                 'nombre' => $request->input('empresa_nombre'),
    //                 'rubro' => $request->input('empresa_rubro'),
    //                 'telefono_whatsapp' => $request->input('empresa_telefono_whatsapp'),
    //             ]);
    //         }

    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Ocurrió un error: ' . $e->getMessage())->withInput();
    //     }

    //     return redirect()->route('usuarios.index')->with('mensaje', 'Usuario ' . $usuario->name . ' actualizado correctamente.');
    // }

    
    // public function destroy(User $usuario)
    // {
    //     $this->authorize('user-delete', $usuario);

    //     DB::beginTransaction();
    //     try {
    //         $nombreUsuario = $usuario->name;
    //         $empresa = $usuario->empresa;

    //         $usuario->delete();

    //         if ($empresa) {
                
    //             $otrosAdmins = User::where('empresa_id', $empresa->id)
    //                                 ->where('id', '!=', $usuario->id) 
    //                                 ->exists();

    //             if (!$otrosAdmins) {
                    
    //                 if ($empresa->logo_url) {
    //                     cloudinary()->uploadApi()->destroy($empresa->logo_url);
    //                 }

    //                 foreach ($empresa->productos as $producto) {
    //                     if ($producto->imagen_url) {
    //                         cloudinary()->uploadApi()->destroy($producto->imagen_url);
    //                     }
    //                 }
                    
    //                 $empresa->delete();
    //             }
    //         }

    //         DB::commit();

    //         return redirect()->route('usuarios.index')->with('mensaje', 'Usuario ' . $nombreUsuario . ' y sus datos asociados han sido eliminados correctamente.');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->route('usuarios.index')->with('error', 'Ocurrió un error al eliminar el usuario: ' . $e->getMessage());
    //     }
    // }
    
    // public function toggleStatus(User $usuario)
    // {
    //     $this->authorize('user-activate', $usuario);
    //     $usuario->activo = !$usuario->activo;
    //     $usuario->save();
    //     return redirect()->route('usuarios.index')->with('mensaje', 'Estado del usuario actualizado correctamente.');
    // }
}