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
<div class="container-contenido py-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Detalle del Pedido #{{ $pedido->id }}</h1>
        <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver a Mis Compras
        </a>
    </div>

    <div class="row g-4">
        {{-- Columna Izquierda: Línea de Tiempo del Estado --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fa-solid fa-truck-fast me-2"></i>Estado del Pedido</h5>
                </div>
                <div class="card-body">
                    @php
                        // Define el orden de los estados y su estado actual
                        $estados = ['pendiente', 'atendido', 'enviado', 'entregado'];
                        $estadoActualIndex = array_search($pedido->estado, $estados);
                        if ($pedido->estado == 'cancelado' || $pedido->estado == 'completado') {
                            $estadoActualIndex = 99; // Un número alto para marcar todo como hecho o cancelado
                        }
                    @endphp

                    <ul class="timeline">
                        @foreach($estados as $index => $estado)
                        
                        <li class="timeline-item">
                            @if($pedido->estado == 'cancelado')
                                <div class="timeline-icon inactive"><i class="fa-solid fa-ban"></i></div>
                            @else
                                <div class="timeline-icon {{ $index <= $estadoActualIndex ? 'active' : 'inactive' }}">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            @endif
                            <div class="timeline-content">
                                <h6 class="fw-bold {{ $index <= $estadoActualIndex ? 'active' : 'text-muted' }}">{{ ucfirst($estado) }}</h6>
                                <p class="small text-muted mb-0">
                                    @switch($estado)
                                        @case('pendiente') Tu pedido ha sido recibido. @break
                                        @case('atendido') La tienda está preparando tu pedido. @break
                                        @case('enviado') Tu pedido está en camino. @break
                                        @case('entregado') ¡Has recibido tu pedido! @break
                                    @endswitch
                                </p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                                @if($pedido->estado == 'pendiente')
                        <div class="card-footer text-center">
                            <p class="small text-muted mb-2">¿Te equivocaste o cambiaste de opinión? Puedes cancelar el pedido ahora.</p>
                            <form action="{{ route('pedidos.cliente.cancelar', $pedido) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres cancelar este pedido? No podrás deshacer esta acción.');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fa-solid fa-ban me-1"></i> Cancelar mi Pedido
                                </button>
                            </form>
                        </div>
                    @endif
                    @if($pedido->estado == 'cancelado')
                        <div class="alert alert-danger text-center">Este pedido ha sido cancelado.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna Derecha: Resumen de la Compra --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h6>Vendido por:</h6>
                            <p class="text-muted">{{ $pedido->empresa->nombre }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Enviado a:</h6>
                            <p class="text-muted mb-0">{{ $pedido->cliente->nombre }}</p>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Resumen de productos:</h6>
                    <ul class="list-group list-group-flush">
                        @foreach($pedido->detalles as $detalle)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div class="d-flex align-items-center">
                                @if($detalle->producto && $detalle->producto->imagen_url)
                                    <img src="{{ cloudinary()->image($detalle->producto->imagen_url)->toUrl() }}" alt="{{ $detalle->producto->nombre }}" class="product-image-sm me-3">
                                @else
                                    <img src="https://via.placeholder.com/60x60.png?text=Img" alt="Sin imagen" class="product-image-sm me-3">
                                @endif
                                <div>
                                    <span>{{ $detalle->cantidad }} x {{ $detalle->producto->nombre ?? 'Producto no disponible' }}</span><br>
                                    <small class="text-muted">S/.{{ number_format($detalle->precio_unitario, 2) }} c/u</small>
                                </div>
                            </div>
                            <span class="fw-bold">S/.{{ number_format($detalle->subtotal, 2) }}</span>
                        </li>
                        @endforeach
                    </ul>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <span>S/.{{ number_format($pedido->total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Envío:</span>
                        <span>A coordinar</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total del Pedido:</span>
                        <span class="text-success">S/.{{ number_format($pedido->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection