@extends('autenticacion.app')
@section('titulo', 'Sistema - Login')

@section('contenido')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@vite(['resources/css/login.css'])

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

@push('scripts')
    @include('plantilla.partials.sweetalert-listener')
@endpush