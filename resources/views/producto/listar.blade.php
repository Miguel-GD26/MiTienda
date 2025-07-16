@extends('welcome.app')
@section('title', 'Nuestra Tienda')

    
@push('styles')
<style>
    /* El CSS de las tarjetas va aquí, para no ensuciar el layout principal */
    .card-container {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 100px 50px;
        padding: 50px 0;
        background-color: #f0f2f5; 
    }
    .custom-card {
        font-family: 'Poppins', sans-serif;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        width: 350px;
        max-width: 100%;
        height: 300px;
        background: #FFF;
        border-radius: 20px;
        transition: 0.5s;
        box-shadow: 0 35px 80px rgba(0, 0, 0, 0.15);
    }
    .custom-card:hover { 
        height: 400px; 
    }
    .custom-card .img-box {
        position: absolute;
        top: 20px;
        width: 300px;
        height: 220px;
        background: #333;
        border-radius: 12px;
        overflow: hidden;
        transition: 0.5s;
    }
    .custom-card:hover .img-box {
        top: -100px;
        transform: scale(0.75);
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.2);
    }
    .custom-card .img-box img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .custom-card .custom-content {
        position: absolute;
        top: 252px;
        width: 100%;
        /* 1. Aumentamos la altura para dar espacio a dos líneas */
        height: 60px; 
        padding: 0 30px;
        text-align: center;
        overflow: hidden;
        transition: 0.5s;
    }

    .custom-card:hover .custom-content {
        top: 130px;
        height: 250px;
    }
    .custom-card .custom-content h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #333333;

        /* 2. Propiedades para limitar a 2 líneas */
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* El número de líneas que quieres mostrar */
        -webkit-box-orient: vertical;
    }
    /* MODIFICACIÓN PRINCIPAL: Ocultamos el precio y el botón por defecto */
    .custom-card .custom-content .price,
    .custom-card .custom-content a {
        opacity: 0; /* Hacemos que sean invisibles */
        visibility: hidden; /* Los ocultamos completamente */
        transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
    }

    .custom-card .custom-content .price {
        font-size: 1.4rem;
        font-weight: 600;
        color: #e91e63;
        margin: 10px 0;
    }
    
    .custom-card .custom-content p { 
        color: #333; 
    }
    
    .custom-card .custom-content a {
        position: relative;
        top: 15px;
        display: inline-block;
        padding: 12px 25px;
        text-decoration: none;
        background: #e91e63;
        color: #fff;
        font-weight: 500;
        border-radius: 8px;
    }

    /* MODIFICACIÓN 2: Los mostramos al pasar el mouse */
    .custom-card:hover .custom-content .price,
    .custom-card:hover .custom-content a {
        opacity: 1; /* Los hacemos visibles */
        visibility: visible; /* Los mostramos */
        transition-delay: 0.2s; /* Un pequeño retraso para que aparezcan después de que la tarjeta empiece a expandirse */
    }
</style>
@endpush


@section('contenido')
<div class="container-contenido">
        <div class="text-center mb-5">
            <h1 style="font-family: 'Poppins', sans-serif; font-weight: 900;">Explora Nuestros Productos</h1>
            <p class="text-muted">La mejor selección de productos, solo para ti.</p>
        </div>

        <div class="card-container">
            @forelse ($productos as $producto)
                <div class="custom-card">
                    <div class="img-box">
                        <img src="{{ cloudinary()->image($producto->imagen_url)->toUrl() }} : 'https://via.placeholder.com/300x220.png?text=Producto' }}" 
                            alt="Imagen de {{ $producto->nombre }}">
                    </div>
                    <div class="custom-content">
                        <h2>{{ $producto->nombre }}</h2>
                        <div class="price">S/.{{ number_format($producto->precio, 2) }}</div>
                        <a href="#">Ver Detalles</a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <h3 class="text-muted">No hay productos para mostrar en este momento.</h3>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $productos->links() }}
        </div>
</div>
@endsection