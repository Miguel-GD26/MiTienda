@extends('autenticacion.app')
@section('titulo', 'Sistema - Login')

@section('contenido')
<video autoplay loop muted playsinline class="video-background d-none d-lg-block">
    <source src="{{ asset('assets/video/fondo.mp4') }}" type="video/mp4">
</video>

<div class="login-page-container">
    <div class="left-video-spacer d-none d-lg-block"></div>
    
    {{-- 1. Añadimos 'position-relative' a la columna del formulario --}}
    <div class="login-form-column">
        
        {{-- 2. Movemos el loader aquí, como hijo directo de la columna --}}
        

        <div class="login-content position-relative">
            <div 
            id="success-loader-container" 
            class="position-absolute w-100 h-100 top-0 start-0 d-none flex-column align-items-center justify-content-center" 
            style="background-color: rgba(255, 255, 255, 1); z-index: 1000; backdrop-filter: blur(8px); border-radius: inherit;">
            
            <img src="{{ asset('assets/img/progress.gif') }}" alt="Cargando..." style="width: 200px; height: auto;">
            <h4 class="text-black mt-3 fw-bold">¡Datos Correctos!</h4>
            <p class="text-black fs-5" id="loader-welcome-message"></p>
        </div>
            @livewire('auth.login')
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('login-success', (event) => {
            const loader = document.getElementById('success-loader-container');
            const message = document.getElementById('loader-welcome-message');

            if (loader && message) {
                message.innerText = `Bienvenido, ${event.userName}`;
                loader.classList.remove('d-none');
                loader.classList.add('d-flex');
            }

            setTimeout(() => {
                window.location.href = event.redirectUrl;
            }, 2000);
        });
    });
</script>
@endpush