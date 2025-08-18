@extends('plantilla.app')
@section('titulo', 'Detalle del Pedido #' . $pedido->id)

@push('estilos')
<style>
    .status-badge { 
        font-size: 1rem; 
        padding: 0.5em 0.9em; 
        font-weight: 600; 
    }
    .product-image-sm { 
        width: 60px; 
        height: 60px; 
        object-fit: cover; 
        border-radius: .375rem; 
    }
    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.65;
    }
</style>
@endpush

@section('contenido')
    @livewire('order.admin.order-detail', ['pedido' => $pedido])
@endsection

