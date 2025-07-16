<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Cliente; // Asegúrate de importar el modelo Cliente
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Muestra el formulario de registro.
     */
    public function showRegistroForm()
    {
        return view('autenticacion.registro');
    }

    /**
     * Procesa la petición de registro.
     * He cambiado el nombre del método a 'store' para seguir convenciones.
     * Asegúrate de que tu ruta apunte a este método.
     */
    public function registrar (Request $request)
    {
        // --- 1. VALIDACIÓN ---
        $request->validate([
            'tipo_usuario' => ['required', 'in:cliente,empresa'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($request->input('tipo_usuario') === 'empresa') {
            $request->validate([
                'empresa_nombre' => ['required', 'string', 'max:255', 'unique:empresas,nombre'],
                'empresa_telefono_whatsapp' => ['required', 'string', 'max:20'],
                'empresa_rubro' => ['required', 'string', 'max:255'],
            ]);
        }

        // --- 2. CREACIÓN (USANDO TRANSACCIÓN) ---
        try {
            DB::beginTransaction();

            // Creamos el usuario con los datos comunes
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'activo' => 1,
            ]);

            // Asignamos rol y creamos datos adicionales según el tipo
            if ($request->input('tipo_usuario') === 'empresa') {
                $slug = Str::slug($request->input('empresa_nombre'));
                $empresa = Empresa::create([
                    'nombre' => $request->input('empresa_nombre'),
                    'telefono_whatsapp' => $request->input('empresa_telefono_whatsapp'),
                    'rubro' => $request->input('empresa_rubro'),
                    'slug' => $slug,
                ]);
                $user->empresa_id = $empresa->id;
                $user->save();
                $user->assignRole('admin');
                
            } else { // Si es 'cliente'
                // ===============================================
                //          LÓGICA ACTUALIZADA PARA CLIENTES
                // ===============================================
                // Creamos el registro en la tabla `clientes` y lo vinculamos al User
                Cliente::create([
                    'nombre' => $request->input('name'),
                    'telefono' => $request->input('cliente_telefono'), // Campo opcional
                    'user_id' => $user->id, // Vínculo crucial con la cuenta de usuario
                ]);

                // Asignamos el rol de 'cliente'
                $user->assignRole('cliente');
                // ===============================================
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Durante el desarrollo, es útil ver el error real.
            // En producción, deberías loguearlo y mostrar un mensaje amigable.
            // dd($e); 
            
            return back()->with('error', 'Ocurrió un error durante el registro. Por favor, inténtelo de nuevo.')
                         ->withInput();
        }

        // --- 3. LOGIN Y REDIRECCIÓN ---
        Auth::login($user);

        if ($user->hasRole('admin')) {
            return redirect()->route('dashboard')->with('mensaje', '¡Registro de empresa completado! Bienvenido.');
        } else {
            return redirect()->route('welcome')->with('mensaje', '¡Registro completado! Ya puedes empezar a comprar.');
        }
    }
}