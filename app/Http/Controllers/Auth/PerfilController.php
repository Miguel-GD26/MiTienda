<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class PerfilController extends Controller
{
    /**
     * Muestra el formulario de perfil del usuario autenticado.
     * Redirige a la vista correcta según el rol.
     */
    public function edit()
    {
        $user = Auth::user()->load('empresa', 'cliente'); // 1. Carga las relaciones para eficiencia

        if ($user->hasRole(['super_admin', 'admin'])) {
            // Un admin ve su perfil dentro del panel de administración.
            // La vista recibe el 'user' y su 'empresa' a través de la relación.
            return view('autenticacion.perfil', ['registro' => $user, 'empresa' => $user->empresa]);
        }
        
        if ($user->hasRole('cliente')) {
            // Un cliente ve su perfil en una vista pública o de cliente.
            // La vista recibe el 'user' y su perfil 'cliente' a través de la relación.
            return view('autenticacion.cliente', ['registro' => $user, 'cliente' => $user->cliente]);
        }

        // Si el usuario no tiene un rol con perfil editable, lo redirigimos.
        return redirect()->route('dashboard')->with('error', 'No tienes un perfil de usuario válido para editar.');
    }

    /**
     * Actualiza el perfil del usuario autenticado.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // --- VALIDACIÓN DE DATOS ---
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ];
        
        // Reglas para admin/super_admin con empresa
        if ($user->hasRole(['super_admin', 'admin']) && $user->empresa) {
            $rules['empresa_nombre'] = ['required', 'string', 'max:255', Rule::unique('empresas', 'nombre')->ignore($user->empresa_id)];
            $rules['empresa_rubro'] = 'nullable|string|max:255';
            $rules['empresa_telefono_whatsapp'] = 'nullable|string|max:20';
            $rules['empresa_logo'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'; // La regla ya estaba, ¡perfecto!
        }
        // Reglas para cliente
        elseif ($user->hasRole('cliente')) {
            $rules['cliente_telefono'] = 'nullable|string|max:20';
        }
        
        $validatedData = $request->validate($rules);

        // --- ACTUALIZACIÓN DE DATOS ---
        DB::beginTransaction();
        try {
            // 1. Actualizar el modelo User (común para todos)
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            if ($request->filled('password')) {
                $user->password = Hash::make($validatedData['password']);
            }
            $user->save();

            // 2. Actualizar la Empresa si es un Admin/SuperAdmin
            if ($user->hasRole(['super_admin', 'admin']) && $user->empresa) {
                $empresa = $user->empresa; // Obtenemos el modelo de la empresa
                
                $empresaData = [
                    'nombre' => $validatedData['empresa_nombre'],
                    'rubro' => $request->input('empresa_rubro'),
                    'telefono_whatsapp' => $request->input('empresa_telefono_whatsapp'),
                ];
                
                // --- LÓGICA DEL LOGO (la parte importante que faltaba) ---
                if ($request->hasFile('empresa_logo')) {
                    // Si ya existe un logo, lo eliminamos de Cloudinary
                    if ($empresa->logo_url) {
                        cloudinary()->uploadApi()->destroy($empresa->logo_url);
                    }
                    
                    // Subimos el nuevo logo a una carpeta 'logos_empresa'
                    $uploadedFile = cloudinary()->uploadApi()->upload($request->file('empresa_logo')->getRealPath(), [
                        'folder' => 'logos_empresa'
                    ]);
                    // Guardamos el public_id que nos devuelve Cloudinary
                    $empresaData['logo_url'] = $uploadedFile['public_id'];
                }

                $empresa->update($empresaData); // Actualizamos la empresa con todos los datos
            } 
            // 3. Actualizar el Cliente si es un Cliente
            elseif ($user->hasRole('cliente') && $user->cliente) {
                $user->cliente->update([
                    'nombre' => $validatedData['name'], // Sincronizamos el nombre
                    'telefono' => $request->input('cliente_telefono'),
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al actualizar el perfil: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('perfil.edit')->with('mensaje', 'Perfil actualizado correctamente.');
    }
}