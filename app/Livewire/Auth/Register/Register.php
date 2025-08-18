<?php

namespace App\Livewire\Auth\Register;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\User;
use App\Notifications\VerificationCodeNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Component;

class Register extends Component
{
    public $step = 1;
    public $tipo_usuario = '';

    public $registrationData = [];

    public $verification_code = ['', '', '', '', '', ''];

    public $countdown = 0;
    public $canResend = false;

    public $isLoading = false;

    protected $listeners = ['registrationSubmitted'];

    public function registrationSubmitted($data)
    {
        $this->isLoading = true;

        $this->registrationData = $data;
        $generated_code = random_int(100000, 999999);
        $code_expires_at = now()->addMinutes(10);

        session([
            'verification_code' => $generated_code,
            'verification_expires_at' => $code_expires_at,
        ]);
        
        try {
            Notification::route('mail', $this->registrationData['email'])
                        ->notify(new VerificationCodeNotification($generated_code, $this->registrationData['name']));
        } catch (\Exception $e) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'No se pudo enviar el email de verificación.']);
            $this->isLoading = false; 
            return;
        }

        $this->step = 2;
        $this->startCountdown();
        $this->isLoading = false; 
        $this->dispatch('alert', ['type' => 'success', 'message' => '¡Registro casi listo! Te hemos enviado un código.']);
    }
    /**
     * Verifica el código y redirige.
     *
     * @return Redirector
     */
    public function verifyCode()
    {
        $this->validate(
            ['verification_code.*' => 'required|numeric|digits:1'],
            ['verification_code.*' => 'El código de verificación es inválido.']
        );

        $generated_code = session('verification_code');
        $code_expires_at = session('verification_expires_at');

        if (!$code_expires_at || now()->gt($code_expires_at)) {
            $this->addError('verification_code.0', 'El proceso ha expirado. Por favor, reenvía el código.');
            return;
        }

        if ($generated_code != implode('', $this->verification_code)) {
            $this->addError('verification_code.0', 'El código de verificación es inválido.');
            return;
        }

        session()->forget(['verification_code', 'verification_expires_at']);

        $user = null;
        $data = $this->registrationData;
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'activo' => 1,
                'email_verified_at' => now(),
            ]);

            if ($this->tipo_usuario === 'empresa') {
                $empresa = Empresa::create([
                    'nombre' => $data['empresa_nombre'],
                    'slug' => Str::slug($data['empresa_nombre']),
                    'rubro' => $data['empresa_rubro'],
                    'telefono_whatsapp' => $data['empresa_telefono_whatsapp'],
                    'trial_ends_at' => now()->addDays(7),
                    'subscription_status' => 'trialing',
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
            report($e);
            return redirectRoute('registro')->with('error', 'Ocurrió un error al finalizar tu registro.');
        }

        Auth::login($user);

        if ($user->hasRole('admin')) {
            return $this->redirectRoute('dashboard');
        } else {
            return $this->redirectRoute('welcome');
        }
    }
    
    public function resendCode()
    {
        if (!$this->canResend) return;

        $generated_code = random_int(100000, 999999);
        $code_expires_at = now()->addMinutes(10);

        session([
            'verification_code' => $generated_code,
            'verification_expires_at' => $code_expires_at,
        ]);

        try {
            Notification::route('mail', $this->registrationData['email'])
                        ->notify(new VerificationCodeNotification($generated_code, $this->registrationData['name']));
        } catch (\Exception $e) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'No se pudo reenviar el email.']);
            return;
        }

        $this->verification_code = ['', '', '', '', '', ''];
        $this->resetErrorBag();
        $this->startCountdown();
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Se ha enviado un nuevo código.']);
    }

    public function startCountdown()
    {
        $this->canResend = false;
        $this->countdown = 60;
    }

    public function decrementCountdown()
    {
        if ($this->countdown > 0) {
            $this->countdown--;
            if ($this->countdown == 0) {
                $this->canResend = true;
            }
        }
    }

    public function backToForm()
    {
        $this->step = 1;
        $this->tipo_usuario = '';
        $this->registrationData = [];
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.auth.register.register');
    }
}