<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PerfilController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/soporte', function () {
    return view('soporte');
})->name('soporte');


Route::resource('categorias', CategoriaController::class)->except(['show']);
Route::get('/categorias/lista', [CategoriaController::class, 'listar'])->name('categorias.list');

Route::middleware(['auth'])->group(function(){
    Route::resource('usuarios', UserController::class);
    Route::patch('usuarios/{usuario}/toggle', [UserController::class, 'toggleStatus'])->name('usuarios.toggle');
    Route::resource('roles', RoleController::class);

    Route::get('dashboard', function(){
        return view('dashboard');
    })->name('dashboard');

    Route::post('logout', function(){
        Auth::logout();
        return redirect('/login');
    })->name('logout');

    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::resource('categoriass',CategoriaController::class);
});

Route::middleware('guest')->group(function(){
    Route::get('login', function(){
        return view('autenticacion.login');
    })->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/registro', [RegisterController::class, 'showRegistroForm'])->name('registro');
    Route::post('/registro', [RegisterController::class, 'registrar'])->name('registro.store');

    Route::get('password/reset', [ResetPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('password/email', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.send-link');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'resetPassword'])->name('password.update');
    

});