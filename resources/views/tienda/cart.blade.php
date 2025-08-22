@extends('welcome.app')
@section('title', 'Mi Carrito de Compras')
@push('estilos')
<style>
/* --- ESTILOS PARA EL CONTROL DE CANTIDAD MINIMALISTA --- */
.quantity-control-minimal {
    display: flex;
    align-items: center;
    justify-content: center;
}

.quantity-control-minimal .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border: 1px solid #ddd;
    border-radius: 50%;
    background-color: white;
    color: #555;
    font-size: 1rem;
    line-height: 1;
    padding: 0;
    transition: background-color 0.2s;
}

.quantity-control-minimal .btn:hover {
    background-color: #f7f7f7;
}

.quantity-control-minimal .btn:disabled {
    background-color: #f7f7f7;
    color: #ccc;
    cursor: not-allowed;
}

.quantity-control-minimal .quantity-input {
    width: 50px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 6px;
    margin: 0 0.5rem;
    padding: 0.3rem;
    -moz-appearance: textfield;
}

.quantity-control-minimal .quantity-input::-webkit-outer-spin-button,
.quantity-control-minimal .quantity-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* --- NUEVO ESTILO PARA EL BOTÓN DE REALIZAR PEDIDO --- */
.btn-realizar-pedido {
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

.btn-realizar-pedido:hover {
    background: linear-gradient(145deg, #27ae60, #219150) !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2) !important;
}

.btn-realizar-pedido:active {
    transform: translateY(0px) !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
}

.btn-accion-principal {
    background: linear-gradient(145deg, #0d6efd, #0a58ca) !important;
    /* Degradado azul de Bootstrap */
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
    text-decoration: none !important;
    /* Asegura que no haya subrayado si es un <a> */
}

.btn-accion-principal:hover {
    background: linear-gradient(145deg, #0b5ed7, #0a53be) !important;
    /* Hover más oscuro */
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2) !important;
    color: #fff !important;
    /* Mantiene el color del texto */
}

.btn-accion-principal:active {
    transform: translateY(0px) !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
}

.btn-accion-secundaria {
    background: linear-gradient(145deg, #6c757d, #5a6268) !important;
    /* Degradado gris elegante */
    color: #fff !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    padding: 0.75rem 1.5rem !important;
    font-size: 1rem !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    text-decoration: none !important;
}

.btn-accion-secundaria:hover {
    background: linear-gradient(145deg, #5a6268, #495057) !important;
    /* Hover más oscuro */
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2) !important;
    color: #fff !important;
}

.btn-accion-secundaria:active {
    transform: translateY(0px) !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
}
</style>
@endpush

@section('contenido')
<div class="container-contenido">
    @livewire('shopping-cart')
</div>
@endsection