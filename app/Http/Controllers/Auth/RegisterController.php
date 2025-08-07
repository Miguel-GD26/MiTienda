<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Notifications\VerificationCodeNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;


class RegisterController extends Controller
{
    // public function showRegistroForm()
    // {
    //     return view('autenticacion.registro');
    // }

    // public function registrar(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'tipo_usuario' => ['required', 'in:cliente,empresa'],
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
    //         'password' => ['required', 'confirmed', Rules\Password::defaults()],
    //         'empresa_nombre' => ['required_if:tipo_usuario,empresa', 'nullable', 'string', 'max:255', 'unique:empresas,nombre'],
    //         'empresa_telefono_whatsapp' => ['required_if:tipo_usuario,empresa', 'nullable', 'string', 'digits:9'],
    //         'empresa_rubro' => ['required_if:tipo_usuario,empresa', 'nullable', 'string', 'max:255'],
    //         'cliente_telefono' => ['required_if:tipo_usuario,cliente', 'nullable', 'string', 'digits:9'],
    //     ], [
    //         // Mensajes de error personalizados
    //         'tipo_usuario.required' => 'Debes seleccionar si quieres comprar o vender.',
    //         'name.required' => 'Tu nombre completo es obligatorio.',
    //         'email.required' => 'El correo electrónico es obligatorio.',
    //         'email.unique' => 'Este correo electrónico ya está en uso. Por favor, inicia sesión.',
    //         'password.required' => 'La contraseña es obligatoria.',
    //         'password.confirmed' => 'Las contraseñas no coinciden.',
    //         'empresa_nombre.required_if' => 'El nombre de la empresa es obligatorio para vender.',
    //         'empresa_nombre.unique' => 'El nombre de esta empresa ya está registrado.',
    //         'empresa_telefono_whatsapp.required_if' => 'El WhatsApp de la empresa es obligatorio.',
    //         'empresa_rubro.required_if' => 'El rubro de la empresa es obligatorio.',
    //         'cliente_telefono.required_if' => 'Tu teléfono es obligatorio para registrarte como cliente.',
    //         '*.digits' => 'El teléfono debe tener 9 dígitos.'
    //     ]);

    //     try {

    //         DB::beginTransaction();
    //         $verificationCode = random_int(100000, 999999);

    //         $request->session()->put('registration_data', [
    //             'data' => $validatedData,
    //             'verification_code' => $verificationCode,
    //             'code_expires_at' => now()->addMinutes(10),
    //         ]);
            
    //         Notification::route('mail', $validatedData['email'])
    //                     ->notify(new VerificationCodeNotification($verificationCode, $validatedData['name']));

    //         DB::commit();

    //     } catch (\Exception $e) {
    //         DB::rollBack(); 
    //         dd($e);
    //         \Log::error('Error en la preparación del registro: ' . $e->getMessage());
    //         return back()->with('error', 'Ocurrió un error al preparar el registro. Por favor, inténtelo de nuevo.')->withInput();
    //     }

    //     return redirect()->route('verification.code.form')
    //                      ->with('email', $request->email)
    //                      ->with('mensaje_registro', '¡Registro casi listo! Te hemos enviado un código a tu correo.');
    // }
}