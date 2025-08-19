<div id="product-list-wrapper">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4" id="product-grid-container">
        @forelse ($productos as $producto)
        @php
        $cartItem = $cartItems->get($producto->id);
        $quantityInCart = $cartItem ? $cartItem->cantidad : 0;
        $availableStock = $producto->stock - $quantityInCart;
        @endphp

        <div class="col">
            <div class="card product-card-intuitive h-100 shadow-sm">
                <div class="card-img-container position-relative">
                    {{-- El click ahora llama al método de Livewire --}}
                    <a href="#" wire:click.prevent="openProductModal({{ $producto->id }})">
                        <img src="{{ $producto->imagen_url ? cloudinary()->image($producto->imagen_url)->toUrl() : 'https://via.placeholder.com/400x300.png?text=Producto' }}"
                            class="card-img-top" alt="Imagen de {{ $producto->nombre }}">
                    </a>
                    @if ($availableStock <= 0) <div class="product-badge badge-outofstock">Agotado
                </div>
                @else
                @if ($producto->is_on_sale)<div class="product-badge badge-sale">Oferta</div>@endif
                @if ($availableStock <= 5)<div class="product-badge badge-lowstock"
                    style="top: 55px; background-color: #E67E22;">¡Últimas unidades!
            </div>@endif
            @endif
        </div>
        <div class="card-body d-flex flex-column">
            <h3 class="product-title">
                {{-- También aquí --}}
                <a href="#" class="text-dark text-decoration-none"
                    wire:click.prevent="openProductModal({{ $producto->id }})">
                    {{ $producto->nombre }}
                </a>
            </h3>

            {{-- Mostramos la descripción corta --}}
            @if($producto->descripcion)
            <p class="product-description text-muted small">
                {{ Str::limit($producto->descripcion, 80) }}
                @if(strlen($producto->descripcion) > 80)
                {{-- Y el enlace "Ver más" que también abre el modal --}}
                <a href="#" class="text-primary fw-bold" wire:click.prevent="openProductModal({{ $producto->id }})">Ver
                    más</a>
                @endif
            </p>
            @endif

            <div class="price-container">
                @if ($producto->is_on_sale)
                <span class="sale-price">S/. {{ number_format($producto->precio_oferta, 2) }}</span>
                <del class="original-price">S/. {{ number_format($producto->precio, 2) }}</del>
                @else
                <span class="sale-price">S/. {{ number_format($producto->precio, 2) }}</span>
                @endif
            </div>
            @if ($availableStock > 0 && $availableStock <= 5) <p class="stock-bajo-texto text-center">¡Solo quedan
                <strong>{{ $availableStock }}</strong> en stock!</p>@endif
                <div class="card-actions mt-auto">
                    @if ($quantityInCart == 0 && $producto->stock <= 0) <button type="button"
                        class="btn btn-agotado w-100 fw-bold" disabled><i class="fa-solid fa-ban me-1"></i>
                        Agotado</button>
                        @else
                        @auth
                        @if(auth()->user()->hasRole('cliente'))
                        @if($quantityInCart == 0)
                        {{-- ESTADO 1: No en carrito. Usa wire:click para llamar a addToCart() --}}
                        <div class="add-to-cart-form" x-data="{ quantity: 1, stock: {{ $availableStock }} }">
                            <div class="quantity-control-wrapper">
                                <button type="button" class="btn" @click="if (quantity > 1) quantity--">-</button>
                                <input type="number" x-model.number="quantity" min="1" :max="stock"
                                    aria-label="Cantidad" class="quantity-input">
                                <button type="button" class="btn" @click="if (quantity < stock) quantity++">+</button>
                            </div>
                            <button type="button" wire:click="addToCart({{ $producto->id }}, quantity)"
                                wire:loading.attr="disabled" wire:target="addToCart({{ $producto->id }})"
                                class="btn btn-anadir w-100 fw-bold">
                                <i class="fa-solid fa-cart-shopping me-1"></i> Añadir
                            </button>
                        </div>
                        @else
                        {{-- ESTADO 2: Ya en carrito. Usa wire:click para llamar a updateCartItem() --}}
                        <div class="quantity-in-cart-control">
                            <button type="button"
                                wire:click="updateCartItem({{ $producto->id }}, {{ $quantityInCart - 1 }})"
                                wire:loading.attr="disabled" wire:target="updateCartItem({{ $producto->id }})"
                                class="btn" title="Quitar uno">
                                <i class="fa-solid {{ $quantityInCart == 1 ? 'fa-trash-can' : 'fa-minus' }}"></i>
                            </button>
                            <div class="quantity-info">
                                <span>{{ $quantityInCart }} en carrito</span>
                                <span class="subtotal">S/
                                    {{ number_format($producto->precio_final * $quantityInCart, 2) }}</span>
                            </div>
                            <button type="button"
                                wire:click="updateCartItem({{ $producto->id }}, {{ $quantityInCart + 1 }})"
                                wire:loading.attr="disabled" wire:target="updateCartItem({{ $producto->id }})"
                                class="btn" title="Añadir otro" @if($availableStock <=0) disabled @endif>
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                        @endif
                        @else
                        <button type="button" class="btn btn-login-comprar w-100 fw-bold" disabled><i
                                class="fa-solid fa-user-shield me-1"></i> Solo clientes</button>
                        @endif
                        @else
                        <a href="{{ route('login', ['redirect' => request()->fullUrl(), 'add_product' => $producto->id]) }}"
                            class="btn btn-anadir w-100 fw-bold">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Inicia sesión para comprar
                        </a>
                        <!-- <a href="{{ route('login') }}" class="btn btn-login-comprar w-100 fw-bold"><i class="fa-solid fa-right-to-bracket me-1"></i> Inicia sesión para comprar</a> -->
                        @endauth
                        @endif
                </div>
        </div>
    </div>
