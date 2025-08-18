<div wire:key="order-detail-{{ $pedido->id }}" x-data="{ showCancelModal: false }"
    @close-cancel-modal.window="showCancelModal = false">
    <div class="container-fluid mt-4">
        {{-- ÁREA MODIFICADA --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <h2 class="h3 mb-0"><i class="fa-solid fa-file-lines me-2"></i>Detalle del Pedido #{{ $pedido->id }}</h2>
            
            <div class="d-flex gap-2">
                {{-- ¡NUEVO BOTÓN AQUÍ! --}}
                <button wire:click="downloadPdf" wire:loading.attr="disabled" class="btn btn-danger">
                    <span wire:loading.remove wire:target="downloadPdf">
                        <i class="fa-solid fa-file-pdf me-1"></i> Ver PDF
                    </span>
                    <span wire:loading wire:target="downloadPdf" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>

                <a href="{{ route('pedidos.index', session('pedido_filters', [])) }}" class="btn btn-secondary"><i
                        class="fa-solid fa-arrow-left me-1"></i> Volver</a>
            </div>
        </div>  
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center p-3">
                        <div><span class="fw-bold">Fecha:</span> {{ $pedido->created_at->format('d/m/Y H:i') }}</div>
                        <span
                            class="badge rounded-pill fs-6 @switch($pedido->estado) @case('pendiente') bg-warning text-dark @break @case('atendido') bg-info text-dark @break @case('enviado') bg-primary @break @case('entregado') bg-success @break @case('cancelado') bg-danger @break @endswitch">{{ ucfirst($pedido->estado) }}</span>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="mb-3">Resumen de Productos</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cant.</th>
                                        <th class="text-end">P. Unit.</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pedido->detalles as $detalle)
                                    <tr wire:key="detalle-{{ $detalle->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($detalle->producto?->imagen_url)<img
                                                    src="{{ cloudinary()->image($detalle->producto->imagen_url)->toUrl() }}"
                                                    alt="{{ $detalle->producto->nombre }}"
                                                    style="width:60px;height:60px;object-fit:cover;border-radius:.375rem;"
                                                    class="me-3">@else<div
                                                    class="bg-light d-flex align-items-center justify-content-center me-3"
                                                    style="width:60px;height:60px;border-radius:.375rem;"><i
                                                        class="fa-solid fa-image text-muted"></i></div>@endif
                                                <span>{{ $detalle->producto->nombre ?? 'Producto no disponible' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">{{ $detalle->cantidad }}</td>
                                        <td class="text-end align-middle">
                                            S/.{{ number_format($detalle->precio_unitario, 2) }}</td>
                                        <td class="text-end fw-bold align-middle">
                                            S/.{{ number_format($detalle->subtotal, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-group-divider">
                                    <tr class="fw-bold fs-5">
                                        <td colspan="3" class="text-end border-0">Total:</td>
                                        <td class="text-end border-0 text-success">
                                            S/.{{ number_format($pedido->total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fa-solid fa-user-tag me-2"></i>Información del Cliente</h5>
                    </div>
                    <div class="card-body"><strong>Nombre:</strong>
                        <p class="text-muted">{{ $pedido->cliente->nombre ?? 'N/A' }}</p><strong>Correo:</strong>
                        <p class="text-muted">{{ $pedido->cliente->user->email ?? 'N/A' }}</p><strong>Teléfono:</strong>
                        <p class="text-muted">{{ $pedido->cliente->telefono ?? 'N/A' }}</p>
                    </div>
                </div>
                {{-- ========================================================= --}}
{{--        SECCIÓN "ACCIONES DEL PEDIDO" - VERSIÓN FINAL       --}}
{{-- ========================================================= --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="fa-solid fa-cogs me-2"></i>Acciones del Pedido</h5></div>
    <div class="card-body">
        {{-- ¡LA SOLUCIÓN! Este div con wire:key fuerza a Livewire a reemplazar todo el bloque
             cuando el estado cambia, evitando el error de morphing. --}}
        <div wire:key="acciones-{{ $pedido->estado }}">

            @can('pedido-update-status', $pedido)
            <label class="form-label fw-bold">Actualizar Estado:</label>
            <div class="input-group">
                <select wire:model.live="estadoSeleccionado" class="form-select" @if(in_array($pedido->estado, ['entregado', 'cancelado'])) disabled @endif>
                    @foreach(['pendiente', 'atendido', 'enviado', 'entregado', 'cancelado'] as $estado)
                    @if($estado === 'cancelado' && $pedido->estado !== 'cancelado') @continue @endif
                    <option value="{{ $estado }}">{{ ucfirst($estado) }}</option>
                    @endforeach
                </select>
                <button wire:click="updateStatus" class="btn btn-primary" @if($estadoSeleccionado === $pedido->estado || in_array($pedido->estado, ['entregado', 'cancelado'])) disabled @endif>
                    <span wire:loading.remove wire:target="updateStatus"><i class="fa-solid fa-save"></i></span>
                    <span wire:loading wire:target="updateStatus" class="spinner-border spinner-border-sm"></span>
                </button>
            </div>
            @if(in_array($pedido->estado, ['entregado', 'cancelado']))
                <small class="text-muted d-block mt-2">Este pedido es final.</small>
            @endif
            @endcan

            @can('pedido-cancel', $pedido)
                <hr>
                <button @click="showCancelModal = true" class="btn btn-outline-danger w-100" @if(!in_array($this->pedido->estado, ['pendiente', 'atendido'])) disabled @endif>
                    <i class="fa-solid fa-ban me-2"></i>Cancelar Pedido
                </button>
            @endcan

        </div> {{-- Fin del div con wire:key --}}
    </div>
</div>
            </div>
        </div>
    </div>

    <!-- MODAL CONTROLADO 100% CON ALPINE.JS -->
    <div class="modal fade" :class="{'show': showCancelModal}"
        :style="showCancelModal ? 'display: block;' : 'display: none;'" tabindex="-1" x-transition>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Confirmar
                        Cancelación</h5>
                    <button type="button" class="btn-close" @click="showCancelModal = false"></button>
                </div>
                <div class="modal-body">
                    <p><strong>¿Estás seguro?</strong> El stock será devuelto. Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showCancelModal = false">No, volver</button>
                    <button type="button" class="btn btn-danger" wire:click="cancelOrder">
                        <span wire:loading.remove wire:target="cancelOrder">Sí, Cancelar Pedido</span><span wire:loading
                            wire:target="cancelOrder" class="spinner-border spinner-border-sm"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade" :class="{'show': showCancelModal}"
        :style="showCancelModal ? 'display: block;' : 'display: none;'" x-transition.opacity></div>
</div>