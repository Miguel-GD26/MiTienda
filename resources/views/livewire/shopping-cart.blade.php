<div class="py-5">

    {{-- Encabezado de la página --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        
        <h1 class="mb-0 fw-bold">Mi Carrito</h1>
        @if($returnUrl)
        <a href="{{ $returnUrl }}" class="btn btn-accion-principal">
            <i class="fa-solid fa-arrow-left me-1"></i> Seguir Comprando
        </a>
        @endif
    </div>

    @if($cartItems->isNotEmpty())
    <div class="row g-4">
        {{-- Columna izquierda con la tabla de productos --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3">
                    <h2 class="h5 mb-0 fw-semibold"><i class="fa-solid fa-cart-plus"></i> Tus Productos
                        ({{ $cartItems->sum('cantidad') }})</h2>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Producto</th>
                                <th class="text-center">Precio Unitario</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Subtotal</th>
                                <th class="text-end pe-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                            @if($item->producto)
                            <tr wire:key="cart-item-{{ $item->id }}-{{ $item->cantidad }}">
                                <td class="ps-3">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $item->producto->imagen_url ? cloudinary()->image($item->producto->imagen_url)->toUrl() : 'https://via.placeholder.com/60x60.png?text=Img' }}"
                                            width="60" class="me-3 rounded shadow-sm"
                                            alt="{{ $item->producto->nombre }}">
                                        <div>
                                            <div class="fw-bold">{{ $item->producto->nombre }}</div>
                                            <small class="text-muted">Vendido por: <a
                                                    href="{{ route('tienda.public.index', $item->producto->empresa->slug) }}"
                                                    class="text-muted text-decoration-none">{{ $item->producto->empresa->nombre }}</a></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($item->producto->is_on_sale)
                                    <div><strong
                                            class="text-danger">S/.{{ number_format($item->producto->precio_oferta, 2) }}</strong>
                                    </div>
                                    <del
                                        class="text-muted small">S/.{{ number_format($item->producto->precio, 2) }}</del>
                                    @else
                                    <span>S/.{{ number_format($item->producto->precio_final, 2) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="quantity-control-minimal">
                                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->cantidad - 1 }})"
                                            class="btn" wire:loading.attr="disabled"
                                            wire:target="updateQuantity({{ $item->id }})">
                                            <i
                                                class="fa-solid {{ $item->cantidad == 1 ? 'fa-trash-can' : 'fa-minus' }}"></i>
                                        </button>
                                        <input type="number" value="{{ $item->cantidad }}"
                                            wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                                            wire:keydown.enter="updateQuantity({{ $item->id }}, $event.target.value)"
                                            wire:loading.attr="disabled" wire:target="updateQuantity({{ $item->id }})"
                                            class="quantity-input" min="1" max="{{ $item->producto->stock }}">
                                        <button wire:click="updateQuantity({{ $item->id }}, {{ $item->cantidad + 1 }})"
                                            class="btn" wire:loading.attr="disabled"
                                            wire:target="updateQuantity({{ $item->id }})" @if($item->cantidad >=
                                            $item->producto->stock) disabled @endif>
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center fw-bold">
                                    S/.{{ number_format($item->producto->precio_final * $item->cantidad, 2) }}
                                </td>
                                <td class="text-end pe-3">
                                    <button wire:click="confirmRemoveItem({{ $item->id }})"
                                        class="btn btn-sm text-danger" title="Eliminar producto">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3 text-start">
                <button wire:click="confirmClearCart" class="btn btn-sm btn-link text-danger ps-0">
                    <i class="fa-solid fa-xmark me-1"></i> Vaciar Carrito
                </button>
            </div>
        </div>

        {{-- Columna derecha con el resumen de compra --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 position-sticky" style="top: 20px;">
                <div class="card-header bg-light py-3">
                    <h3 class="card-title mb-0 fw-semibold"><i class="fa-solid fa-list-ul"></i> Resumen del Pedido</h3>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>S/.{{ number_format($cartTotal, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                        <span>Total</span>
                        <span>S/.{{ number_format($cartTotal, 2) }}</span>
                    </div>
                    <form wire:submit.prevent="checkout" class="d-grid">
                        <div class="mb-3">
                            <label for="notas" class="form-label small text-muted">Notas adicionales (opcional):</label>
                            <textarea wire:model="notas" id="notas" class="form-control form-control-sm" rows="2"
                                placeholder="Instrucciones especiales..."></textarea>
                        </div>
                        <div class="text-center mt-2">
                            <button type="submit" class="btn btn-realizar-pedido fw-bold w-100">
                                <i class="fa-brands fa-shopify me-2"></i> Realizar Pedido
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @else
    {{-- Bloque para cuando el carrito está vacío --}}
    <div class="text-center p-5 bg-light rounded shadow-sm">
        <i class="fa-solid fa-cart-shopping fa-3x text-muted mb-3"></i>
        <h3>Tu carrito está vacío</h3>
        @if(session('url.store_before_login'))
        <p class="text-muted">Parece que aún no has añadido ningún producto.</p>
        <a href="{{ session('url.store_before_login') }}" class="btn btn-accion-secundaria mt-2">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver a la última tienda visitada
        </a>
        @else
        <p class="text-muted">Para empezar a comprar, visita la página principal y elige una tienda.</p>
        <a href="/" class="btn btn-accion-secundaria mt-2">
            <i class="fa-solid fa-store me-1"></i> Ir a la Página Principal
        </a>
        @endif
    </div>
    @endif

    <!-- Modal para Vaciar Carrito -->
    @if($showConfirmClearModal)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vaciar Carrito</h5><button type="button" class="btn-close"
                        wire:click="$set('showConfirmClearModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que quieres eliminar todos los productos de tu carrito?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        wire:click="$set('showConfirmClearModal', false)">Cancelar</button>
                    <button type="button" class="btn btn-danger" wire:click="clearCart">Sí, Vaciar Carrito</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal para Eliminar Item -->
    @if($showConfirmRemoveModal)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Producto</h5><button type="button" class="btn-close"
                        wire:click="$set('showConfirmRemoveModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que quieres eliminar
                        "<strong>{{ $itemToRemove->producto->nombre ?? '' }}</strong>" de tu carrito?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        wire:click="$set('showConfirmRemoveModal', false)">Cancelar</button>
                    <button type="button" class="btn btn-danger" wire:click="removeItem">Sí, Eliminar</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Backdrop para ambos modales --}}
    @if($showConfirmClearModal || $showConfirmRemoveModal)
    <div class="modal-backdrop fade show"></div>
    @endif
</div>