</div>
@empty
<div class="col-12">
    <div class="text-center p-5 my-5">
        <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
        <h3 class="mt-4 text-dark-emphasis">No se encontraron productos</h3>
        <p class="text-muted">Intenta con otros filtros o busca con otras palabras.</p>
    </div>
</div>
@endforelse
</div>
@if ($productos->hasPages())
<div class="d-flex justify-content-center mt-5">
    {{ $productos->links() }}
</div>
@endif


@if($showProductModal && $selectedProduct)
<div class="modal fade show" style="display: block;" tabindex="-1" @keydown.escape.window="$wire.closeProductModal()">

    {{-- ¡AQUÍ ESTÁ LA CORRECCIÓN! Añadimos 'modal-dialog-scrollable' --}}
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $selectedProduct->nombre }}</h5>
                <button type="button" class="btn-close" wire:click="closeProductModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5 mb-3 mb-md-0">
                        <img src="{{ $selectedProduct->imagen_url ? cloudinary()->image($selectedProduct->imagen_url)->toUrl() : 'https://via.placeholder.com/400x300.png?text=Producto' }}"
                            class="img-fluid rounded" alt="Imagen de {{ $selectedProduct->nombre }}">
                    </div>
                    <div class="col-md-7">
                        <h3 class="fw-bold">{{ $selectedProduct->nombre }}</h3>
                        <div class="price-container fs-4 mb-3">
                            @if ($selectedProduct->is_on_sale)
                            <span class='sale-price'>S/. {{ number_format($selectedProduct->precio_oferta, 2) }}</span>
                            <del class='original-price'>S/. {{ number_format($selectedProduct->precio, 2) }}</del>
                            @else
                            <span class='sale-price'>S/. {{ number_format($selectedProduct->precio, 2) }}</span>
                            @endif
                        </div>
                        <h6 class="text-muted border-top pt-3 mt-3">Descripción:</h6>
                        <p style="white-space: pre-wrap;">{{ preg_replace('/^/m', '- ', $selectedProduct->descripcion ?:
                             'Este producto no tiene una descripción detallada.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
@endif
</div>