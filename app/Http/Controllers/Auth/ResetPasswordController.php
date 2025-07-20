<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ResetPasswordNotification;

class ResetPasswordController extends Controller
{
    public function showRequestForm()
    {
        return view('autenticacion.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        try {
            $user->notify(new ResetPasswordNotification($token));
        } catch (\Exception $e) {
            \Log::error('Error al enviar email de reseteo: ' . $e->getMessage());
            return back()->with('error', 'No se pudo enviar el enlace de recuperación. Por favor, inténtelo de nuevo más tarde.');
        }

        return back()->with('mensaje', 'Te hemos enviado un enlace de recuperación a tu correo.');
    }

    public function showResetForm($token)
    {
        return view('autenticacion.reset', compact('token'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        $reset = DB::table('password_reset_tokens')->where('token', $request->token)->first();

        $tokenCreatedAt = \Carbon\Carbon::parse($reset->created_at);
        if (!$reset || $reset->email !== $request->email || $tokenCreatedAt->addMinutes(config('auth.passwords.users.expire'))->isPast()) {
            return back()->withErrors(['email' => 'El token de restablecimiento es inválido o ha expirado.']);
        }

        User::where('email', $request->email)->update(['password' => Hash::make($request->input('password'))]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('mensaje', 'Tu contraseña ha sido restablecida con éxito.');
    }
}