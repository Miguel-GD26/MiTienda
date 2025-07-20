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
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function edit()
    {
        $user = Auth::user()->load('empresa', 'cliente'); 
        $isSocialUser = !is_null($user->provider_name);

        if ($user->hasRole(['super_admin', 'admin'])) {
            return view('autenticacion.perfil', ['registro' => $user, 'empresa' => $user->empresa, 'isSocialUser' => $isSocialUser]);
        }
        
        if ($user->hasRole('cliente')) {
            return view('autenticacion.cliente', ['registro' => $user, 'cliente' => $user->cliente, 'isSocialUser' => $isSocialUser]);
        }

        return redirect()->route('dashboard')->with('error', 'No tienes un perfil de usuario válido para editar.');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // --- VALIDACIÓN DE DATOS ---
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ];

        if (is_null($user->provider_name)) {
            $rules['password'] = ['nullable', 'confirmed', Password::defaults()]; // Usamos las reglas por defecto de Laravel.
        }

        
        // Reglas para admin/super_admin con empresa
        if ($user->hasRole(['super_admin', 'admin']) && $user->empresa) {
            $rules['empresa_nombre'] = ['required', 'string', 'max:255', Rule::unique('empresas', 'nombre')->ignore($user->empresa_id)];
            $rules['empresa_rubro'] = 'nullable|string|max:255';
            $rules['empresa_telefono_whatsapp'] = 'nullable|string|max:20';
            $rules['empresa_logo'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        }
        // Reglas para cliente
        elseif ($user->hasRole('cliente')) {
            $rules['cliente_telefono'] = 'required|string|max:9';
        }
        
        $validatedData = $request->validate($rules);

        // --- ACTUALIZACIÓN DE DATOS ---
        DB::beginTransaction();
        try {
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            
            
            if ($request->filled('password') && is_null($user->provider_name)) {
                $user->password = Hash::make($validatedData['password']);
            }
            $user->save();
            
            // 2. Actualizar la Empresa si es un Admin/SuperAdmin
            if ($user->hasRole(['super_admin', 'admin']) && $user->empresa) {
                $empresa = $user->empresa; 
                
                $empresaData = [
                    'nombre' => $validatedData['empresa_nombre'],
                    'rubro' => $request->input('empresa_rubro'),
                    'telefono_whatsapp' => $request->input('empresa_telefono_whatsapp'),
                ];
                
                if ($request->hasFile('empresa_logo')) {
                    if ($empresa->logo_url) {
                        cloudinary()->uploadApi()->destroy($empresa->logo_url);
                    }
                    
                    $uploadedFile = cloudinary()->uploadApi()->upload($request->file('empresa_logo')->getRealPath(), [
                        'folder' => 'logos_empresa'
                    ]);
                    $empresaData['logo_url'] = $uploadedFile['public_id'];
                }

                $empresa->update($empresaData); 
            } 
            
            elseif ($user->hasRole('cliente') && $user->cliente) {
                $user->cliente->update([
                    'nombre' => $validatedData['name'], 
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