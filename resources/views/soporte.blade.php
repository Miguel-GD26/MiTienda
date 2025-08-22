@extends('welcome.app')
@section('title', 'Centro de Ayuda y Soporte')

@push('estilos')
<style>
    /* Estilo inspirado en el banner de tu tienda, pero adaptado para soporte */
    .support-header {
        background: linear-gradient(rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.8)), url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=2073&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        
    }

    /* Tarjetas de selección interactivas */
    .selection-card {
        cursor: pointer;
        border: 2px solid var(--bs-border-color);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .selection-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.1);
        border-color: var(--bs-primary);
    }
    .selection-card.active {
        border-color: var(--bs-primary);
        box-shadow: 0 .5rem 1.5rem rgba(13, 110, 253, .15);
        background-color: var(--bs-primary-bg-subtle);
    }
    .accordion-button:not(.collapsed) {
        color: var(--bs-primary);
        background-color: var(--bs-primary-bg-subtle);
    }
    .whatsapp-link {
        color: #25D366;
        font-weight: bold;
    }
    .nacimos-texto {
    color: #000000 !important;
    text-shadow: none !important;
    font-weight: 900 !important; /* Más fuerte que fw-bold si lo deseas */
    }
</style>
@endpush

@section('contenido')
<div class="container-contenido" x-data="{ userType: '', contactFormVisible: false }">
    
    {{-- 1. Cabecera Atractiva --}}
    <div class="support-header text-center p-5 mb-5 rounded shadow-sm">
        <h1 class="display-5 fw-bold">Estamos aquí para ayudarte</h1>
        <p class="lead col-lg-8 mx-auto nacimos-texto">Encuentra respuestas rápidas en nuestra sección de preguntas frecuentes o contáctanos directamente si necesitas asistencia con la plataforma.</p>
    </div>
    @if(isset($tienda) && $tienda)
<div class="card shadow-sm border-0 mb-5 bg-light-subtle">
    <div class="card-body p-4 text-center">
        <h2 class="h4 mb-3">Soporte Directo de la Tienda <span class="text-primary">{{ $tienda->nombre }}</span></h2>
        <p class="text-muted">Si tu consulta es sobre un producto específico, tu pedido, el pago o la entrega, te recomendamos contactar directamente con la tienda.</p>
        <a href="https://wa.me/{{ $tienda->telefono_whatsapp }}" target="_blank" class="btn btn-success btn-lg">
            <i class="fab fa-whatsapp me-2"></i>Contactar a {{ $tienda->nombre }} por WhatsApp
        </a>
    </div>
