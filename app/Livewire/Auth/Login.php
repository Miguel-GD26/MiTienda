<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Http\Controllers\CartController; // <-- 1. Importa el CartController
use App\Models\Producto;                 // <-- 2. Importa el modelo Producto
use Illuminate\Http\Request; 

class Login extends Component
{
    public $email = '';
    public $password = '';

    protected $rules = [
        'email'    => 'required|email',
        'password' => 'required',
    ];

    protected $messages = [
        'email.required' => 'El campo correo electrónico es obligatorio.',
        'email.email' => 'El formato del correo electrónico no es válido.',
        'password.required' => 'El campo contraseña es obligatorio.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function authenticate(Request $request)
    {

        $credentials = $this->validate();
        $throttleKey = Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => "Demasiados intentos. Por favor, inténtalo de nuevo en {$seconds} segundos."
            ]);
            return;
        }

        if (!Auth::attempt($credentials)) {
            RateLimiter::hit($throttleKey);
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'El correo electrónico o la contraseña no son correctos.'
            ]);
            return;
        }
        
        RateLimiter::clear($throttleKey);

        $user = Auth::user();

        if (!$user->activo) {
            Auth::logout();
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Su cuenta está inactiva. Por favor, contacte con el administrador.'
            ]);
            return;
        }

        session()->regenerate();

        if ($request->session()->has('url.intended') && strpos($request->session()->get('url.intended'), 'add_product=') !== false) {
            
            // Extraemos el ID del producto de la URL guardada
            parse_str(parse_url($request->session()->get('url.intended'), PHP_URL_QUERY), $queryParams);
            $productId = $queryParams['add_product'] ?? null;

            if ($productId && $producto = Producto::find($productId)) {
                // Creamos una instancia de CartController para usar su lógica de 'add'
                $cartController = new CartController();
                // Creamos una nueva Request para simular la cantidad (por defecto 1)
                $addRequest = new Request(['quantity' => 1]);
                // Llamamos al método 'add'
                $cartController->add($addRequest, $producto);
            }
        }

        if ($user->hasRole(['super_admin', 'admin', 'vendedor', 'repartidor'])) {
            return $this->redirect('dashboard');
        }

        if ($user->hasRole('cliente')) {
            if (session()->has('url.store_before_login')) {
                $url = session()->pull('url.store_before_login');
                return $this->redirect($url);
            }
            return redirect()->intended(route('welcome'));
        }
        
        return $this->redirectRoute('welcome');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}