<div id="product-list-wrapper">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4" id="product-grid-container">
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">¡Ups! Revisa los siguientes errores:</h4>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @forelse ($productos as $producto)
            @php
                // 1. Usamos el método ->get() de la colección para buscar por la clave (ID del producto).
                $cartItem = $cartItems->get($producto->id);

                // 2. Si se encuentra el item, obtenemos su propiedad ->cantidad. Si no, es 0.
                $quantityInCart = $cartItem ? $cartItem->cantidad : 0;
                
                // 3. El cálculo del stock disponible sigue siendo el mismo.
                $availableStock = $producto->stock - $quantityInCart;
            @endphp

            <div class="col">
                <div class="product-card-intuitive">
                    <div class="card-img-container">
                        <img src="{{ $producto->imagen_url ? cloudinary()->image($producto->imagen_url)->toUrl() : 'https://via.placeholder.com/400x300.png?text=Producto' }}"
                             class="card-img-top" alt="Imagen de {{ $producto->nombre }}">
                        
                        @if ($availableStock <= 0)
                            <div class="product-badge badge-outofstock">Agotado</div>
                        @else
                            @if ($producto->is_on_sale)
                                <div class="product-badge badge-sale">Oferta</div>
                            @endif

                            @if ($availableStock <= App\Models\Producto::UMBRAL_STOCK_BAJO)
                                <div class="product-badge badge-lowstock">¡Últimas unidades!</div>
                            @endif
                        @endif
    
                    </div>

                    <div class="card-body">
                        <h3 class="product-title">{{ $producto->nombre }}</h3>
                        
                        <div class="price-container">
                            @if ($producto->is_on_sale)
                                <span class="sale-price">S/.{{ number_format($producto->precio_oferta, 2) }}</span>
                                <del class="original-price">S/.{{ number_format($producto->precio, 2) }}</del>
                            @else
                                <span class="sale-price">S/.{{ number_format($producto->precio, 2) }}</span>
                            @endif
                        </div>

                        @if ($availableStock > 0 && $availableStock <= App\Models\Producto::UMBRAL_STOCK_BAJO)
                                <p class="stock-bajo-texto text-center">
                                    ¡Solo quedan <strong>{{ $availableStock  }}</strong> en stock!
                                </p>
                        @endif
                        
                    <div class="card-actions">
                        
                        @if ($availableStock <= 0)
                                {{-- Si está agotado, muestra este único botón --}}
                                <button type="button" class="btn btn-agotado w-100 fw-bold" disabled>
                                    <i class="fa-solid fa-ban me-1"></i> Agotado
                                </button>
                        @else
                            @auth 
                                @if(auth()->user()->hasRole('cliente'))
                                    <form action="{{ route('cart.add', $producto) }}" method="POST" class="btn-add-cart">
                                        @csrf
                                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                                            <i class="fa-solid fa-cart-shopping me-1"></i> Añadir
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-secondary w-100 fw-bold" disabled>
                                        <i class="fa-solid fa-user-shield me-1"></i> Solo clientes
                                    </button>
                                @endif
                            @else 
                                <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 fw-bold">
                                    <i class="fa-solid fa-right-to-bracket me-1"></i> Inicia sesión para comprar
                                </a>
                            @endauth
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center p-5 my-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-box2-heart empty-state-icon" viewBox="0 0 16 16">
                      <path d="M8 7.982C9.664 6.309 11.822 5 14 5c.08 0 .16.002.24.006l.342-.857L12.45 2.15a2.5 2.5 0 0 0-2.923-.282L8 3.528 6.473 1.868a2.5 2.5 0 0 0-2.923.282L1.418 4.15l.342.857A3.98 3.98 0 0 1 2 5c2.178 0 4.336 1.31 6 2.982z"/>
                      <path d="M13.75.5a1.25 1.25 0 0 0-1.25 1.25v1.5a.75.75 0 0 0 1.5 0v-1.5A1.25 1.25 0 0 0 13.75.5M1 4.25a.75.75 0 0 0 1.5 0v-1.5a1.25 1.25 0 0 0-2.5 0v1.5a.75.75 0 0 0 1 .75M15 4.25v1.5a.75.75 0 0 0 1.5 0v-1.5a1.25 1.25 0 0 0-2.5 0v1.5a.75.75 0 0 0 1 .75M2.25.5A1.25 1.25 0 0 0 1 1.75v1.5a.75.75 0 0 0 1.5 0v-1.5A1.25 1.25 0 0 0 2.25.5M16 8.5v5.5a1.5 1.5 0 0 1-1.5 1.5H1.5A1.5 1.5 0 0 1 0 14V8.5h16ZM3.75 10.5a.75.75 0 0 0-1.5 0v1.5a.75.75 0 0 0 1.5 0v-1.5Zm2.25 0a.75.75 0 0 0-1.5 0v1.5a.75.75 0 0 0 1.5 0v-1.5Z"/>
                    </svg>
                    <h3 class="mt-4 text-dark-emphasis">No se encontraron productos</h3>
                    <p class="text-muted">Intenta con otras palabras clave o revisa las categorías.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if ($productos->hasPages())
        <div class="d-flex justify-content-center mt-5" id="product-pagination-container">
            {{ $productos->appends(request()->query())->links() }}
        </div>
    @endif
</div>