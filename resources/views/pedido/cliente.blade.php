@extends('welcome.app')
@section('title', 'Mis Compras')

@push('estilos')
<style>
    .text{color:white}
    .status-badge { font-size: 1rem; padding: 0.5em 0.9em; font-weight: 600; }
    .pedido-card {
        transition: box-shadow 0.2s ease-in-out;
    }
    .pedido-card:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endpush

@section('contenido')
    @livewire('order.customer.my-orders')
@endsection

