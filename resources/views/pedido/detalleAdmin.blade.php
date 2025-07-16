@extends('plantilla.app')
@section('titulo', 'Detalle del Pedido #' . $pedido->id)

@push('estilos')
<style>
    .status-badge { font-size: 1rem; padding: 0.5em 0.9em; font-weight: 600; }
    .product-image-sm { width: 60px; height: 60px; object-fit: cover; border-radius: .375rem; }
    
    /* Estilo para el botón de guardar cuando está deshabilitado */
    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.65;
    }
</style>
@endpush

@section('contenido')
<main class="app-main">
    {{-- 
      Inicializamos Alpine.js con dos variables:
      - estadoActual: Guarda el estado inicial del pedido para compararlo.
      - estadoSeleccionado: Se actualiza en tiempo real cuando el admin elige una nueva opción.
    --}}
    <div class="container-fluid mt-4" 
         x-data="{ 
            estadoActual: '{{ $pedido->estado }}',
            estadoSeleccionado: '{{ $pedido->estado }}'
         }">
        
        {{-- Cabecera con título y botón para volver --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0">
                <i class="fa-solid fa-file-lines me-2"></i>
                Detalle del Pedido #{{ $pedido->id }}
            </h2>
            <a href="{{ route('pedidos.index', session('pedido_filters', [])) }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al Listado
            </a>
        </div>
        
        {{-- Mensaje de éxito al actualizar --}}
        @if (session('mensaje'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('mensaje') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            {{-- Columna Izquierda: Detalles del Pedido y Productos --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center p-3">
                        <div>
                            <span class="fw-bold">Fecha del Pedido:</span> {{ $pedido->created_at->format('d/m/Y H:i') }}
                        </div>
                        <span class="badge rounded-pill status-badge
                        @switch($pedido->estado)
                            @case('pendiente') bg-warning text-dark @break
                            @case('atendido') bg-info text-dark @break
                            @case('enviado') bg-dark @break
                            @case('entregado') bg-success @break
                            @case('cancelado') bg-danger @break
                        @endswitch
                        ">{{ ucfirst($pedido->estado) }}</span>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="mb-3">Resumen de Productos</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-end">Precio Unit.</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pedido->detalles as $detalle)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($detalle->producto && $detalle->producto->imagen_url)
                                                    <img src="{{ cloudinary()->image($detalle->producto->imagen_url)->toUrl() }}" alt="{{ $detalle->producto->nombre }}" class="product-image-sm me-3">
                                                @else
                                                    <img src="https://via.placeholder.com/60x60.png?text=Img" alt="Sin imagen" class="product-image-sm me-3">
                                                @endif
                                                <span>{{ $detalle->producto->nombre ?? 'Producto no disponible' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">{{ $detalle->cantidad }}</td>
                                        <td class="text-end align-middle">S/.{{ number_format($detalle->precio_unitario, 2) }}</td>
                                        <td class="text-end fw-bold align-middle">S/.{{ number_format($detalle->subtotal, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-group-divider">
                                    <tr class="fw-bold fs-5">
                                        <td colspan="3" class="text-end border-0">Total del Pedido:</td>
                                        <td class="text-end border-0 text-success">S/.{{ number_format($pedido->total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna Derecha: Información del Cliente y Acciones --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fa-solid fa-user-tag me-2"></i>Información del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <strong>Nombre:</strong>
                        <p class="text-muted">{{ $pedido->cliente->nombre ?? 'No disponible' }}</p>
                        <strong>Correo:</strong>
                        <p class="text-muted">{{ $pedido->cliente->correo ?? 'No disponible' }}</p>
                        <strong>Teléfono:</strong>
                        <p class="text-muted">{{ $pedido->cliente->telefono ?? 'No proporcionado' }}</p>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                         <h5 class="mb-0"><i class="fa-solid fa-cogs me-2"></i>Acciones del Pedido</h5>
                    </div>
                    <div class="card-body">
                        @can('pedido-update-status', $pedido)
                        <form action="{{ route('pedidos.updateStatus', $pedido) }}" method="POST">
                           @csrf
                           <label for="estado" class="form-label fw-bold">Actualizar Estado:</label>
                           <div class="input-group">
                               <select name="estado" id="estado" class="form-select" x-model="estadoSeleccionado"
                                       @if(in_array($pedido->estado, ['completado', 'cancelado'])) disabled @endif>
                                   @foreach(['pendiente', 'atendido', 'enviado', 'entregado'] as $estado)
                                       <option value="{{ $estado }}" {{ $pedido->estado == $estado ? 'selected' : '' }}>
                                           {{ ucfirst($estado) }}
                                       </option>
                                   @endforeach
                               </select>
                               <button type="submit" class="btn btn-primary" :disabled="estadoSeleccionado === estadoActual" data-bs-toggle="tooltip" title="Guardar nuevo estado">
                                   <i class="fa-solid fa-floppy-disk"></i>
                               </button>
                           </div>
                           @if(in_array($pedido->estado, ['completado', 'cancelado']))
                                <small class="text-muted d-block mt-2">Este pedido ya no puede cambiar de estado.</small>
                           @endif
                       </form>
                       @endcan
                       
                       @can('pedido-cancel', $pedido)
                       @if($pedido->estado !== 'cancelado' && $pedido->estado !== 'completado')
                           <hr>
                           <p class="small text-muted mb-2">Si el pedido debe ser anulado, puedes cancelarlo aquí.</p>
                           <form action="{{ route('pedidos.destroy', $pedido) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres cancelar este pedido?');">
                               @csrf
                               @method('DELETE')
                               <button type="submit" class="btn btn-outline-danger w-100">
                                   <i class="fa-solid fa-ban me-2"></i>Cancelar Pedido
                               </button>
                           </form>
                       @endif
                       @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activar el menú correspondiente en el sidebar
        const menuGestion = document.getElementById('mnuGestion');
        if (menuGestion) menuGestion.classList.add('menu-open');
        const itemPedido = document.getElementById('itemPedido');
        if (itemPedido) itemPedido.classList.add('active');

        // Inicializar tooltips de Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush