<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Cliente;
use App\Notifications\VerificationCodeNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class VerificationCodeController extends Controller
{
    public function showForm()
    {
        if (!session('email')) {
            return redirect()->route('registro');
        }
        return view('autenticacion.verify-code');
    }

    public function verify(Request $request)
    {
        $request->validate(['verification_code' => 'required|numeric|digits:6']);

        $registrationData = $request->session()->get('registration_data');

        if (!$registrationData || now()->gt($registrationData['code_expires_at'])) {
            $request->session()->forget('registration_data');
            return back()->withInput()->withErrors(['verification_code' => 'El proceso de verificación ha expirado. Por favor, regístrate de nuevo.']);
        }
        if ($registrationData['verification_code'] != $request->verification_code) {
            return back()->withInput()->withErrors(['verification_code' => 'El código de verificación es inválido.']);
        }

        $user = null;
        try {
            DB::beginTransaction();

            $data = $registrationData['data'];
            
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'activo' => 1,
                'email_verified_at' => now(),
            ]);

            if ($data['tipo_usuario'] === 'empresa') {
                $empresa = Empresa::create([
                    'nombre' => $data['empresa_nombre'],
                    'telefono_whatsapp' => $data['empresa_telefono_whatsapp'],
                    'rubro' => $data['empresa_rubro'],
                    'slug' => Str::slug($data['empresa_nombre']),
                ]);
                $user->empresa_id = $empresa->id;
                $user->save();
                $user->assignRole('admin');
            } else {
                Cliente::create([
                    'nombre' => $data['name'],
                    'telefono' => $data['cliente_telefono'],
                    'user_id' => $user->id,
                ]);
                $user->assignRole('cliente');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear el usuario tras verificación: ' . $e->getMessage());
            return redirect()->route('registro')->with('error', 'Ocurrió un error al finalizar tu registro. Por favor, inténtalo de nuevo.');
        }

        $request->session()->forget('registration_data');
        $request->session()->forget('email');
        
        Auth::login($user);

        if ($user->hasRole('admin')) {
            return redirect()->route('dashboard')->with('mensaje', '¡Cuenta verificada! Bienvenido.');
        } else {
            return redirect()->route('welcome')->with('mensaje', '¡Cuenta verificada! Ya puedes empezar a comprar.');
        }
    }

    public function resend(Request $request)
    {
        $registrationData = $request->session()->get('registration_data');

        if (!$registrationData) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Tu sesión ha expirado. Por favor, regístrate de nuevo.'], 400);
            }
            return redirect()->route('registro')->with('warning', 'Tu sesión ha expirado. Por favor, regístrate de nuevo.');
        }

        $newCode = random_int(100000, 999999);
        $registrationData['verification_code'] = $newCode;
        $registrationData['code_expires_at'] = now()->addMinutes(10);
        $request->session()->put('registration_data', $registrationData);

        try {
            $userData = $registrationData['data'];
            
            Notification::route('mail', $userData['email'])
                        ->notify(new VerificationCodeNotification($newCode, $userData['name']));
                        
        } catch (\Exception $e) {
            \Log::error('Error al reenviar código: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['message' => 'No se pudo enviar el email. Intenta más tarde.'], 500);
            }
            return back()->with('error', 'No se pudo enviar el email. Intenta más tarde.');
        }
        
        if ($request->ajax()) {
            return response()->json(['mensaje' => 'Se ha enviado un nuevo código de verificación.']);
        }
        return back()->with('mensaje', 'Se ha enviado un nuevo código de verificación a tu correo.');
    }
}