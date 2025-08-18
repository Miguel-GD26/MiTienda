@extends('welcome.app')
@section('title', '¡Pedido Realizado con Éxito!')

@push('estilos')
<style>
.d-flex .btn {
  margin: 0 0.5rem; /* agrega 8px de espacio horizontal */
}
/* Estilos para el resumen del pedido */
#resumen-pedido {
    font-family: 'Courier New', Courier, monospace;
    white-space: pre-wrap;
    word-wrap: break-word;
    background-color: #f4f6f9;
    border: 1px dashed #ced4da;
    padding: 1.5rem;
    border-radius: .5rem;
    font-size: 14px;
    line-height: 1.7;
    text-align: left;
}

/* Animación para el ícono de check */
.fa-check-circle {
    animation: bounceIn 1s;
}

@keyframes bounceIn {

    0%,
    20%,
    40%,
    60%,
    80%,
    100% {
        transition-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
    }

    0% {
        opacity: 0;
        transform: scale3d(.3, .3, .3);
    }

    20% {
        transform: scale3d(1.1, 1.1, 1.1);
    }

    40% {
        transform: scale3d(.9, .9, .9);
    }

    60% {
        opacity: 1;
        transform: scale3d(1.03, 1.03, 1.03);
    }

    80% {
        transform: scale3d(.97, .97, .97);
    }

    100% {
        opacity: 1;
        transform: scale3d(1, 1, 1);
    }
}

/* --- ESTILOS DE BOTONES DE ACCIÓN --- */

/* ESTILO PRIMARIO (VERDE - para la acción final más importante) */
.btn-accion-primaria {
    background: linear-gradient(145deg, #2ecc71, #27ae60) !important;
    color: #fff !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    padding: 0.75rem 1.5rem !important;
    font-size: 1rem !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    text-decoration: none !important;
}

.btn-accion-primaria:hover {
    background: linear-gradient(145deg, #27ae60, #219150) !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2) !important;
    color: #fff !important;
}

.btn-accion-primaria:active {
    transform: translateY(0px) !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
}

/* ESTILO SECUNDARIO (AZUL - para acciones importantes como Copiar) */
.btn-accion-secundaria {
    background: linear-gradient(145deg, #0d6efd, #0a58ca) !important;
    color: #fff !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    padding: 0.75rem 1.5rem !important;
    font-size: 1rem !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    text-decoration: none !important;
}

.btn-accion-secundaria:hover {
    background: linear-gradient(145deg, #0b5ed7, #0a53be) !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2) !important;
    color: #fff !important;
}

.btn-accion-secundaria:active {
    transform: translateY(0px) !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
}

/* ESTILO TERCIARIO (GRIS - para acciones menos importantes como Volver) */
.btn-accion-terciaria {
    background: linear-gradient(145deg, #6c757d, #5a6268) !important;
    color: #fff !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    padding: 0.75rem 1.5rem !important;
    font-size: 1rem !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    text-decoration: none !important;
}

.btn-accion-terciaria:hover {
    background: linear-gradient(145deg, #5a6268, #495057) !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2) !important;
    color: #fff !important;
}

.btn-accion-terciaria:active {
    transform: translateY(0px) !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
}
</style>
@endpush

@section('contenido')
<div class="container-contenido py-5">
    <div class="row justify-content-center">
        <div class="col-md-9 text-center">

            <i class="fa-solid fa-check-circle text-success fa-5x mb-4"></i>
            <h1 class="display-4 fw-bold">¡Gracias, {{ $pedido->cliente->nombre }}!</h1>
            <p class="lead text-muted">Hemos recibido tu pedido con referencia <strong>#{{ $pedido->id }}</strong>.</p>
            <p>Para finalizar, envía el siguiente resumen a <strong>{{ $pedido->empresa->nombre }}</strong> para
                coordinar el pago y la entrega.</p>
            <hr class="my-4">

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Resumen del Pedido</h5>
                </div>
                <div class="card-body">
                    <pre id="resumen-pedido">{{ $resumenWeb }}</pre>

                    <div class="d-flex justify-content-center flex-wrap mt-4">
                        <button id="copy-btn" class="btn btn-accion-secundaria mx-2 my-2">
                            <i class="fa-solid fa-copy me-2"></i> Copiar Resumen
                        </button>

                        @if($pedido->empresa->telefono_whatsapp)
                        <a id="whatsapp-btn" href="#"
                        data-telefono="{{ $pedido->empresa->telefono_whatsapp }}"
                        class="btn btn-accion-primaria mx-2 my-2">
                            <i class="fa-brands fa-whatsapp me-2"></i> Enviar por WhatsApp
                        </a>
                        @endif
                    </div>

                </div>
            </div>

            <div class="mt-4">
                {{-- BOTÓN TERCIARIO (GRIS) --}}
                <a href="{{ route('tienda.public.index', ['empresa' => $pedido->empresa]) }}"
                    class="btn btn-accion-terciaria">
                    <i class="fa-solid fa-arrow-left me-2"></i> Volver a la Tienda
                </a>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const copyButton = document.getElementById('copy-btn');
    const whatsappButton = document.getElementById('whatsapp-btn');
    const resumenElement = document.getElementById('resumen-pedido');

    if (!resumenElement) return;

    const textoParaMensajes = resumenElement.innerText;

    if (copyButton) {
        copyButton.addEventListener('click', function() {
            navigator.clipboard.writeText(textoParaMensajes).then(() => {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-check me-2"></i> ¡Copiado!';

                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            }).catch(err => {
                console.error('Error al copiar: ', err);
                alert('No se pudo copiar el texto. Por favor, hazlo manualmente.');
            });
        });
    }

    if (whatsappButton) {
        const numeroBase = whatsappButton.getAttribute('data-telefono');
        const numeroLimpio = numeroBase.replace(/\D/g, '');
        const textoWhatsapp = encodeURIComponent(textoParaMensajes);
        const whatsappUrl = `https://wa.me/${numeroLimpio}?text=${textoWhatsapp}`;

        whatsappButton.setAttribute('href', whatsappUrl);
        whatsappButton.setAttribute('target', '_blank');
    }
});
</script>
@endpush