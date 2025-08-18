@extends('plantilla.app')

@section('titulo', 'Listado de Productos')

@section('contenido')
<main class="app-main">
    @livewire('product-management')
</main>
@endsection

@push('estilos')
{{-- Estilo para el círculo del icono, si un producto no tiene imagen --}}
<style>
    .icon-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border-radius: 50%;
    }
    

    .modal-dialog-scrollable .modal-content .modal-footer {
        position: sticky;
        bottom: 0;
        background-color: white;
        z-index: 1055; 
    }
    
</style>
@endpush

