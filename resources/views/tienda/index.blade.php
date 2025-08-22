@extends('welcome.app')
@section('titulo')
{{ $tienda->nombre }}
@endsection


@push('estilos')
<style>
/* ========================================
   ESTILOS PRINCIPALES - TIENDA DE PRODUCTOS
   ======================================== */

/* --- LAYOUT GENERAL --- */
#product-grid-container {
    row-gap: 1.2rem;
}

.tienda-header {
    background: linear-gradient(rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.8)), 
                url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=2073&auto=format&fit=crop');
    background-size: cover;
    background-position: center;
    color: white;
}

/* --- BADGES DE PRODUCTOS --- */
.product-card-intuitive {
    position: relative; /* CRUCIAL para badges */
}

.card-img-container {
    position: relative;
    overflow: hidden;
}

.card-img-top {
    position: relative;
    z-index: 1;
}

/* Badge base (esquina superior izquierda) */
.product-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 5px 10px;
    font-size: 0.75rem;
    font-weight: bold;
    color: white;
    border-radius: 15px;
    z-index: 10;
    text-transform: uppercase;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    letter-spacing: 0.3px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Colores específicos de badges */
.badge-sales {
    background-color: #f02805; /* Rojo para ofertas */
}

.badge-outofstock {
    background-color: #6c757d; /* Gris para agotado */
}

/* Badge para últimas unidades (esquina inferior derecha) */
.badge-lowstocks {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background-color: #ff7700; /* Naranja */
    color: white;
    padding: 4px 8px;
    font-size: 0.7rem;
    font-weight: bold;
    border-radius: 12px;
    z-index: 10;
    text-transform: uppercase;
    white-space: nowrap;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    letter-spacing: 0.3px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.2);
    
    /* Sobrescribir herencia */
    top: auto !important;
    left: auto !important;
}

/* --- ANIMACIONES PARA BADGES --- */
.product-badge,
.badge-lowstocks {
    animation: fadeInBadge 0.3s ease-in;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.product-badge:hover,
.badge-lowstocks:hover {
    transform: scale(1.05);
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.4);
}

