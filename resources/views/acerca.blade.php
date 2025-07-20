@extends('welcome.app')
@section('title', 'Sobre Nosotros')

@push('estilos')
<style>
    .about-header {
        background: linear-gradient(rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.8)), url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=2073&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        
    }
    
    .step-icon {
        width: 80px;
        height: 80px;
        background-color: var(--bs-primary-bg-subtle);
        color: var(--bs-primary);
        border: 2px solid var(--bs-primary-border-subtle);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    .feature-card {
        border: 0;
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.07);
        transition: transform 0.2s ease;
    }
    .feature-card:hover {
        transform: translateY(-5px);
    }
    .value-card {
        text-align: center;
    }
    .cta-section {
        background-color: var(--bs-light);
    }
    .nacimos-texto {
    color: #000000 !important;
    text-shadow: none !important;
    font-weight: 900 !important; /* Más fuerte que fw-bold si lo deseas */
    }


</style>
@endpush

@section('contenido')
<div class="container-contenido">

    {{-- 1. Cabecera con Misión --}}
    <div class="about-header text-white text-center p-5 mb-5 rounded shadow">
        <h1 class="display-5 fw-bold">Conectando tu comunidad, una conversación a la vez</h1>
            <p class="lead col-lg-8 mx-auto fw-bold nacimos-texto">Nacimos con la misión de simplificar el comercio local, creando un puente directo y confiable entre los vendedores de tu barrio y tú, a través de la herramienta que ya usas todos los días: WhatsApp.</p>
    </div>

    {{-- 2. ¿Cómo Funciona? Explicación Visual --}}
    <div class="container text-center my-5 py-5">
        <h2 class="mb-5">Un Proceso Sencillo y Familiar</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="step-icon">
                    <i class="fa-solid fa-store"></i>
                </div>
                <h4 class="fw-semibold">1. Descubre Tiendas</h4>
                <p class="text-muted">Explora un catálogo vibrante de productos de negocios locales cerca de ti.</p>
            </div>
            <div class="col-md-4">
                <div class="step-icon">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <h4 class="fw-semibold">2. Contacta Directamente</h4>
                <p class="text-muted">¿Te gusta algo? Inicia una conversación por WhatsApp con el vendedor con un solo clic.</p>
            </div>
            <div class="col-md-4">
                <div class="step-icon">
                    <i class="fa-solid fa-handshake-simple"></i>
                </div>
                <h4 class="fw-semibold">3. Coordina y Compra</h4>
                <p class="text-muted">Acuerda el pago y la entrega de forma segura y directa. ¡Sin intermediarios!</p>
            </div>
        </div>
    </div>

    {{-- 3. Beneficios para Compradores y Vendedores --}}
    <div class="bg-white py-5">
        <div class="container">
            {{-- Para Compradores --}}
            <div class="row g-5 align-items-center mb-5 pb-5">
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=1974&auto=format&fit=crop" class="img-fluid rounded shadow-lg" alt="Persona comprando">
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold">Para Ti, el Comprador</h2>
                    <p class="text-muted mb-4">Redescubre el placer de comprar local con la comodidad de la tecnología.</p>
                    <ul class="list-unstyled fs-5">
                        <li class="mb-3"><i class="fa-solid fa-circle-check text-primary me-2"></i><strong>Confianza Total:</strong> Habla directamente con el dueño de la tienda.</li>
                        <li class="mb-3"><i class="fa-solid fa-circle-check text-primary me-2"></i><strong>Apoyo Local:</strong> Cada compra fortalece la economía de tu comunidad.</li>
                        <li class="mb-3"><i class="fa-solid fa-circle-check text-primary me-2"></i><strong>Sin Complicaciones:</strong> Usa WhatsApp, una app que ya conoces y en la que confías.</li>
                    </ul>
                </div>
            </div>
            {{-- Para Vendedores --}}
            <div class="row g-5 align-items-center flex-row-reverse">
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1556740738-b6a63e27c4df?q=80&w=2070&auto=format&fit=crop" class="img-fluid rounded shadow-lg" alt="Vendedor atendiendo">
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold">Para Ti, el Vendedor</h2>
                    <p class="text-muted mb-4">Tu vitrina digital para llegar a más clientes, sin la complejidad de un e-commerce tradicional.</p>
                    <ul class="list-unstyled fs-5">
                        <li class="mb-3"><i class="fa-solid fa-circle-check text-success me-2"></i><strong>Fácil de Empezar:</strong> Sube tus productos en minutos y empieza a vender.</li>
                        <li class="mb-3"><i class="fa-solid fa-circle-check text-success me-2"></i><strong>Comunicación Directa:</strong> Convierte interesados en clientes fieles a través de WhatsApp.</li>
                        <li class="mb-3"><i class="fa-solid fa-circle-check text-success me-2"></i><strong>Control Total:</strong> Gestiona tus ventas y pagos a tu manera, sin intermediarios.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    {{-- 4. Nuestros Valores --}}
    <div class="container my-5 py-5">
        <h2 class="text-center mb-5">Nuestros Pilares</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="value-card">
                    <div class="step-icon"><i class="fa-solid fa-users"></i></div>
                    <h4 class="fw-semibold">Comunidad</h4>
                    <p class="text-muted">Creemos en el poder de conectar a las personas y fortalecer los lazos locales.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card">
                    <div class="step-icon"><i class="fa-solid fa-bolt"></i></div>
                    <h4 class="fw-semibold">Simplicidad</h4>
                    <p class="text-muted">La tecnología debe ser una herramienta fácil de usar, no una barrera.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card">
                    <div class="step-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <h4 class="fw-semibold">Confianza</h4>
                    <p class="text-muted">Fomentamos un entorno transparente donde la comunicación directa es clave.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. Llamada a la Acción (CTA) Final --}}
    <div class="cta-section py-5">
        <div class="container text-center">
            <h2 class="fw-bold mb-3">¿Listo para unirte?</h2>
            <p class="lead text-muted mb-4">Forma parte de nuestra creciente comunidad de compradores y vendedores locales.</p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="{{ route('registro') }}?tipo_usuario=empresa" class="btn btn-primary btn-lg px-4 gap-3">Empieza Hoy</a>
                <!--<a href="{{ url('/') }}" class="btn btn-outline-secondary btn-lg px-4">Explorar Tiendas</a> -->
            </div>
        </div>
    </div>

</div>
@endsection