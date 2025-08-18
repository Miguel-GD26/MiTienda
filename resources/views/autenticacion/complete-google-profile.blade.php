{{-- resources/views/autenticacion/complete-google-profile.blade.php --}}

@extends('autenticacion.app')

@section('titulo', 'Sistema - Completar Registro con Google')

@section('contenido')
    {{-- Aquí llamamos a tu componente Livewire por su alias --}}
    {{-- El alias es 'auth.register-google.complete-social-profile' --}}
    @livewire('auth.register-google.complete-social-profile')
@endsection

