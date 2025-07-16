@extends('welcome.app')
@section('title', 'Mi Carrito de Compras')

@section('contenido')
<div class="container-contenido py-5">

    @php
        // --- Lógica Centralizada para Determinar la URL de Retorno para el botón superior ---
        $firstItem = !empty($cartItems) ? reset($cartItems) : null;
        // La URL de retorno prioriza la info del carrito, y si no, usa la sesión.
        $returnUrl = $firstItem ? route('tienda.public.index', $firstItem['tienda_slug']) : session('url.store_before_login');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Mi Carrito de Compras</h1>
        
        {{-- El botón "Seguir Comprando" en la cabecera solo aparece si tenemos una URL a la que volver --}}
        @if($returnUrl)
            <a href="{{ $returnUrl }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-arrow-left me-1"></i> Seguir Comprando
            </a>
        @endif
    </div>

    @if (session('mensaje_carrito'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('mensaje_carrito') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(!empty($cartItems))
        {{-- =============================================== --}}
        {{-- VISTA CUANDO EL CARRITO TIENE PRODUCTOS --}}
        {{-- =============================================== --}}
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Precio</th>
                            <th class="text-center" style="width: 120px;">Cantidad</th>
                            <th class="text-center">Subtotal</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $id => $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if(isset($item['image']) && $item['image'])
                                        <img src="{{ $item['image'] }}" width="60" class="me-3 rounded shadow-sm" alt="{{ $item['name'] }}">
                                    @else
                                        <img src="https://via.placeholder.com/60x60.png?text=Img" width="60" class="me-3 rounded shadow-sm" alt="Sin imagen">
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $item['name'] }}</div>
                                        <small class="text-muted">De: <a href="{{ route('tienda.public.index', $item['tienda_slug']) }}" class="text-muted">{{ $item['tienda_nombre'] }}</a></small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">S/.{{ number_format($item['price'], 2) }}</td>
                            <td>
                                <form action="{{ route('cart.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" class="form-control form-control-sm mx-auto" style="width: 80px;" min="1" onchange="this.form.submit()">
                                </form>
                            </td>
                            <td class="text-center fw-bold">S/.{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                            <td class="text-center">
                                <form action="{{ route('cart.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <button type="submit" class="btn btn-sm btn-link text-danger" title="Eliminar">×</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row justify-content-end mt-4">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>S/.{{ number_format($cartTotal, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total</span>
                            <span>S/.{{ number_format($cartTotal, 2) }}</span>
                        </div>
                        <form action="{{ route('cart.checkout') }}" method="POST" class="d-grid mt-3">
                            @csrf
                            <button type="submit" class="btn btn-primary fw-bold btn-lg">Proceder al Pago</button>
                        </form>
                    </div>
                </div>
                <div class="mt-2 text-center">
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-danger">Vaciar Carrito</button>
                    </form>
                </div>
            </div>
        </div>
    @else
        {{-- =============================================== --}}
        {{-- VISTA PARA CARRITO VACÍO - CON LÓGICA CORREGIDA --}}
        {{-- =============================================== --}}
        <div class="text-center p-5 bg-light rounded shadow-sm">
            <i class="fa-solid fa-cart-shopping fa-3x text-muted mb-3"></i>
            <h3>Tu carrito está vacío</h3>
            
            @if(session('url.store_before_login'))
                {{-- Si el middleware guardó una URL en la sesión, la usamos --}}
                <p class="text-muted">Parece que aún no has añadido ningún producto.</p>
                <a href="{{ session('url.store_before_login') }}" class="btn btn-primary mt-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Volver a la última tienda visitada
                </a>
            @else
                {{-- Si no hay URL guardada, mostramos el mensaje y botón de fallback --}}
                <p class="text-muted">Para empezar a comprar, primero visita la página principal y elige una tienda.</p>
                <a href="/" class="btn btn-primary mt-2">
                    <i class="fa-solid fa-store me-1"></i> Ir a la Página Principal
                </a>
            @endif
        </div>
    @endif
</div>
@endsection