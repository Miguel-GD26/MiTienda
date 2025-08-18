@extends('autenticacion.app')
@section('titulo', 'Sistema - Restablecer Contraseña')

@section('contenido')
  @livewire('auth.password-reset.reset-form', ['token' => $token])
@endsection

