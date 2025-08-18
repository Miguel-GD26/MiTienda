@extends('welcome.app')
@section('title', 'Detalle de mi Pedido #' . $pedido->id)

@push('estilos')
<style>
    .product-image-sm { width: 60px; height: 60px; object-fit: cover; border-radius: .375rem; }
    
    /* Estilos para la línea de tiempo del estado del pedido */
    .timeline { list-style: none; padding: 0; position: relative; }
    .timeline::before {
        content: ''; position: absolute; top: 0; left: 18px; height: 100%;
        width: 4px; background: #e9ecef; border-radius: 2px;
    }
    .timeline-item { position: relative; margin-bottom: 2rem; }
    .timeline-icon {
        position: absolute; top: 0; left: 0; width: 40px; height: 40px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        z-index: 1;
    }
    .timeline-content { margin-left: 60px; }
    .timeline-item .timeline-icon.active { background-color: var(--bs-success); color: white; }
    .timeline-item .timeline-icon.inactive { background-color: #e9ecef; color: #6c757d; }
    .timeline-item .fw-bold.active { color: var(--bs-success); }
</style>
@endpush

@section('contenido')
    @livewire('order.customer.my-order-detail', ['pedido' => $pedido])
@endsection

