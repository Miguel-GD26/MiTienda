<a href="{{ route('cart.index') }}" class="nav-link position-relative" aria-label="Ver carrito de compras">
    <i class="fa-solid fa-cart-shopping"></i>
    @if($cartCount > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7em;">
            {{ $cartCount }}
            <span class="visually-hidden"></span>
        </span>
    @endif
</a>