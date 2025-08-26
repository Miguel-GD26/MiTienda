<div class="container-contenido py-5">
    {{-- ENCABEZADO DE LA SECCIÓN --}}
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h1 class="h2 mb-0" style="font-weight: 600;">Mi Historial de Compras</h1>
    </div>

    {{-- VISTA CUANDO NO HAY PEDIDOS --}}
    @if($pedidos->isEmpty())
    <div class="text-center p-5 bg-white rounded-lg shadow-sm border mt-4">
        <i class="fas fa-shopping-bag fa-4x text-light-gray mb-4"></i>
        <h3 class="mb-2">Tu historial de compras está vacío</h3>
        <p class="text-muted">Cuando realices tu primer pedido, podrás seguir su estado desde aquí.</p>
        <a href="/" class="btn btn-primary btn-lg mt-3">
            <i class="fas fa-store me-2"></i> Empezar a Comprar
        </a>
    </div>
    @else
    {{-- LISTA DE PEDIDOS CON NUEVO DISEÑO --}}
    <div class="order-list-container">
        @foreach($pedidos as $pedido)
        @php
        // Mapeo de estados para colores y iconos (incluyendo color de texto)
        $statusMap = [
        'pendiente' => ['color' => 'warning', 'icon' => 'fa-solid fa-hourglass-half', 'text' => 'white'],
        'atendido' => ['color' => 'info', 'icon' => 'fa-solid fa-box-open', 'text' => 'white'],
        'enviado' => ['color' => 'primary', 'icon' => 'fa-solid fa-truck-fast', 'text' => 'white'],
        'entregado' => ['color' => 'success', 'icon' => 'fa-solid fa-check-circle', 'text' => 'white'],
        'cancelado' => ['color' => 'danger', 'icon' => 'fa-solid fa-times-circle', 'text' => 'white'],
        ];
        $status = $statusMap[$pedido->estado] ?? ['color' => 'secondary', 'icon' => 'fa-solid fa-question-circle',
        'text' => 'white'];
        @endphp

        <a href="{{ route('cliente.pedidos.show', $pedido) }}"
            class="order-card order-card--{{ $pedido->estado }} text-decoration-none">

            {{-- Logo --}}
            <div class="order-card__logo">
                @if($pedido->empresa->logo_url)
                @php
                // REINTRODUCIENDO LA LÓGICA DE CLOUDINARY QUE ME MOSTRASTE
                $logoPath = cloudinary()->image($pedido->empresa->logo_url)->toUrl();
                @endphp
                <img src="{{ $logoPath }}" alt="Logo de {{ $pedido->empresa->nombre }}">
                @else
                <div class="logo-placeholder">
                    {{ substr($pedido->empresa->nombre, 0, 1) }}
                </div>
                @endif
            </div>

            {{-- Info Central --}}
            <div class="order-card__info">
                <h5 class="fw-bolder mb-1">{{ $pedido->empresa->nombre }}</h5>
                <p class="text-muted mb-1 small">
                    Orden #{{ $pedido->id }} &bull; {{ $pedido->created_at->format('d/m/Y') }}
                </p>
                <strong class="text-success fs-5">Total: S/.{{ number_format($pedido->total, 2) }}</strong>
            </div>

            {{-- Status Badge (a la derecha) --}}
            <div class="order-card__status">
                <span class="badge fs-6 rounded-pill bg-{{ $status['color'] }} text-{{ $status['text'] }}">
                    <i class="{{ $status['icon'] }} me-1"></i>
                    {{ ucfirst($pedido->estado) }}
                </span>
            </div>

            {{-- NUEVO: Icono de Ver Detalles --}}
            <div class="order-card__details-arrow">
                <i class="fa-solid fa-eye"></i><span> Ver Detalles</span>
                
            </div>
        </a>
        @endforeach
    </div>

    {{-- PAGINACIÓN --}}
    @if ($pedidos->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $pedidos->links() }}
    </div>
    @endif
    @endif
</div>