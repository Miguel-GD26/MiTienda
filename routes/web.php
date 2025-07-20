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

// --- Importaciones de Middleware y Modelos ---
use App\Http\Middleware\RedirectAdminsFromWelcome;
use App\Http\Middleware\RememberStoreUrl;
use App\Models\Pedido;



// 1. RUTAS PÚBLICAS (Accesibles por todos los visitantes)
Route::middleware([RedirectAdminsFromWelcome::class])->group(function () {
    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
    Route::get('/soporte', fn() => view('soporte'))->name('soporte');
    Route::get('/acerca', fn() => view('acerca'))->name('acerca');
    Route::get('/categorias/lista', [CategoriaController::class, 'listar'])->name('categorias.list');
});

// --- TIENDA PÚBLICA ---
Route::middleware([RememberStoreUrl::class])->group(function () {
    Route::get('/tienda/{empresa:slug}', [ProductoController::class, 'mostrarTienda'])->name('tienda.public.index');
    Route::get('/tienda/{empresa:slug}/categoria/{categoria}', [ProductoController::class, 'filtrarPorCategoria'])->name('tienda.public.categoria');
    Route::get('/tienda/{empresa:slug}/buscar-categorias', [CategoriaController::class, 'buscarPublico'])->name('tienda.categorias.buscar');
    Route::get('/tienda/{empresa:slug}/buscar-productos', [ProductoController::class, 'buscarPublicoAjax'])->name('tienda.productos.buscar_ajax');
});


// 2. RUTAS PARA INVITADOS (Usuarios no autenticados)

Route::middleware('guest')->group(function () {
    // Login
    Route::get('login', fn() => view('autenticacion.login'))->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');

    // Registro
    Route::get('/registro', [RegisterController::class, 'showRegistroForm'])->name('registro');
    Route::post('/registro', [RegisterController::class, 'registrar'])->name('registro.store');

    // Recuperación de contraseña
    Route::get('password/reset', [ResetPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('password/email', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.send-link');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'resetPassword'])->name('password.update');

    Route::get('/verification/verify', [VerificationCodeController::class, 'showForm'])->name('verification.code.form');
    Route::post('/verification/verify', [VerificationCodeController::class, 'verify'])->name('verification.code.verify');
    Route::get('/verification/resend', [VerificationCodeController::class, 'resend'])->name('verification.code.resend');
});

    Route::get('/auth/google/redirect', [SocialiteController::class, 'redirect'])->name('login.google.redirect');

    // 2. Google devuelve al usuario a esta ruta
    Route::get('/auth/google/callback', [SocialiteController::class, 'callback'])->name('login.google.callback');

    // 3. Muestra el formulario para completar el perfil si es un usuario nuevo
    Route::get('/auth/google/complete', [SocialiteController::class, 'showCompleteForm'])->name('login.google.complete');

    // 4. Procesa el formulario del paso 3
    Route::post('/auth/google/complete', [SocialiteController::class, 'processCompleteForm'])->name('login.google.complete.store');


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
    Route::resource('usuarios', UserController::class)->except(['show']);
    Route::patch('usuarios/{usuario}/toggle', [UserController::class, 'toggleStatus'])->name('usuarios.toggle');
    Route::resource('roles', RoleController::class);
    Route::resource('permisos', PermissionController::class)->except(['show']);
    
    // Gestión de Clientes (Admins)
    Route::resource('clientes', ClienteController::class)->middleware('role:admin|super_admin')->only(['index', 'show', 'destroy']);
     Route::get('/mis-clientes', [ClienteController::class, 'misClientes'])->name('clientes.mitienda');
    // Gestión de Empresas (SOLO Super Admin)
    Route::resource('empresas', EmpresaController::class);
});