@keyframes fadeInBadge {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* --- BOTONES --- */
.btn-agotado {
    background-color: #8B8177; /* Marrón-grisáceo */
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    font-size: 1rem;
}

.btn-anadir {
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
}

.btn-anadir:hover {
    background: linear-gradient(145deg, #27ae60, #219150) !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2) !important;
}

.btn-anadir:active {
    transform: translateY(0px) !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
}

.btn-login-comprar {
    position: relative !important;
    background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
    color: #fff !important;
    border: none !important;
    padding: 0.8rem 2rem !important;
    font-size: 1rem !important;
    font-weight: 600 !important;
    border-radius: 12px !important;
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15) !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 10px !important;
    overflow: hidden !important;
    text-decoration: none !important;
}

.btn-login-comprar i {
    font-size: 1.2rem !important;
    color: #fff !important;
    transition: transform 0.3s ease !important;
}

/* --- CONTROLES DE CANTIDAD --- */
.add-to-cart-form,
.update-cart-form {
    display: flex;
    gap: 0.5rem;
}

.quantity-control-wrapper {
    display: flex;
    align-items: center;
    border: 1px solid #dee2e6;
    border-radius: 50px;
    background-color: white;
    overflow: hidden;
    flex-shrink: 0;
}

.quantity-control-wrapper .btn {
    background-color: #f8f9fa;
    border: none;
    color: #495057;
    font-weight: bold;
    height: 100%;
    padding: 0 12px;
}

.quantity-input {
    width: 50px;
    text-align: center;
    border: none;
    font-weight: 600;
    padding: 0.5rem 0;
    background-color: transparent;
}

.quantity-input::-webkit-outer-spin-button,
.quantity-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.quantity-input[type=number] {
    -moz-appearance: textfield;
}

/* Control cuando el producto YA está en carrito */
.quantity-in-cart-control {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    background-color: #f8f9fa;
    border-radius: 50px;
    padding: 5px;
    border: 1px solid #dee2e6;
}

.quantity-in-cart-control .btn {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    font-weight: bold;
    background-color: white;
    border: none;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.quantity-info {
    font-weight: 600;
    text-align: center;
}

.quantity-info .subtotal {
    font-size: 0.8rem;
    color: #6c757d;
    display: block;
}

.card-actions {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* --- TEXTO DE STOCK BAJO --- */
.stock-bajo-texto {
    font-size: 0.9rem;
    font-weight: 500;
    color: #E67E22;
    margin-top: 0.5rem;
    margin-bottom: 0.75rem;
}

.stock-bajo-texto strong {
    font-weight: 700;
    color: #D35400;
}

/* --- ENCABEZADO DE TIENDA --- */
.store-header-pro {
    display: none; /* Oculto por defecto en pantallas grandes */
    background: linear-gradient(to top, #ffffff, #f8f9fa);
    padding: 2rem 0;
    margin-bottom: 2.5rem;
    border-bottom: 1px solid #dee2e6;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}

.store-header-pro .container-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
}

.header-branding {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.store-logo {
    height: 70px;
    width: auto;
    object-fit: contain;
    border-radius: 0;
    background-color: transparent;
    border: none;
    filter: drop-shadow(0 0 1px black);
}

.store-logo:hover {
    transform: rotate(5deg) scale(1.1);
}

.store-identity .store-name {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #212529;
}

.store-identity .store-rubro {
    font-size: 1.1rem;
    color: #6c757d;
    margin: 0;
}

/* --- WHATSAPP FLOTANTE --- */
.whatsapp-float-button {
    position: fixed;
    bottom: 110px;
    right: 25px;
    width: 50px;
    height: 50px;
    background-color: #25D366;
    color: #FFF;
    border-radius: 50%;
    font-size: 1.8rem;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    opacity: 0;
    animation: fadeInUp 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) 1s forwards,
               pulse 2.5s infinite 2s ease-in-out;
}

.whatsapp-float-button:hover {
    color: #0f0e0eff !important;
    animation-play-state: paused;
    transform: scale(1.1);
    transition: transform 0.2s;
}

/* --- PAGINACIÓN --- */
.pagination-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #11998e, #38ef7d);
    padding: 1rem;
    border-radius: 10px;
}

.pagination-container button,
.pagination-container a {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background: #fff;
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

/* --- ANIMACIONES GENERALES --- */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(37, 211, 102, 0.4);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
}

.animated-element {
    animation: fadeInUp 0.7s ease-out forwards;
    opacity: 0;
}

/* ========================================
   RESPONSIVE DESIGN
   ======================================== */

/* Móviles */
@media (max-width: 576px) {
    /* Badges más pequeños */
    .product-badge {
        top: 8px;
        left: 8px;
        padding: 4px 8px;
        font-size: 0.7rem;
        border-radius: 12px;
    }
    
    .badge-lowstocks {
        bottom: 8px;
        right: 8px;
        padding: 3px 6px;
        font-size: 0.65rem;
        border-radius: 10px;
    }

    /* WhatsApp button */
    .whatsapp-float-button {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
        bottom: 130px;
        right: 15px;
    }

    /* Paginación */
    .pagination-container {
        display: flex;
    }
}

/* Tablets */
@media (max-width: 768px) {
    /* Mostrar header de tienda solo en móvil */
    .store-header-pro {
        display: block;
    }

    .store-header-pro .container-flex {
        flex-direction: column;
        text-align: center;
    }

    .header-branding {
        flex-direction: column;
    }

    .store-identity .store-name {
        font-size: 1.8rem;
    }
}
</style>
@endpush

@section('contenido')
<div class="container-contenido">

    <header class="store-header-pro animated-element">
        <div class="container container-flex">
            <div class="header-branding">
                {{-- Logo con retraso de animación 0.2s --}}
                <div class="animated-element" style="animation-delay: 0.2s;">
                    @if($tienda->logo_url)
                    @php $logoPath = cloudinary()->image($tienda->logo_url)->toUrl(); @endphp
                    <img src="{{ $logoPath }}" alt="Logo de {{ $tienda->nombre }}" class="store-logo">
                    @else
                    <div
                        class="store-logo d-flex align-items-center justify-content-center fs-1 bg-secondary text-white">
                        {{ substr($tienda->nombre, 0, 1) }}
                    </div>
                    @endif
                </div>
                <div class="store-identity animated-element" style="animation-delay: 0.4s;">
                    <h1 class="store-name">{{ $tienda->nombre }}</h1>
                    <p class="store-rubro">{{ $tienda->rubro }}</p>
                </div>
            </div>
        </div>
    </header>

    <div>
        @livewire('storefront', ['empresa' => $tienda], key($tienda->id))
    </div>

    {{-- BOTÓN FLOTANTE DE WHATSAPP (el CSS ya maneja su animación) --}}
    @if($tienda->telefono_whatsapp)
    <a href="https://wa.me/{{ $tienda->telefono_whatsapp }}?text=Hola,%20vengo%20de%20tu%20tienda%20online%20y%20quisiera%20consultar%20algo."
        class="whatsapp-float-button" target="_blank" title="Contactar por WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    @endif

</div>
@endsection