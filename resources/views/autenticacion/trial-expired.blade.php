@extends('autenticacion.app')
@section('titulo', 'Período de Prueba Finalizado')

@section('contenido')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5 text-center">
                    
                    {{-- Logo o Icono Principal --}}
                    <div class="mb-4">
                        <img src="{{ asset('assets/img/tiempo.gif') }}" alt="Tiempo finalizado" style="width: 120px; height: auto;">
                    </div>

                    {{-- Título y Mensaje Principal --}}
                    <h1 class="h3 fw-bold mb-3">Tu Período de Prueba ha Finalizado</h1>
                    
                    <p class="text-muted lead mb-4">
                        ¡Gracias por evaluar nuestro sistema, {{ auth()->user()->name }}! Para desbloquear todas las funcionalidades y continuar impulsando tu negocio, es momento de activar tu plan.
                    </p>

                    {{-- Preparación de la URL de WhatsApp --}}
                    @php
                        $numeroWhatsapp = '51996595664'; // Reemplaza con tu número
                        
                        // Obtenemos los datos del usuario y la empresa
                        $nombreUsuario = auth()->user()->name ?? 'un usuario';
                        $nombreEmpresa = auth()->user()->empresa->nombre ?? 'mi empresa';

                        // ¡CAMBIO AQUÍ! Construimos el mensaje incluyendo el nombre del usuario.
                        $mensaje = "Hola, soy {$nombreUsuario} de la empresa '{$nombreEmpresa}'. Mi período de prueba ha terminado y me gustaría conocer los planes de pago para activar mi cuenta. ¡Gracias!";

                        // Codificamos la URL para que funcione correctamente
                        $urlWhatsapp = "https://wa.me/{$numeroWhatsapp}?text=" . rawurlencode($mensaje);
                    @endphp

                    {{-- Botón de Acción Principal (Call to Action) --}}
                    <div class="d-grid gap-3">
                        <a href="{{ $urlWhatsapp }}" target="_blank" class="btn btn-success btn-lg fw-bold d-flex align-items-center justify-content-center">
                            <i class="fab fa-whatsapp fa-2x me-3"></i>
                            <div>
                                <span class="d-block">Activar mi cuenta por WhatsApp</span>
                                <small class="fw-normal">Habla con un asesor ahora</small>
                            </div>
                        </a>
                    </div>
                    
                    {{-- Línea Separadora --}}
                    <hr class="my-4">

                    {{-- Acciones Secundarias: Cerrar Sesión --}}
                    <div>
                        <p class="text-muted small mb-2">¿No estás listo para continuar?</p>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection