@extends('welcome.app')
@section('title', $tienda->nombre ?? 'Sobre Nosotros')

@push('estilos')
<style>
:root {
    --primary-color: #11998e;
    --secondary-color: #38ef7d;
    --primary-dark: #0d7a6f;
    --text-primary: #2c3e50;
    --text-secondary: #7f8c8d;
    --bg-light: #f8fffe;
    --shadow: 0 0.5rem 1.5rem rgba(17, 153, 142, 0.1);
    --shadow-hover: 0 1rem 2rem rgba(17, 153, 142, 0.15);
}

.hero-section {
    background: linear-gradient(135deg, rgba(17, 153, 142, 0.9), rgba(56, 239, 125, 0.8)),
    url('{{ $tienda->imagen_hero ?? "https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=2073&auto=format&fit=crop" }}');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    min-height: 60vh;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(17, 153, 142, 0.1), rgba(56, 239, 125, 0.1));
    animation: float 6s ease-in-out infinite;
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0px) rotate(0deg);
    }

    50% {
        transform: translateY(-20px) rotate(2deg);
    }
}

.hero-content {
    position: relative;
    z-index: 2;
}

.company-logo {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
    margin-bottom: 2rem;
    transition: transform 0.3s ease;
}

.company-logo:hover {
    transform: scale(1.05) rotate(5deg);
}

.section-card {
    background: white;
    border-radius: 20px;
    padding: 3rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    border: 1px solid rgba(17, 153, 142, 0.1);
}

.section-card:hover {
    box-shadow: var(--shadow-hover);
    transform: translateY(-5px);
}

.icon-container {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}

.icon-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: shine 3s infinite;
}

@keyframes shine {
    0% {
        left: -100%;
    }

    50% {
        left: 100%;
    }

    100% {
        left: 100%;
    }
}

.step-number {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 30px;
    height: 30px;
    background: var(--secondary-color);
    color: var(--primary-dark);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
    box-shadow: 0 2px 10px rgba(56, 239, 125, 0.3);
}

.process-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    border: 1px solid rgba(17, 153, 142, 0.1);
    transition: all 0.3s ease;
    height: 100%;
    position: relative;
}

.process-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-hover);
    border-color: var(--primary-color);
}

.feature-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: rgba(17, 153, 142, 0.03);
    border-radius: 10px;
    border-left: 4px solid var(--primary-color);
    transition: all 0.3s ease;
}

.feature-item:hover {
    background: rgba(17, 153, 142, 0.08);
    transform: translateX(10px);
}

.feature-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

.value-card {
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: 15px;
    border: 1px solid rgba(17, 153, 142, 0.1);
    height: 100%;
    transition: all 0.3s ease;
}

.value-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
    border-color: var(--primary-color);
}

.cta-section {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-radius: 30px;
    margin: 3rem 0;
    position: relative;
    overflow: hidden;
}

.cta-section::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: repeating-linear-gradient(45deg,
            transparent,
            transparent 10px,
            rgba(255, 255, 255, 0.03) 10px,
            rgba(255, 255, 255, 0.03) 20px);
    animation: move 20s linear infinite;
}

@keyframes move {
    0% {
        transform: translate(-50%, -50%) rotate(0deg);
    }

    100% {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}

.cta-content {
    position: relative;
    z-index: 2;
}

.btn-primary-custom {
    background: linear-gradient(135deg, white, #f8f9fa);
    color: var(--primary-color);
    border: 2px solid white;
    padding: 1rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.btn-primary-custom:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(17, 153, 142, 0.3);
}

.stats-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    color: white;
    margin-bottom: 1rem;
}

.stats-number {
    font-size: 3rem;
    font-weight: bold;
    display: block;
    color: var(--secondary-color);
}

.location-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    border: 1px solid rgba(17, 153, 142, 0.1);
    transition: all 0.3s ease;
}

.location-card:hover {
    box-shadow: var(--shadow-hover);
    transform: translateY(-5px);
}

