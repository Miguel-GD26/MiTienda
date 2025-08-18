@extends('autenticacion.app')
@section('titulo', 'Sistema - Login')

@section('contenido')
<video autoplay loop muted playsinline controlslist="nodownload" class="video-background d-none d-lg-block">
    <source src="{{ asset('assets/video/fondo.mp4') }}" type="video/mp4">
</video>

<div class="login-page-container">
    <div class="left-video-spacer d-none d-lg-block"></div>
    <div class="login-form-column">
        <div class="login-content">
            @livewire('auth.login')
        </div>
    </div>
</div>
@endsection

