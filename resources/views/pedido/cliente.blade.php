@extends('welcome.app')
@section('title', 'Mis Compras')

@push('estilos')
<style>
    /* ========================================
   ESTILOS PARA HISTORIAL DE COMPRAS (RESPONSIVE)
   ======================================== */

.order-list-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    /* Espacio entre tarjetas */
}

.order-card {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    /* Espacio entre logo, info y status */
    padding: 1.25rem;
    background-color: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.04);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    position: relative;
    overflow: hidden;
    /* Necesario para el acento de color */
    color: #212529;
}

/* El "Plus": la barra de color lateral */
.order-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 6px;
    height: 100%;
    background-color: #6c757d;
    /* Color por defecto */
    transition: width 0.2s ease;
}

.order-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08);
}

.order-card:hover::before {
    width: 8px;
    /* La barra se hace más ancha al pasar el mouse */
}

/* Colores de la barra lateral según el estado */
.order-card--pendiente::before {
    background-color: var(--bs-warning);
}

.order-card--atendido::before {
    background-color: var(--bs-info);
}

.order-card--enviado::before {
    background-color: var(--bs-primary);
}

.order-card--entregado::before {
    background-color: var(--bs-success);
}

.order-card--cancelado::before {
    background-color: var(--bs-danger);
}

/* Estilos del Logo */
.order-card__logo {
    flex-shrink: 0;
}

.order-card__logo img,
.order-card__logo .logo-placeholder {
    height: 70px;
    width: auto;
    object-fit: contain;
    border-radius: 0;
    background-color: transparent;
    border: none;
    filter: drop-shadow(0 0 1px black);
}

.logo-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    font-weight: bold;
    color: #495057;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
}

/* Info central */
.order-card__info {
    flex-grow: 1;
    /* Ocupa el espacio disponible */
}

/* Status (badge) */
.order-card__status {
    margin-left: auto;
    /* Empuja el status a la derecha */
    flex-shrink: 0;
}

/* Ajustes para el texto del badge */
.badge {
    padding: 0.6em 1em;
    font-weight: 600;
}

/* ========================================
   RESPONSIVE
   ======================================== */

/* Tablets */
@media (max-width: 768px) {
    .order-card {
        gap: 1rem;
        padding: 1rem;
    }
    
    .order-card__logo img,
    .order-card__logo .logo-placeholder {
        height: 60px;
    }
}

/* Móviles */
@media (max-width: 576px) {
    .order-card {
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
    }
    
    /* Barra de color horizontal arriba */
    .order-card::before {
        width: 100%;
        height: 4px;
        top: 0;
        left: 0;
    }
    
    .order-card__logo img,
    .order-card__logo .logo-placeholder {
        height: 50px;
    }
    
    .order-card__status {
        margin-left: 0;
    }
}

/* Touch devices */
@media (hover: none) {
    .order-card:hover {
        transform: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.04);
    }
    
    .order-card:hover::before {
        width: 6px;
    }
}
/* Icono de ver detalles */
.order-card__details {
    flex-shrink: 0;
    margin-left: 0.75rem;
    opacity: 0.6;
    transition: all 0.2s ease;
}

.order-card__details span {
    font-size: 1.1rem;
}

.order-card:hover .order-card__details {
    opacity: 1;
    transform: translateX(3px);
}

/* Responsive para el icono */
@media (max-width: 576px) {
    .order-card__details {
        align-self: center;
        margin-left: 0;
        margin-top: 0.5rem;
    }
    
    .order-card:hover .order-card__details {
        transform: translateY(-2px);
    }
    .order-card__details-arrow span {
        opacity: 1 !important;
        transform: translateX(0) !important;
    }
}
.order-card:hover .order-card__details-arrow {
    color: var(--bs-primary); /* El texto cambia a color primario */
}

.order-card:hover .order-card__details-arrow span {
    opacity: 1; /* La flecha aparece */
    transform: translateX(0); /* La flecha se desliza a su posición */
}
.order-card__details-arrow span {
    opacity: 0;
    transform: translateX(-8px);
    transition: opacity 0.25s ease, transform 0.25s ease;
}

@keyframes fadeInSlideUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush

@section('contenido')
@livewire('order.customer.my-orders')
@endsection