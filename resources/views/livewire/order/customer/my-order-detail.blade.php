<div class="container-contenido py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Pedido #{{ $pedido->id }}</h1>
            <p class="text-muted mb-0">Realizado el {{ $pedido->created_at->format('d/m/Y') }}</p>
        </div>
        <a href="{{ route('cliente.pedidos') }}" class="btn btn-outline-secondary"><i
                class="fa-solid fa-arrow-left me-1"></i> Volver</a>
    </div>

    <div class="row g-4">
        {{-- Columna de Estado del Pedido --}}
        {{-- AQUÍ ESTÁ LA CORRECCIÓN: Se añade un estilo en línea --}}
        <div class="col-lg-4 mb-4 mb-lg-0" style="position: relative; z-index: 0;">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-truck-fast me-2"></i> Seguimiento del Envío</h5>
                </div>
                <div class="card-body">
                    @if($pedido->estado == 'cancelado')
                    <div class="alert alert-cancelled text-center">
                        <i class="fa-solid fa-ban fa-2x mb-2"></i>
                        <h6 class="fw-bold">Pedido Cancelado</h6>
                    </div>
                    @else
                    @php
                    $estados = ['pendiente', 'atendido', 'enviado', 'entregado'];
                    $estadoActualIndex = array_search($pedido->estado, $estados);
                    @endphp
                    <ol class="stepper">
                        @foreach($estados as $index => $estado)
                        @php
                        $class = '';
                        if ($index < $estadoActualIndex) $class='completed' ; if ($index==$estadoActualIndex)
                            $class='active' ; @endphp <li class="step {{ $class }}">
                            <div class="step-marker">
                                @switch($estado)
                                @case('pendiente') <i class="fa-solid fa-receipt"></i> @break
                                @case('atendido') <i class="fa-solid fa-box-archive"></i> @break
                                @case('enviado') <i class="fa-solid fa-truck"></i> @break
                                @case('entregado') <i class="fa-solid fa-house-chimney"></i> @break
                                @endswitch
                            </div>
                            <div class="step-details">
                                <h6 class="step-title">{{ ucfirst($estado) }}</h6>
                                <p class="step-desc mb-0">
                                    @switch($estado)
                                    @case('pendiente') Tu pedido ha sido recibido. @break
                                    @case('atendido') La tienda está preparando tu paquete. @break
                                    @case('enviado') Tu pedido ha sido enviado y está en camino. @break
                                    @case('entregado') ¡Entregado en tu dirección! @break
                                    @endswitch
                                </p>
                            </div>
                            </li>
                            @endforeach
                    </ol>
                    @endif
                </div>
                @if($pedido->estado == 'pendiente')
                <div class="card-footer bg-transparent text-center ">
                    <p class="small text-muted mb-2">¿Cambiaste de opinión?</p>
                    <button wire:click="openCancelModal" class="btn btn-sm btn-outline-danger"><i
                            class="fa-solid fa-times me-1"></i> Cancelar Pedido</button>
                </div>
                @endif
            </div>
        </div>

        {{-- Columna de Resumen del Pedido (sin cambios) --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-basket-shopping me-2"></i> Resumen de la Compra</h5>
                </div>
                <div class="card-body">
                    <div class="row pb-3 mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h6 class="small text-muted text-uppercase">Vendido por</h6>
                            <p class="mb-0 fw-semibold">{{ $pedido->empresa->nombre }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="small text-muted text-uppercase">Dirección de Envío</h6>
                            <p class="mb-0 fw-semibold">{{ $pedido->cliente->nombre }}</p>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush">
                        @foreach($pedido->detalles as $detalle)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                            <div class="d-flex align-items-center">
                                @if($detalle->producto && $detalle->producto->imagen_url)
                                <img src="{{ cloudinary()->image($detalle->producto->imagen_url)->toUrl() }}"
                                    alt="{{ $detalle->producto->nombre }}" class="product-image-tile me-3">
                                @else
                                <img src="https://via.placeholder.com/60x60.png?text=Img" alt="Sin imagen"
                                    class="product-image-tile me-3">
                                @endif
                                <div>
                                    <span
                                        class="product-name">{{ $detalle->producto->nombre ?? 'Producto no disponible' }}</span><br>
                                    <small class="text-muted">{{ $detalle->cantidad }} x
                                        S/.{{ number_format($detalle->precio_unitario, 2) }}</small>
                                </div>
                            </div>
                            <span class="fw-bold">S/.{{ number_format($detalle->subtotal, 2) }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer bg-light p-4">
                    <div class="d-flex justify-content-between text-muted">
                        <span>Subtotal</span>
                        <span>S/.{{ number_format($pedido->total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted mb-2">
                        <span>Costo de Envío</span>
                        <span>A coordinar</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bolder fs-4 align-items-center">
                        <span>Total a Pagar:</span>
                        <span class="total-amount">S/.{{ number_format($pedido->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Confirmación de Cancelación Mejorado --}}
    @if($showCancelModal)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header border-0 pb-0 justify-content-end">
                    <button type="button" class="btn-close" wire:click="$set('showCancelModal', false)"></button>
                </div>
                <div class="modal-body text-center pt-0 px-4 pb-4">
                    <i class="fa-solid fa-triangle-exclamation fa-4x text-danger mb-3"></i>
                    <h4 class="fw-bold">¿Estás seguro?</h4>
                    <p class="text-muted">
                        Quieres cancelar el pedido <strong>#{{ $pedido->id }}</strong>. <br>
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill fw-bold"
                        wire:click="$set('showCancelModal', false)">
                        <i class="fa-solid fa-times me-1"></i> No, volver
                    </button>
                    <button type="button" class="btn btn-danger rounded-pill fw-bold" wire:click="cancelOrder"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="cancelOrder">
                            <i class="fa-solid fa-trash-can me-1"></i> Sí, cancelar
                        </span>
                        <span wire:loading wire:target="cancelOrder">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Cancelando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>