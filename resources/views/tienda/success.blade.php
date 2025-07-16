@extends('welcome.app')
@section('title', '¡Pedido Realizado con Éxito!')

@push('estilos')
<style>
    /* Estilos para que el resumen se vea como texto de código y sea fácil de copiar */
    #resumen-pedido {
        font-family: 'Courier New', Courier, monospace; /* Fuente monoespaciada para alinear texto */
        white-space: pre-wrap; /* Respeta los saltos de línea y ajusta el texto si es largo */
        word-wrap: break-word;
        background-color: #f4f6f9; /* Un fondo gris claro */
        border: 1px dashed #ced4da;
        padding: 1.5rem;
        border-radius: .5rem;
        font-size: 14px;
        line-height: 1.7;
        text-align: left; /* Alineación del texto a la izquierda */
    }

    .copy-btn {
        transition: all 0.2s ease-in-out;
    }

    /* Estilos para el icono de check en el header */
    .fa-check-circle {
        /* Puedes añadir animaciones si tienes Animate.css, si no, puedes quitar la clase */
        animation: bounceIn 1s; 
    }

    @keyframes bounceIn {
        0%, 20%, 40%, 60%, 80%, 100% {
            transition-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
        }
        0% { opacity: 0; transform: scale3d(.3, .3, .3); }
        20% { transform: scale3d(1.1, 1.1, 1.1); }
        40% { transform: scale3d(.9, .9, .9); }
        60% { opacity: 1; transform: scale3d(1.03, 1.03, 1.03); }
        80% { transform: scale3d(.97, .97, .97); }
        100% { opacity: 1; transform: scale3d(1, 1, 1); }
    }
</style>
@endpush

@section('contenido')
<div class="container-contenido py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            
            <i class="fa-solid fa-check-circle text-success fa-5x mb-4"></i>
            <h1 class="display-4 fw-bold">¡Gracias, {{ $pedido->cliente->nombre }}!</h1>
            <p class="lead text-muted">Hemos recibido tu pedido con el número de referencia <strong>#{{ $pedido->id }}</strong>.</p>
            <p>Para finalizar, por favor, envía el siguiente resumen por WhatsApp a <strong>{{ $pedido->empresa->nombre }}</strong> para coordinar el pago y la entrega.</p>
            <hr class="my-4">

            <div class="card shadow-sm">
                <div class="card-header">
                    Resumen del Pedido
                </div>
                <div class="card-body">
                    
                    {{-- El elemento <pre> es la única fuente de texto para el JavaScript --}}
                    <pre id="resumen-pedido">{{ $resumenWeb }}</pre>
                    
                    <div class="d-flex justify-content-center flex-wrap gap-2 mt-4">
                        <button id="copy-btn" class="btn btn-secondary copy-btn">
                            <i class="fa-solid fa-copy me-1"></i> Copiar Resumen
                        </button>
                        
                        @if($pedido->empresa->telefono_whatsapp)
                            {{-- El JavaScript construirá el href de este enlace --}}
                            <a id="whatsapp-btn" href="#" 
                               data-telefono="{{ $pedido->empresa->telefono_whatsapp }}"
                               class="btn btn-success">
                                <i class="fa-brands fa-whatsapp me-1"></i> Enviar por WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <a href="{{ route('tienda.public.index', ['empresa' => $pedido->empresa]) }}" class="btn btn-outline-primary mt-4">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver a la Tienda
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const copyButton = document.getElementById('copy-btn');
    const whatsappButton = document.getElementById('whatsapp-btn');
    const resumenElement = document.getElementById('resumen-pedido');
    
    // Si el elemento de resumen no existe, no hacemos nada para evitar errores.
    if (!resumenElement) return;

    // Obtenemos el texto visible, que es nuestra fuente de verdad.
    const textoParaMensajes = resumenElement.innerText;
    
    // Lógica para el botón de copiar
    if (copyButton) {
        copyButton.addEventListener('click', function () {
            navigator.clipboard.writeText(textoParaMensajes).then(() => {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-check me-1"></i> ¡Copiado!';
                this.classList.remove('btn-secondary');
                this.classList.add('btn-info');
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('btn-info');
                    this.classList.add('btn-secondary');
                }, 2000); // Vuelve al estado original después de 2 segundos
            }).catch(err => {
                console.error('Error al copiar: ', err);
                alert('No se pudo copiar el texto. Por favor, hazlo manualmente.');
            });
        });
    }

    // Lógica para el botón de WhatsApp
    if (whatsappButton) {
        const numeroBase = whatsappButton.getAttribute('data-telefono');
        const numeroLimpio = numeroBase.replace(/\D/g, ''); // Limpia todo lo que no sea un dígito
        
        // Codificamos el texto visible usando la función del navegador, que maneja bien los emojis.
        const textoWhatsapp = encodeURIComponent(textoParaMensajes);
        
        const whatsappUrl = `https://wa.me/${numeroLimpio}?text=${textoWhatsapp}`;
        
        // Asignamos la URL construida al botón
        whatsappButton.setAttribute('href', whatsappUrl);
        whatsappButton.setAttribute('target', '_blank'); // Para que abra en una nueva pestaña
    }
});
</script>
@endpush