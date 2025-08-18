@extends('welcome.app')
@section('titulo')
    {{ $tienda->nombre }}
@endsection


@push('estilos')
<style>

#product-grid-container {
    row-gap: 1.2rem; /* ajusta a tu gusto */
}

.tienda-header {
    background: linear-gradient(rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.8)), url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=2073&auto=format&fit=crop');
    background-size: cover;
    background-position: center;
    color: white;
}

/* --- BADGES (Etiquetas sobre la imagen) --- */
.product-badge {
    position: relative;
    top: 15px;
    left: 15px;
    padding: 6px 12px;
    font-size: 0.85rem;
    font-weight: bold;
    color: white;
    border-radius: 20px;
    z-index: 2;
    text-transform: uppercase;
}

.badge-outofstock {
    background-color: #6c757d;
    /* Gris oscuro para Agotado */
}

.badge-sale {
    background-color: #ffc107;
}


.badge-lowstock {
    transform: translate(100%, 700%);
    background-color: #E67E22;
    color: white;
}


/* --- BOTONES --- */
.btn-agotado {
    background-color: #8B8177;
    /* Marrón-grisáceo */
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    font-size: 1rem;
}

.btn-anadir {
    background: linear-gradient(145deg, #2ecc71, #27ae60) !important; /* Degradado verde elegante */
    color: #fff !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important; /* Borde translúcido */
    padding: 0.75rem 1.5rem !important;
    font-size: 1rem !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
    border-radius: 10px !important;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15) !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
}

.btn-anadir:hover {
    background: linear-gradient(145deg, #27ae60, #219150) !important; /* Hover más oscuro */
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 14px rgba(0,0,0,0.2) !important;
}

.btn-anadir:active {
    transform: translateY(0px) !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15) !important;
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

/* --- ESTILOS PARA EL CONTROL DE CANTIDAD CON +/- --- */
.add-to-cart-form, .update-cart-form {
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
.quantity-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.quantity-input[type=number] { -moz-appearance: textfield; }

/* Control +/- cuando el producto YA está en el carrito */
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
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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
    justify-content: center; /* Centra horizontalmente */
    align-items: center;
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


</style>
@endpush

@section('contenido')
<div class="container-contenido">

    {{-- Banner de la Tienda (Estático) --}}
    <div class="tienda-header text-center p-5 mb-5 rounded shadow-lg">
        <h1 class="display-3 fw-bold">{{ $tienda->nombre }}</h1>
    </div>
    @livewire('storefront', ['empresa' => $tienda], key($tienda->id))

</div>
@endsection

