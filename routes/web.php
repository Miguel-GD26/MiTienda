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


// --- Importaciones de Middleware y Modelos ---
use App\Http\Middleware\RedirectAdminsFromWelcome;
use App\Http\Middleware\RememberStoreUrl;
use App\Http\Middleware\CheckTrialStatus;
use App\Models\Pedido;      

use App\Livewire\UserManagement;
use App\Livewire\RoleManagement;

// 1. RUTAS PÚBLICAS (Accesibles por todos los visitantes)
Route::middleware([RedirectAdminsFromWelcome::class])->group(function () {
    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
    Route::get('/soporte', fn() => view('soporte'))->name('soporte');
    Route::get('/acerca', fn() => view('acerca'))->name('acerca');
    Route::get('/categorias/lista', [CategoriaController::class, 'listar'])->name('categorias.list');
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
    Route::post('logout', function() {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    // --- GESTIÓN DE LA TIENDA (Productos, Categorías, etc. - Panel Admin) ---
    Route::resource('categorias', CategoriaController::class)->except(['show']);
    Route::resource('productos', ProductoController::class)->except(['show']);

    // --- CARRITO DE COMPRAS Y PEDIDOS (Para Clientes) ---
    Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
    Route::post('/carrito/agregar/{producto}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/carrito/actualizar', [CartController::class, 'update'])->name('cart.update');
    Route::post('/carrito/remover', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/carrito/limpiar', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/pedido/procesar', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/pedidos/{pedido}/cancelar-cliente', [PedidoController::class, 'cancelarPorCliente'])->name('pedidos.cliente.cancelar');

    Route::get('/pedido-exitoso/{pedido}', function(Pedido $pedido) {
        if (auth()->id() !== $pedido->cliente->user_id) abort(403);
        
        $pedido->load('detalles.producto', 'empresa', 'cliente');

        // --- CONSTRUIMOS UNA ÚNICA VERSIÓN DEL TEXTO ---
        $resumenWeb = "*¡Nuevo Pedido!* 🛍️\n\n" .
                    "*Referencia:* #" . $pedido->id . "\n" .
                    "*Cliente:* " . $pedido->cliente->nombre . "\n" .
                    "*Fecha:* " . $pedido->created_at->format('d/m/Y') . "\n" .
                    "-----------------------------------\n" .
                    "*DETALLE:*\n";
        
        foreach($pedido->detalles as $detalle) {
            $resumenWeb .= "- {$detalle->cantidad}x {$detalle->producto->nombre} = S/." . number_format($detalle->subtotal, 2) . "\n";
        }

        $resumenWeb .= "-----------------------------------\n" .
                    "*TOTAL: S/." . number_format($pedido->total, 2) . "*";

        if($pedido->notas) {
            $resumenWeb .= "\n\n*Notas:* " . $pedido->notas;
        }
        
        // Pasamos solo 'pedido' y 'resumenWeb'
        return view('tienda.success', compact('pedido', 'resumenWeb'));

    })->name('pedido.success');
    
 
    // Gestión de Pedidos (Admins)
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::post('/pedidos/{pedido}/actualizar-estado', [PedidoController::class, 'update'])->name('pedidos.updateStatus');

    Route::delete('/pedidos/{pedido}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');

    // Gestión de Usuarios y Permisos (Admins)
    // Route::resource('usuarios', UserController::class)->except(['show']);
    // Route::patch('usuarios/{usuario}/toggle', [UserController::class, 'toggleStatus'])->name('usuarios.toggle');
    //Route::get('/usuarios', UserManagement::class)->name('usuarios.index');
    Route::get('usuarios', [UserController::class, 'index'])->name('usuarios.index');
    // Route::resource('roles', RoleController::class);
    //Route::get('/roles', RoleManagement::class)->name('roles.index');
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::resource('permisos', PermissionController::class)->except(['show']);
    
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
    Route::get('/categoria/{categoria}', [ProductoController::class, 'filtrarPorCategoria'])->name('tienda.public.categoria');
    Route::get('/buscar-categorias', [CategoriaController::class, 'buscarPublico'])->name('tienda.categorias.buscar');
    Route::get('/buscar-productos', [ProductoController::class, 'buscarPublicoAjax'])->name('tienda.productos.buscar_ajax');
});
