@extends('welcome.app')
@section('contenido')
<div class="carousel-wrapper">
    <div id="blog-carousel" class="carousel slide overlay-bottom vh-100" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="carousel-img" src="{{ asset('assets/img/carousel-1.jpg') }}" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center text-center">
                    <h2 class="text-white font-weight-bold mb-2">¿Tienes un negocio?</h2>
                    <h1 class="display-4 text-primary font-weight-bold mb-2">Vende online fácil</h1>
                    <p class="text-white mb-3">Tu tienda + pedidos por WhatsApp</p>
                </div>
            </div>
        
            <div class="carousel-item">
                <img class="carousel-img" src="{{ asset('assets/img/carousel-2.jpg') }}" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center text-center">
                    <h2 class="text-white font-weight-bold mb-2">Crea tu catálogo</h2>
                    <h1 class="display-4 text-primary font-weight-bold mb-2">Link único para tus clientes</h1>
                    <p class="text-white mb-3">Recibe pedidos al instante</p>
                </div>
            </div>
        </div>

        <a class="carousel-control-prev" href="#blog-carousel" data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" href="#blog-carousel" data-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>
    </div>
</div>
@endsection
