<?php

namespace App\Livewire\Auth\PasswordReset;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ResetForm extends Component
{
    public $token;
    public $email;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $this->email = request()->query('email', '');
    }

    protected function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function resetPassword()
    {
        $this->validate();

        $resetRecord = DB::table('password_reset_tokens')->where('token', $this->token)->first();

        if (!$resetRecord || \Carbon\Carbon::parse($resetRecord->created_at)->addMinutes(config('auth.passwords.users.expire'))->isPast()) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Este enlace de recuperación ha expirado. Por favor, solicita uno nuevo.']);
            return;
        }

        if ($resetRecord->email !== $this->email) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'El correo electrónico no coincide con el del enlace de recuperación.']);
            return;
        }

        $user = User::where('email', $this->email)->first();
        if ($user) {
            $user->update(['password' => Hash::make($this->password)]);
            DB::table('password_reset_tokens')->where('email', $this->email)->delete();

            session()->flash('alert', [
                'type' => 'success',
                'message' => '¡Tu contraseña ha sido restablecida con éxito! Ya puedes iniciar sesión.'
            ]);
            return redirect()->route('login');
        } else {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'No se encontró un usuario asociado a este correo electrónico.']);
        }
    }

    public function render()
    {
        return view('livewire.auth.password-reset.reset-form');
    }
}