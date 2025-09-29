<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Http\Controllers\CartController;
use App\Models\Producto;
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

    public function authenticate()
    {
        $credentials = $this->validate();
        $throttleKey = Str::lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->dispatch('alert', ['type' => 'error', 'message' => "Demasiados intentos. Inténtalo en {$seconds} segundos."]);
            return;
        }

        if (!Auth::attempt($credentials)) {
            RateLimiter::hit($throttleKey);
            $this->dispatch('alert', ['type' => 'error', 'message' => 'El correo o la contraseña no son correctos.']);
            return;
        }

        $user = Auth::user();

        if (!$user->activo) {
            Auth::logout();
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Su cuenta está inactiva. Contacte al administrador.']);
            return;
        }

        RateLimiter::clear($throttleKey);
        session()->regenerate();

        $user = Auth::user();

        // --- LÓGICA DE ÉXITO CORREGIDA ---

        // 1. Lógica post-login (añadir al carrito)
        $request = request();
        $redirectToIntended = true; // Suponemos que queremos usar intended por defecto

        if ($request->session()->has('url.intended') && strpos($request->session()->get('url.intended'), 'add_product=') !== false) {
            parse_str(parse_url($request->session()->get('url.intended'), PHP_URL_QUERY), $queryParams);
            $productId = $queryParams['add_product'] ?? null;

            if ($productId && $producto = Producto::find($productId)) {
                $cartController = new CartController();
                $addRequest = new Request(['quantity' => 1]);
                $cartController->add($addRequest, $producto);

                // Si se añade al carrito, NO usamos intended, sino la URL de la tienda
                $redirectToIntended = false;
            }
        }

        // 2. Determinamos la URL de redirección final
        $redirectUrl = '';

        if ($user->hasRole(['super_admin', 'admin', 'vendedor', 'repartidor'])) {
            $redirectUrl = route('dashboard');
        } elseif ($user->hasRole('cliente')) {
            // La URL de la tienda tiene la máxima prioridad para los clientes
            if (session()->has('url.store_before_login')) {
                $redirectUrl = session()->pull('url.store_before_login');
            } else {
                // ¡ESTA ES LA CORRECCIÓN!
                // Obtenemos la URL intended de la sesión, con un fallback.
                $redirectUrl = session()->pull('url.intended', route('welcome'));
            }
        } else {
            $redirectUrl = route('welcome');
        }

        // 3. Despachamos el evento al frontend con los datos necesarios
        $this->dispatch(
            'login-success',
            redirectUrl: $redirectUrl,
            userName: $user->name
        );
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