@media (max-width: 768px) {
    .hero-section {
        min-height: 50vh;
    }

    .section-card {
        padding: 2rem 1.5rem;
    }

    .process-card {
        padding: 1.5rem;
    }

    .company-logo {
        width: 80px;
        height: 80px;
    }

    .icon-container {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
}

.gradient-text {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.section-divider {
    height: 4px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 2px;
    margin: 2rem 0;
}
</style>
@endpush

@section('contenido')
<div class="container-contenido px-0">

    {{-- 1. Hero Section --}}
    <section class="hero-section d-flex align-items-center">
        <div class="container hero-content">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    @if($tienda->logo_url ?? false)
                    @php $logoPath = cloudinary()->image($tienda->logo_url)->toUrl(); @endphp
                    <img src="{{ $logoPath }}" alt="{{ $tienda->nombre }}" class="company-logo">
                    @endif

                    <h1 class="display-4 fw-bold text-white mb-4">
                        {{ $tienda->nombre ?? 'Tu Empresa' }}
                    </h1>

                    <p class="lead text-white mb-4 fs-3">
                        {{ $tienda->slogan ?? 'Conectando tu comunidad, una conversación a la vez' }}
                    </p>

                    @if($tienda->estadisticas_hero ?? false)
                    <div class="row g-3 mt-4">
                        <div class="col-md-4">
                            <div class="stats-card">
                                <span class="stats-number">{{ $tienda->total_productos ?? '500+' }}</span>
                                <span>Productos</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card">
                                <span class="stats-number">{{ $tienda->total_vendedores ?? '100+' }}</span>
                                <span>Vendedores</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card">
                                <span class="stats-number">{{ $tienda->total_clientes ?? '1000+' }}</span>
                                <span>Clientes</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">

        {{-- 2. Misión, Visión y Valores --}}
        <div class="row g-4 mb-5">
            @if($tienda->mision ?? false)
            <div class="col-lg-4">
                <div class="section-card h-100">
                    <div class="icon-container">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h3 class="fw-bold gradient-text">Nuestra Misión</h3>
                    <div class="section-divider"></div>
                    <p class="text-muted">{{ $tienda->mision }}</p>
                </div>
            </div>
            @endif

            @if($tienda->vision ?? false)
            <div class="col-lg-4">
                <div class="section-card h-100">
                    <div class="icon-container">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <h3 class="fw-bold gradient-text">Nuestra Visión</h3>
                    <div class="section-divider"></div>
                    <p class="text-muted">{{ $tienda->vision }}</p>
                </div>
            </div>
            @endif

            @if($tienda->valores ?? false)
            <div class="col-lg-4">
                <div class="section-card h-100">
                    <div class="icon-container">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <h3 class="fw-bold gradient-text">Nuestros Valores</h3>
                    <div class="section-divider"></div>
                    <p class="text-muted">{{ $tienda->valores }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- 3. ¿Cómo Funciona? --}}
        <section class="mb-5">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold gradient-text">
                    {{ $tienda->titulo_proceso ?? 'Un Proceso Sencillo y Familiar' }}
                </h2>
                <p class="lead text-muted">
                    {{ $tienda->subtitulo_proceso ?? 'Tres simples pasos para conectar contigo' }}
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="process-card">
                        <div class="icon-container position-relative">
                            <i class="fa-solid fa-store"></i>
                            <span class="step-number">1</span>
                        </div>
                        <h4 class="fw-semibold">{{ $tienda->paso1_titulo ?? 'Descubre Tiendas' }}</h4>
                        <p class="text-muted">
                            {{ $tienda->paso1_descripcion ?? 'Explora un catálogo vibrante de productos de negocios locales cerca de ti.' }}
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="process-card">
                        <div class="icon-container position-relative">
                            <i class="fab fa-whatsapp"></i>
                            <span class="step-number">2</span>
                        </div>
                        <h4 class="fw-semibold">{{ $tienda->paso2_titulo ?? 'Contacta Directamente' }}</h4>
                        <p class="text-muted">
                            {{ $tienda->paso2_descripcion ?? '¿Te gusta algo? Inicia una conversación por WhatsApp con el vendedor con un solo clic.' }}
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="process-card">
                        <div class="icon-container position-relative">
                            <i class="fa-solid fa-handshake-simple"></i>
                            <span class="step-number">3</span>
                        </div>
                        <h4 class="fw-semibold">{{ $tienda->paso3_titulo ?? 'Coordina y Compra' }}</h4>
                        <p class="text-muted">
                            {{ $tienda->paso3_descripcion ?? 'Acuerda el pago y la entrega de forma segura y directa. ¡Sin intermediarios!' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- 4. ¿Por qué elegirnos? --}}
        <section class="mb-5">
            <div class="section-card">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h2 class="fw-bold gradient-text mb-4">
                            {{ $tienda->titulo_ventajas ?? '¿Por qué elegirnos?' }}
                        </h2>
                        <p class="lead text-muted mb-4">
                            {{ $tienda->subtitulo_ventajas ?? 'Redescubre el placer de comprar local con la comodidad de la tecnología.' }}
                        </p>

                        @php
                        $ventajas = $tienda->ventajas ?? [
                        ['icono' => 'fa-circle-check', 'titulo' => 'Confianza Total', 'descripcion' => 'Habla
                        directamente con el dueño de la tienda.'],
                        ['icono' => 'fa-heart', 'titulo' => 'Apoyo Local', 'descripcion' => 'Cada compra fortalece la
                        economía de tu comunidad.'],
                        ['icono' => 'fa-mobile', 'titulo' => 'Sin Complicaciones', 'descripcion' => 'Usa WhatsApp, una
                        app que ya conoces y en la que confías.']
                        ];
                        @endphp

                        @foreach($ventajas as $ventaja)
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fa-solid {{ $ventaja['icono'] }}"></i>
                            </div>
                            <div>
                                <h5 class="fw-semibold mb-2">{{ $ventaja['titulo'] }}</h5>
                                <p class="text-muted mb-0">{{ $ventaja['descripcion'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="col-lg-6">
                        <img src="{{ $tienda->imagen_ventajas ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=1974&auto=format&fit=crop' }}"
                            class="img-fluid rounded-3 shadow-lg" alt="Ventajas de nuestro servicio">
                    </div>
                </div>
            </div>
        </section>

        {{-- 5. Nuestros Pilares/Valores Detallados --}}
        <section class="mb-5">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold gradient-text">
                    {{ $tienda->titulo_pilares ?? 'Nuestros Pilares' }}
                </h2>
                <p class="lead text-muted">
                    {{ $tienda->subtitulo_pilares ?? 'Los valores que nos guían cada día' }}
                </p>
            </div>

            @php
            $pilares = $tienda->pilares ?? [
            ['icono' => 'fa-users', 'titulo' => 'Comunidad', 'descripcion' => 'Creemos en el poder de conectar a las
            personas y fortalecer los lazos locales.'],
            ['icono' => 'fa-bolt', 'titulo' => 'Simplicidad', 'descripcion' => 'La tecnología debe ser una herramienta
            fácil de usar, no una barrera.'],
            ['icono' => 'fa-shield-halved', 'titulo' => 'Confianza', 'descripcion' => 'Fomentamos un entorno
            transparente donde la comunicación directa es clave.']
            ];
            @endphp

            <div class="row g-4">
                @foreach($pilares as $pilar)
                <div class="col-md-4">
                    <div class="value-card">
                        <div class="icon-container">
                            <i class="fa-solid {{ $pilar['icono'] }}"></i>
                        </div>
                        <h4 class="fw-semibold">{{ $pilar['titulo'] }}</h4>
                        <p class="text-muted">{{ $pilar['descripcion'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        {{-- 6. Ubicación y Contacto --}}
        @if($tienda->mostrar_ubicacion ?? true)
        <section class="mb-5">
            <div class="section-card">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="location-card">
                            <div class="icon-container">
                                <i class="fa-solid fa-map-marker-alt"></i>
                            </div>
                            <h3 class="fw-bold gradient-text">Nuestra Ubicación</h3>
                            <div class="section-divider"></div>
                            <p class="text-muted mb-3">
                                <strong>Dirección:</strong><br>
                                {{ $tienda->direccion ?? 'Lima, Perú' }}
                            </p>
                            @if($tienda->telefono_whatsapp ?? false)
                            <p class="text-muted mb-3">
                                <strong>Teléfono:</strong><br>
                                <a href="tel:{{ $tienda->telefono_whatsapp }}" class="text-decoration-none"
                                    style="color: var(--primary-color);">
                                    {{ $tienda->telefono_whatsapp }}
                                </a>
                            </p>
                            @endif
                            @if($tienda->usuarios->first()?->email)
                            <p class="text-muted">
                                <strong>Email:</strong><br>
                                <a href="mailto:{{ $tienda->usuarios->first()->email }}" class="text-decoration-none"
                                    style="color: var(--primary-color);">
                                    {{ $tienda->usuarios->first()->email }}
                                </a>
                                </a>
                            </p>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        @php
                        $tienda->mapa_embed = '<iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15848.079508113147!2d-79.79453805610035!3d-6.767430241668267!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x904ce9aa87bf1b41%3A0xf926e8bc5aa87b3b!2sPomalca%2014006!5e0!3m2!1ses-419!2spe!4v1756408471127!5m2!1ses-419!2spe"
                            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>';
                        @endphp
                        @if($tienda->mapa_embed ?? false)
                        {!! $tienda->mapa_embed !!}
                        @else
                        <div class="bg-light rounded-3 p-4 text-center"
                            style="min-height: 300px; display: flex; align-items: center; justify-content: center;">
                            <div>
                                <i class="fa-solid fa-map fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Mapa de ubicación</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- 7. Llamada a la Acción Final --}}
        <section class="cta-section">
            <div class="container cta-content text-center py-5">
                <h2 class="fw-bold mb-3">
                    {{ $tienda->cta_titulo ?? '¿Listo para unirte?' }}
                </h2>
                <p class="lead mb-4">
                    {{ $tienda->cta_descripcion ?? 'Forma parte de nuestra creciente comunidad de compradores y vendedores locales.' }}
                </p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="{{ route('registro') }}?tipo_usuario=tienda" class="btn btn-primary-custom btn-lg px-4">
                        {{ $tienda->cta_texto_boton ?? 'Empieza Hoy' }}
                    </a>
                    @if($tienda->telefono_whatsapp ?? false)
                    <a href="https://wa.me/{{ $tienda->telefono_whatsapp }}" class="btn btn-outline-light btn-lg px-4 ms-2">
                        <i class="fab fa-whatsapp me-2"></i>Contáctanos
                    </a>
                    @endif
                </div>
            </div>
        </section>

    </div>
</div>
@endsection