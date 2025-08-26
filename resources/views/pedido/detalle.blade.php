@extends('welcome.app')
@section('title', 'Detalle de mi Pedido #' . $pedido->id)

@push('estilos')
<style>
    /* === VARIABLES CSS === */
    :root {
        /* Colores principales */
        --color-primary: #4f46e5;
        --color-success: #10b981;
        --color-success-light: #34d399;
        --color-danger: #ef4444;

        /* Escala de grises */
        --color-gray-200: #e5e7eb;
        --color-gray-300: #d1d5db;
        --color-gray-400: #9ca3af;
        --color-gray-600: #4b5563;
        --color-gray-800: #1f2937;
        --color-gray-900: #111827;
        --color-white: #ffffff;

        /* Transiciones */
        --transition-normal: 0.2s ease-out;
        --transition-slow: 0.3s ease-out;

        /* Bordes */
        --border-radius-base: 0.5rem;
        --border-radius-md: 0.75rem;
    }

    /* === STEPPER COMPONENT === */
    .stepper {
        list-style: none;
        padding: 0;
        margin: 0;
        position: relative;
    }

    .step {
        display: flex;
        position: relative;
        align-items: flex-start;
        min-height: 80px;
        padding-bottom: 0.5rem;
    }

    /* Línea conectora */
    .step:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 22px;
        top: 46px;
        width: 2px;
        height: calc(100% - 46px + 0.5rem);
        background: linear-gradient(to bottom, var(--color-gray-300), var(--color-gray-200));
        z-index: 1;
        transition: background var(--transition-normal);
    }

    /* === MARKER STATES === */
    .step-marker {
        flex-shrink: 0;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        font-weight: 700;
        position: relative;
        z-index: 10;
        transition: all var(--transition-slow);

        /* Estado base */
        background-color: var(--color-white);
        border: 3px solid var(--color-gray-300);
        color: var(--color-gray-400);
    }

    /*ESTADO: COMPLETADO */
    .step.completed .step-marker {
        background: linear-gradient(135deg, var(--color-success), var(--color-success-light));
        border-color: var(--color-success);
        color: var(--color-white);
        transform: scale(1.05);
    }

    /* ESTADO: ACTIVO */
    .step.active .step-marker {
        background-color: var(--color-white);
        border-color: var(--color-primary);
        color: var(--color-primary);
        box-shadow: 0 0 0 6px rgba(79, 70, 229, 0.1),
            0 4px 12px rgba(79, 70, 229, 0.25);
        transform: scale(1.08);
        animation: pulse-ring 2s infinite;
    }

    /* Animación de pulso para el estado activo */
    @keyframes pulse-ring {

        0%,
        100% {
            box-shadow: 0 0 0 6px rgba(79, 70, 229, 0.1),
                0 4px 12px rgba(79, 70, 229, 0.25);
        }

        50% {
            box-shadow: 0 0 0 8px rgba(79, 70, 229, 0.15),
                0 4px 12px rgba(79, 70, 229, 0.25);
        }
    }

    /* Línea verde para pasos completados */
    .step.completed:not(:last-child)::before {
        background: linear-gradient(to bottom, var(--color-success), var(--color-success-light));
        width: 3px;
        left: 21.5px;
    }

    /* === STEP CONTENT === */
    .step-details {
        margin-left: 1.25rem;
        flex: 1;
        min-width: 0;
    }

    .step-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        line-height: 1.4;
        transition: all var(--transition-normal);
    }

    .step-desc {
        font-size: 0.9rem;
        margin: 0;
        line-height: 1.5;
        transition: all var(--transition-normal);
    }

    /* === TEXT STATES === */
    .step:not(.completed):not(.active) .step-title {
        color: var(--color-gray-400);
    }

    .step:not(.completed):not(.active) .step-desc {
        color: var(--color-gray-400);
    }

    .step.active .step-title {
        color: var(--color-gray-900);
        font-weight: 700;
    }

    .step.active .step-desc {
        color: var(--color-gray-600);
    }

    .step.completed .step-title {
        color: var(--color-gray-800);
        font-weight: 600;
    }

    .step.completed .step-desc {
        color: var(--color-gray-600);
    }

    /* === PRODUCTO === */
    .product-image-tile {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: var(--border-radius-base);
        border: 2px solid var(--color-gray-200);
        transition: all var(--transition-normal);
    }

    .product-name {
        font-weight: 600;
        color: var(--color-gray-800);
        margin-bottom: 0.25rem;
    }

    .total-amount {
        color: var(--color-primary);
        font-weight: 800;
        font-size: 1.25rem;
    }

    /* === ALERTAS === */
    .alert-cancelled {
        background: linear-gradient(135deg, #fef2f2, #fde8e8);
        border: 2px solid #fecaca;
        color: var(--color-danger);
        border-radius: var(--border-radius-base);
        padding: 1rem;
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    /* === RESPONSIVE === */
    @media (max-width: 768px) {
        .step-marker {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .step:not(:last-child)::before {
            left: 19px;
            top: 40px;
            height: calc(100% - 40px + 0.5rem);
        }

        .step.completed:not(:last-child)::before {
            left: 18.5px;
        }

        .step-details {
            margin-left: 1rem;
        }

        .step-title {
            font-size: 1rem;
        }

        .step-desc {
            font-size: 0.875rem;
        }

        .product-image-tile {
            width: 56px;
            height: 56px;
        }
    }

    @media (max-width: 480px) {
        .step-marker {
            width: 36px;
            height: 36px;
            font-size: 0.9rem;
        }

        .step:not(:last-child)::before {
            left: 17px;
            top: 36px;
            height: calc(100% - 36px + 0.5rem);
        }

        .step.completed:not(:last-child)::before {
            left: 16.5px;
        }

        .step-details {
            margin-left: 0.875rem;
        }
    }

    /* === ACCESIBILIDAD === */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }

        .step-marker {
            transform: none !important;
        }
    }

    /* --- Mejora para Botones del Modal --- */

    .btn-danger.rounded-pill {
        transition: all 0.2s ease-in-out;
        /* Animación suave para todos los cambios */
    }

    /* Efecto hover para el botón "Sí, cancelar" */
    .btn-danger.rounded-pill:hover {
        background-color: #bb2d3b;
        /* Un rojo ligeramente más oscuro */
        border-color: #b02a37;
        /* Un borde también más oscuro */
        transform: translateY(-2px);
        /* Eleva el botón ligeramente */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        /* Añade una sombra para dar profundidad */
    }

    /* Opcional: Efecto hover para el botón secundario "No, volver" */
    .btn-outline-secondary.rounded-pill {
        transition: all 0.2s ease-in-out;
    }

    .btn-outline-secondary.rounded-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
</style>
@endpush

@section('contenido')
@livewire('order.customer.my-order-detail', ['pedido' => $pedido])
@endsection