</div>
@endif
    {{-- 2. PASO 1: Segmentación del Usuario (El "Filtro" de Soporte) --}}
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body p-4 text-center">
            <h2 class="h4 mb-4">Para empezar, ¿podrías decirnos quién eres?</h2>
            <div class="row g-3 justify-content-center">
                {{-- Tarjeta Cliente --}}
                <div class="col-md-5">
                    <div class="card selection-card h-100" @click="userType = 'cliente'; contactFormVisible = false" :class="{ 'active': userType === 'cliente' }">
                        <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                            <i class="fa-solid fa-user fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Quiero Comprar</h5>
                            <p class="card-text text-muted small">Tengo dudas sobre el proceso de compra y contacto con vendedores.</p>
                        </div>
                    </div>
                </div>
                {{-- Tarjeta Empresa --}}
                <div class="col-md-5">
                    <div class="card selection-card h-100" @click="userType = 'empresa'; contactFormVisible = false" :class="{ 'active': userType === 'empresa' }">
                        <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                            <i class="fa-solid fa-store fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Quiero Vender</h5>
                            <p class="card-text text-muted small">Necesito ayuda para gestionar mi tienda y mis productos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. PASO 2: Preguntas Frecuentes (El "Contenido Dinámico") --}}
    <div x-show="userType" x-transition.opacity.duration.500ms class="mb-5">
        <h2 class="text-center mb-4">Preguntas Frecuentes</h2>
        
        {{-- FAQ para Clientes --}}
        <div class="accordion" id="faqCliente" x-show="userType === 'cliente'">
            <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q1-cliente">¿Cómo realizo una compra?</button></h2><div id="q1-cliente" class="accordion-collapse collapse" data-bs-parent="#faqCliente"><div class="accordion-body">¡Es muy fácil! En cada producto verás un botón para <span class="whatsapp-link"><i class="fab fa-whatsapp"></i> Contactar por WhatsApp</span>. Al hacer clic, se abrirá una conversación con el vendedor para que puedan coordinar el pago y la entrega directamente.</div></div></div>
            <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2-cliente">¿El pago se hace en la web?</button></h2><div id="q2-cliente" class="accordion-collapse collapse" data-bs-parent="#faqCliente"><div class="accordion-body">No, nuestra plataforma solo te conecta con el vendedor. El pago y el método de envío se acuerdan directamente con la tienda a través de WhatsApp. Te recomendamos usar métodos de pago seguros como Yape, Plin o transferencias bancarias.</div></div></div>
            <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q3-cliente">¿Qué pasa si tengo un problema con un vendedor?</button></h2><div id="q3-cliente" class="accordion-collapse collapse" data-bs-parent="#faqCliente"><div class="accordion-body">Nuestra plataforma funciona como un catálogo para conectar compradores y vendedores. La transacción es un acuerdo privado entre ambas partes. Si bien no intervenimos directamente, si tienes una mala experiencia, por favor contáctanos a través del formulario de abajo para que podamos revisar el caso y tomar las medidas correspondientes con la tienda.</div></div></div>
        </div>
        
        {{-- FAQ para Empresas --}}
        <div class="accordion" id="faqEmpresa" x-show="userType === 'empresa'">
            <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q1-empresa">¿Cómo me contacta un cliente?</button></h2><div id="q1-empresa" class="accordion-collapse collapse" data-bs-parent="#faqEmpresa"><div class="accordion-body">Cuando un cliente esté interesado en tus productos, hará clic en el botón de WhatsApp. Esto iniciará una conversación en tu número de WhatsApp registrado. ¡Asegúrate de que tu número esté siempre actualizado en tu perfil de empresa!</div></div></div>
            <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2-empresa">¿Necesito gestionar el stock en la plataforma?</button></h2><div id="q2-empresa" class="accordion-collapse collapse" data-bs-parent="#faqEmpresa"><div class="accordion-body">Sí, es muy importante que mantengas tu stock actualizado. Si un producto se agota, márcalo como "agotado" o pon el stock en 0 para evitar que los clientes te contacten por productos que ya no tienes.</div></div></div>
            <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q3-empresa">¿Consejos para cerrar más ventas por WhatsApp?</button></h2><div id="q3-empresa" class="accordion-collapse collapse" data-bs-parent="#faqEmpresa"><div class="accordion-body">¡Claro! Responde rápido, sé amable y profesional, ten a mano fotos o videos adicionales del producto, y ofrece opciones de pago y envío claras y seguras. Una buena atención es clave para generar confianza y concretar la venta.</div></div></div>
        </div>
        
        <div class="text-center mt-4">
            <button class="btn btn-outline-secondary" @click="contactFormVisible = true">
                <i class="fa-solid fa-envelope me-2"></i>Tengo un problema con la plataforma
            </button>
        </div>
    </div>
    
    {{-- 4. PASO 3: Formulario de Contacto (La "Acción Final") --}}
    <div x-show="contactFormVisible" x-transition.opacity.duration.500ms>
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-lg-5">
                <h2 class="text-center mb-4">Contacta a Soporte Técnico</h2>
                <p class="text-center text-muted mb-4">Usa este formulario para reportar problemas con la plataforma (ej: errores al subir productos, problemas con tu cuenta, etc.).</p>
                <form action="#" method="POST" class="col-lg-8 mx-auto">
                    @csrf
                    @auth
                        <div class="row"><div class="col-md-6 mb-3"><label for="name" class="form-label">Nombre</label><input type="text" id="name" name="name" class="form-control bg-light" value="{{ auth()->user()->name }}" readonly></div><div class="col-md-6 mb-3"><label for="email" class="form-label">Correo electrónico</label><input type="email" id="email" name="email" class="form-control bg-light" value="{{ auth()->user()->email }}" readonly></div></div>
                    @else
                        <div class="row"><div class="col-md-6 mb-3"><label for="name" class="form-label">Tu Nombre</label><input type="text" id="name" name="name" class="form-control" required></div><div class="col-md-6 mb-3"><label for="email" class="form-label">Tu Correo electrónico</label><input type="email" id="email" name="email" class="form-control" required></div></div>
                    @endauth
                    <div class="mb-3"><label for="subject" class="form-label">Asunto</label><input type="text" id="subject" name="subject" class="form-control" placeholder="Ej: No puedo editar un producto" required></div>
                    <div class="mb-3"><label for="message" class="form-label">Mensaje</label><textarea id="message" name="message" class="form-control" rows="5" placeholder="Describe tu problema con el mayor detalle posible." required></textarea></div>
                    <div class="text-center"><button type="submit" class="btn btn-primary btn-lg px-5"><i class="fa-solid fa-paper-plane me-2"></i>Enviar Mensaje</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection