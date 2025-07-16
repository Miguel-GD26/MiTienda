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
    

    public function index(Request $request)
    {
        $this->authorize('user-list');
        $user = Auth::user();
        $texto = $request->input('texto');

        $query = User::with('roles', 'empresa');

        // Filtrar por empresa si el usuario no es Super Admin
        if (!$user->hasRole('super_admin')) {
            $query->where('empresa_id', $user->empresa_id);
        }

        // Filtrar por texto de búsqueda
        if ($texto) {
            $query->where(function($q) use ($texto) {
                $q->where('name', 'like', "%{$texto}%")
                  ->orWhere('email', 'like', "%{$texto}%");
            });
        }

        $registros = $query->orderBy('id', 'asc')->paginate(10);
        
        return view('usuario.index', compact('registros', 'texto'));
    }


    public function create()
    {
        $this->authorize('user-create');
        $user = Auth::user();

        $rolesQuery = Role::query();
        // Super Admin puede ver todos los roles, los demás no pueden asignar 'super_admin'
        if (!$user->hasRole('super_admin')) {
            $rolesQuery->where('name', '!=', 'super_admin');
        }
        $roles = $rolesQuery->get();
        
        // Solo Super Admin puede asignar empresas
        $empresas = $user->hasRole('super_admin') ? Empresa::all() : collect();

        return view('usuario.action', compact('roles', 'empresas'));
    }


    public function store(Request $request)
    {
        $this->authorize('user-create');

        // --- VALIDACIÓN ---
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::exists('roles', 'name')],
        ];
        if (auth()->user()->hasRole('super_admin') && in_array($request->input('role'), ['admin', 'vendedor', 'repartidor'])) {
            if ($request->input('empresa_id') === 'crear_nueva') {
                $rules['empresa_nombre'] = 'required|string|max:255|unique:empresas,nombre';
                $rules['empresa_rubro'] = 'nullable|string|max:255';
                $rules['empresa_telefono_whatsapp'] = 'nullable|string|max:20';
            } else {
                $rules['empresa_id'] = 'required|exists:empresas,id';
            }
        }
        $validatedData = $request->validate($rules);

        // --- CREACIÓN (TRANSACCIÓN) ---
        DB::beginTransaction();
        try {
            $empresaId = null;
            if (auth()->user()->hasRole('super_admin')) {
                if ($request->input('empresa_id') === 'crear_nueva') {
                    $empresa = Empresa::create([
                        'nombre' => $validatedData['empresa_nombre'],
                        'slug' => Str::slug($validatedData['empresa_nombre']),
                        'rubro' => $request->input('empresa_rubro'),
                        'telefono_whatsapp' => $request->input('empresa_telefono_whatsapp'),
                    ]);
                    $empresaId = $empresa->id;
                } else {
                    $empresaId = $request->input('empresa_id');
                }
            } else {
                $empresaId = auth()->user()->empresa_id;
            }

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'activo' => true,
                'empresa_id' => $empresaId,
            ]);

            $user->assignRole($validatedData['role']);

            if ($validatedData['role'] === 'cliente') {
                Cliente::create([
                    'nombre' => $user->name,
                    'user_id' => $user->id,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('usuarios.index')->with('mensaje', 'Usuario ' . $user->name . ' ha sido creado correctamente.');
    }


    public function edit(User $usuario)
    {
        $this->authorize('user-edit', $usuario);
        $user = Auth::user();

        $rolesQuery = Role::query();
        if (!$user->hasRole('super_admin')) {
            $rolesQuery->where('name', '!=', 'super_admin');
        }
        $roles = $rolesQuery->get();

        $empresas = $user->hasRole('super_admin') ? Empresa::all() : collect();
        $empresaDelPerfil = $usuario->empresa;
        
        return view('usuario.action', ['registro' => $usuario, 'roles' => $roles, 'empresas' => $empresas, 'empresaDelPerfil' => $empresaDelPerfil]);
    }

    

    public function update(Request $request, User $usuario)
    {
        $this->authorize('user-edit', $usuario);
        
        // --- VALIDACIÓN ---
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::exists('roles', 'name')],
        ];
        if (auth()->user()->hasRole('super_admin') && $usuario->empresa_id && $request->has('empresa_nombre')) {
            $rules['empresa_nombre'] = ['required', 'string', 'max:255', Rule::unique('empresas', 'nombre')->ignore($usuario->empresa_id)];
        }
        $validatedData = $request->validate($rules);
        
        // --- ACTUALIZACIÓN (TRANSACCIÓN) ---
        DB::beginTransaction();
        try {
            $usuario->name = $validatedData['name'];
            $usuario->email = $validatedData['email'];
            if ($request->filled('password')) {
                $usuario->password = Hash::make($validatedData['password']);
            }
            $usuario->activo = $request->has('activo') ? 1 : 0;
            
            // La asignación de empresa no se cambia en la actualización
            $usuario->save();
            $usuario->syncRoles($validatedData['role']);

            if ($validatedData['role'] === 'cliente' && !$usuario->cliente) {
                Cliente::create([
                    'nombre' => $usuario->name,
                    'user_id' => $usuario->id,
                ]);
            }

            if (auth()->user()->hasRole('super_admin') && $usuario->empresa && $request->has('empresa_nombre')) {
                $usuario->empresa->update([
                    'nombre' => $request->input('empresa_nombre'),
                    'rubro' => $request->input('empresa_rubro'),
                    'telefono_whatsapp' => $request->input('empresa_telefono_whatsapp'),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('usuarios.index')->with('mensaje', 'Usuario ' . $usuario->name . ' actualizado correctamente.');
    }

    /**
     * Elimina un usuario de la base de datos.
     */
    // public function destroy(User $usuario)
    // {
    //     $this->authorize('user-delete', $usuario);
    //     $nombreUsuario = $usuario->name;
    //     $usuario->delete();
    //     return redirect()->route('usuarios.index')->with('mensaje', $nombreUsuario . ' eliminado correctamente.');
    // }

    // app/Http/Controllers/UserController.php

    public function destroy(User $usuario)
    {
        $this->authorize('user-delete', $usuario);

        // Es crucial realizar esto dentro de una transacción para asegurar que todo
        // se complete correctamente o no se haga nada si algo falla.
        DB::beginTransaction();
        try {
            $nombreUsuario = $usuario->name;
            $empresa = $usuario->empresa; // Guardamos la referencia a la empresa antes de borrar el usuario

            // 1. Eliminar al usuario
            $usuario->delete();

            // 2. Comprobar si el usuario tenía una empresa asociada
            if ($empresa) {
                
                // 2a. Comprobar si esta empresa tiene otros usuarios administradores.
                // Si es así, NO la borramos. Si es el último, SÍ la borramos.
                $otrosAdmins = User::where('empresa_id', $empresa->id)
                                    ->where('id', '!=', $usuario->id) // Excluimos al usuario que estamos borrando
                                    ->exists();

                if (!$otrosAdmins) {
                    // Es el último (o único) administrador, así que procedemos a borrar la empresa.

                    // 2b. Si la empresa tiene un logo, lo eliminamos de Cloudinary
                    if ($empresa->logo_url) {
                        cloudinary()->uploadApi()->destroy($empresa->logo_url);
                    }

                    // 2c. Aquí también deberías eliminar otros activos de la empresa,
                    // como productos y sus imágenes.
                    foreach ($empresa->productos as $producto) {
                        if ($producto->imagen_url) {
                            cloudinary()->uploadApi()->destroy($producto->imagen_url);
                        }
                    }
                    
                    // 2d. Finalmente, eliminamos la empresa de la base de datos.
                    // Esto (si tienes bien configuradas las claves foráneas con onDelete('cascade'))
                    // podría eliminar automáticamente productos, categorías, etc.
                    $empresa->delete();
                }
            }

            DB::commit(); // Confirmamos todos los cambios

            return redirect()->route('usuarios.index')->with('mensaje', 'Usuario ' . $nombreUsuario . ' y sus datos asociados han sido eliminados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack(); // Revertimos todo si algo salió mal
            return redirect()->route('usuarios.index')->with('error', 'Ocurrió un error al eliminar el usuario: ' . $e->getMessage());
        }
    }
    /**
     * Cambia el estado (activo/inactivo) de un usuario.
     */
    public function toggleStatus(User $usuario)
    {
        $this->authorize('user-activate', $usuario);
        $usuario->activo = !$usuario->activo;
        $usuario->save();
        return redirect()->route('usuarios.index')->with('mensaje', 'Estado del usuario actualizado correctamente.');
    }
}