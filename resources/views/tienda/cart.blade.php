@extends('welcome.app')
@section('title', 'Mi Carrito de Compras')

@section('contenido')
<div class="container-contenido py-5">

    {{-- MODIFICADO: La URL de retorno ahora se obtiene del primer producto en el carrito si existe. --}}
    @php
        $returnUrl = $cartItems->isNotEmpty() ? route('tienda.public.index', $cartItems->first()->producto->empresa->slug) : session('url.store_before_login');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Mi Carrito de Compras</h1>
        
        @if($returnUrl)
            <a href="{{ $returnUrl }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-arrow-left me-1"></i> Seguir Comprando
            </a>
        @endif
    </div>

    {{-- Las notificaciones de sesión funcionan igual, no necesitan cambios. --}}
    @if (session('mensaje'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('mensaje') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            {!! session('error') !!} {{-- Usamos {!! !!} para permitir saltos de línea <br> en el mensaje de error --}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- MODIFICADO: La condición ahora usa el método ->isNotEmpty() de la colección Laravel. --}}
    @if($cartItems->isNotEmpty())
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Precio Unitario</th>
                            <th class="text-center" style="width: 150px;">Cantidad</th>
                            <th class="text-center">Subtotal</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- MODIFICADO: El bucle ahora es más simple, iterando sobre objetos 'CartItem'. --}}
                        @foreach($cartItems as $item)
                        {{-- Añadimos una comprobación de seguridad por si el producto fue eliminado de la BD. --}}
                        @if($item->producto) 
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    {{-- MODIFICADO: Acceso a la imagen a través del objeto producto. --}}
                                    @if($item->producto->imagen_url)
                                        <img src="{{ cloudinary()->image($item->producto->imagen_url)->toUrl() }}" width="60" class="me-3 rounded shadow-sm" alt="{{ $item->producto->nombre }}">
                                    @else
                                        <img src="https://via.placeholder.com/60x60.png?text=Img" width="60" class="me-3 rounded shadow-sm" alt="Sin imagen">
                                    @endif
                                    <div>
                                        {{-- MODIFICADO: Acceso a los datos a través de las relaciones de Eloquent. --}}
                                        <div class="fw-bold">{{ $item->producto->nombre }}</div>
                                        <small class="text-muted">De: <a href="{{ route('tienda.public.index', $item->producto->empresa->slug) }}" class="text-muted">{{ $item->producto->empresa->nombre }}</a></small>
                                    </div>
                                </div>
                            </td>
                            {{-- MODIFICADO: El precio se obtiene del producto, no del item del carrito. --}}
                            <td class="text-center">S/.{{ number_format($item->producto->precio, 2) }}</td>
                            <td>
                                <form action="{{ route('cart.update') }}" method="POST" class="d-flex justify-content-center align-items-center">
                                    @csrf
                                    {{-- MODIFICADO: El ID ahora es el ID del producto. --}}
                                    <input type="hidden" name="id" value="{{ $item->producto_id }}">
                                    <input type="number" name="quantity" value="{{ $item->cantidad }}" 
                                           class="form-control form-control-sm text-center" style="width: 70px;" 
                                           min="1" max="{{ $item->producto->stock }}" 
                                           onchange="this.form.submit()">
                                </form>
                            </td>
                            {{-- MODIFICADO: El subtotal se calcula con los datos del objeto. --}}
                            <td class="text-center fw-bold">S/.{{ number_format($item->producto->precio * $item->cantidad, 2) }}</td>
                            <td class="text-center">
                                <form action="{{ route('cart.remove') }}" method="POST">
                                    @csrf
                                    {{-- MODIFICADO: El ID a eliminar es el ID del producto. --}}
                                    <input type="hidden" name="id" value="{{ $item->producto_id }}">
                                    <button type="submit" class="btn btn-sm btn-link text-danger" title="Eliminar">×</button>
                                </form>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row justify-content-end mt-4">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-3">Resumen del Pedido</h3>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>S/.{{ number_format($cartTotal, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                            <span>Total</span>
                            <span>S/.{{ number_format($cartTotal, 2) }}</span>
                        </div>
                        <form action="{{ route('cart.checkout') }}" method="POST" class="d-grid">
                            @csrf
                            {{-- NUEVO: Campo de notas, ya que el controlador lo soporta. --}}
                            <div class="mb-3">
                                <label for="notas" class="form-label small text-muted">Notas para el pedido (opcional):</label>
                                <textarea name="notas" id="notas" class="form-control form-control-sm" rows="2" placeholder="Ej: Dejar en portería, envolver para regalo..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary fw-bold btn-lg">Proceder al Pago</button>
                        </form>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres vaciar todo tu carrito?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-danger">
                            <i class="fa-solid fa-trash-can me-1"></i> Vaciar Carrito
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        {{-- Esta sección para carrito vacío no necesita cambios. --}}
        <div class="text-center p-5 bg-light rounded shadow-sm">
            <i class="fa-solid fa-cart-shopping fa-3x text-muted mb-3"></i>
            <h3>Tu carrito está vacío</h3>
            
            @if(session('url.store_before_login'))
                <p class="text-muted">Parece que aún no has añadido ningún producto.</p>
                <a href="{{ session('url.store_before_login') }}" class="btn btn-primary mt-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Volver a la última tienda visitada
                </a>
            @else
                <p class="text-muted">Para empezar a comprar, primero visita la página principal y elige una tienda.</p>
                <a href="/" class="btn btn-primary mt-2">
                    <i class="fa-solid fa-store me-1"></i> Ir a la Página Principal
                </a>
            @endif
        </div>
    @endif
</div>
@endsection