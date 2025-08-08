<?php

namespace App\Livewire\Auth\PasswordReset;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class RequestForm extends Component
{
    public $email;
    public $sent = false;

    //public $isLoading = false;

    protected $rules = [
        'email' => 'required|email|exists:users,email',
    ];

    public function toggleLoading()
    {
        $this->isLoading = !$this->isLoading;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function sendResetLink()
    {
        
        //$this->isLoading = true;
        $this->validate();

        $user = User::where('email', $this->email)->first();
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $this->email],
            ['token' => $token, 'created_at' => now()]
        );

        try {
            
            $user->notify(new ResetPasswordNotification($token));

            $this->sent = true;

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => '¡Enlace de recuperación enviado! Revisa tu correo.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar email de reseteo: ' . $e->getMessage());
            
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'No se pudo enviar el enlace. Inténtalo más tarde.'
            ]);
            
        } finally {
            //$this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.auth.password-reset.request-form');
    }
}