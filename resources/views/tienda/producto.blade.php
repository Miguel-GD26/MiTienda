{{-- Este es el contenedor que actualizaremos con AJAX --}}
<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4" id="product-grid-container">
    @forelse ($productos as $producto)
        <div class="col">
            <div class="custom-card">
                <div class="img-box">
                    <img src="{{ $producto->imagen_url ? cloudinary()->image($producto->imagen_url)->toUrl() : 'https://via.placeholder.com/300x220.png?text=Producto' }}" 
                         alt="Imagen de {{ $producto->nombre }}">
                </div>
                <div class="custom-content">
                    <h2>{{ $producto->nombre }}</h2>
                    <div class="price">S/.{{ number_format($producto->precio, 2) }}</div>
                    <form action="{{ route('cart.add', $producto) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            <i class="fa-solid fa-cart-shopping me-1"></i> Añadir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center p-5 bg-light rounded">
                <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                <h3 class="text-muted">No se encontraron productos con tu búsqueda</h3>
                <p>Intenta con otras palabras clave.</p>
            </div>
        </div>
    @endforelse
</div>

{{-- La paginación también debe estar dentro del contenedor para actualizarse --}}
<div class="d-flex justify-content-center mt-5" id="product-pagination-container">
    {{ $productos->appends(request()->query())->links() }}
</div>