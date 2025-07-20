<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    public function index()
    {
        $misTiendas = collect(); 
        if (Auth::check() && Auth::user()->hasRole('cliente')) {
            $misTiendas = Auth::user()->cliente
                            ->empresas()
                            ->distinct()
                            ->orderBy('nombre')
                            ->get();
        }
        return view('welcome', compact('misTiendas'));
    }
}