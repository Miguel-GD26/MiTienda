<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --- Importaciones de Controladores (ordenadas alfabéticamente) ---
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\PerfilController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationCodeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\SocialiteController;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\WelcomeController;
use App\Livewire\Auth\PasswordReset\ResetForm;
use Livewire\Features\SupportFileUploads\FileUploadController;

// --- Importaciones de Middleware y Modelos ---
use App\Http\Middleware\RedirectAdminsFromWelcome;
use App\Http\Middleware\RememberStoreUrl;
use App\Http\Middleware\CheckTrialStatus;
use App\Models\Pedido;
use App\Http\Livewire\Storefront;
use App\Models\Empresa;
use App\Livewire\Order\SuccessPage;

Route::middleware('web')->group(function () {

    // 1. RUTAS PÚBLICAS (Accesibles por todos los visitantes)
    Route::middleware([RedirectAdminsFromWelcome::class])->group(function () {
        Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
        Route::get('/soporte', function () {
            // Le pasamos 'tienda' como null para que la vista sepa que no hay contexto.
            return view('soporte', ['tienda' => null]);
        })->name('soporte');

        Route::get('/acerca', function () {
            return view('acerca', ['tienda' => null]);
        })->name('acerca');

        // 2. Rutas CONTEXTUALES (dentro de una tienda)
        // Usamos Route Model Binding para obtener el objeto $empresa automáticamente.
        Route::get('/{empresa:slug}/soporte', function (Empresa $empresa) {
            // Aquí sí pasamos el objeto $empresa a la misma vista.
            return view('soporte', ['tienda' => $empresa]);
        })->name('tienda.soporte'); // Damos un nombre distinto para evitar conflictos

        Route::get('/{empresa:slug}/acerca', function (Empresa $empresa) {
            return view('acerca', ['tienda' => $empresa]);
        })->name('tienda.acerca');
    });

    // Ruta para la página de prueba expirada
    Route::get('/trial-expired', function () {
        return view('autenticacion.trial-expired');
    })->name('trial.expired')->middleware(['auth', CheckTrialStatus::class]);



    // 2. RUTAS PARA INVITADOS (Usuarios no autenticados)

    Route::middleware('guest')->group(function () {
        // Login
        Route::get('login', fn() => view('autenticacion.login'))->name('login');

        // Registro
        Route::get('/registro', fn() => view('autenticacion.registro'))->name('registro');


        // Recuperación de contraseña
        Route::get('password/reset', fn() => view('autenticacion.email'))->name('password.request');
        Route::get('password/reset/{token}', function ($token) {
            return view('autenticacion.reset', ['token' => $token]); // Pasamos el token como un dato   
        })->name('password.reset');

        // Registro Google
        Route::get('auth/google', [SocialiteController::class, 'redirect'])->name('login.google');
        Route::get('auth/google/callback', [SocialiteController::class, 'callback']);
        Route::get('auth/google/complete', function () {
            if (!session()->has('socialite_user_data')) {
                return redirect()->route('login')->with('error', 'Acceso inválido. Por favor, inicia sesión con Google.');
            }
            return view('autenticacion.complete-google-profile');
        })->name('login.google.complete');
    });


    // 3. RUTAS PARA AUTENTICADOS (Requieren que el usuario haya iniciado sesión)

    Route::middleware(['auth'])->group(function () {

        // --- RUTAS GENERALES DE USUARIO ---
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/refresh', [DashboardController::class, 'refresh'])->name('dashboard.refresh');
        Route::post('logout', function () {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect('/login');
        })->name('logout');
        Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
        Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

        // --- GESTIÓN DE LA TIENDA (Productos, Categorías, etc. - Panel Admin) ---
        Route::get('categorias', [CategoriaController::class, 'index'])->name('categorias.index');
        Route::get('productos', [ProductoController::class, 'index'])->name('productos.index');


        // --- CARRITO DE COMPRAS Y PEDIDOS (Para Clientes) ---
        Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
        Route::get('/pedido-exitoso/{pedido}', [PedidoController::class, 'showSuccessPage'])->name('pedido.success');
        


        // Gestión de Pedidos (Admins)
        Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
        Route::get('/pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');

        // RUTAS DE PEDIDOS DEL CLIENTE
        Route::get('/mis-compras', [PedidoController::class, 'index'])->name('cliente.pedidos');
        Route::get('/mis-compras/{pedido}', [PedidoController::class, 'show'])->name('cliente.pedidos.show');

        // Gestión de Usuarios y Permisos (Admins)
        Route::get('usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('permisos', [PermissionController::class, 'index'])->name('permisos.index');

        // Gestión de Clientes (Admins)
        Route::resource('clientes', ClienteController::class)->middleware('role:admin|super_admin')->only(['index', 'show', 'destroy']);
        Route::get('/mis-clientes', [ClienteController::class, 'misClientes'])->name('clientes.mitienda');
        // Gestión de Empresas (SOLO Super Admin)
        Route::resource('empresas', EmpresaController::class);
    });


    // --- TIENDA PÚBLICA ---
    Route::middleware([RememberStoreUrl::class])
        ->prefix('{empresa:slug}')
        ->group(function () {
            Route::get('/', [ProductoController::class, 'mostrarTienda'])->name('tienda.public.index');
        });

    Route::post('/livewire/upload-file', [FileUploadController::class, 'handle'])
        ->name('livewire.upload-file');
});
