@extends('welcome.app')
@section('title', 'Tienda de ' . $tienda->nombre)

@push('estilos')
<style>
    
    .product-card-intuitive {
        margin-bottom: 1.5rem;
    }
    
    .tienda-header {
        background: linear-gradient(rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.8)), url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=2073&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        color: white;
    }

    /* --- BADGES (Etiquetas sobre la imagen) --- */
    .product-badge {
        position: relative;
        top: 15px;
        left: 15px;
        padding: 6px 12px;
        font-size: 0.85rem;
        font-weight: bold;
        color: white;
        border-radius: 20px;
        z-index: 2;
        text-transform: uppercase;
    }

    .badge-outofstock {
        background-color: #6c757d; /* Gris oscuro para Agotado */
    }

    .badge-sale {
        background-color: #ffc107;
    }


    .badge-lowstock {
        transform: translate(100%, 700%);
        background-color: #E67E22;
        color: white;
    }


    /* --- BOTONES --- */
    .btn-agotado {
        background-color: #8B8177; /* Marrón-grisáceo */
        color: white;
        border: none;
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }

    .btn-anadir {
        background-color: #D4A778; /* Ocre/arena */
        color: #4B3F35; /* Marrón oscuro */
        border: none;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: background-color 0.2s ease-in-out;
    }

    .btn-anadir:hover {
        background-color: #c59868;
        color: #4B3F35;
    }

    /* --- TEXTO DE STOCK BAJO --- */
    .stock-bajo-texto {
        font-size: 0.9rem;
        font-weight: 500;
        color: #E67E22;
        margin-top: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .stock-bajo-texto strong {
        font-weight: 700;
        color: #D35400;
    }
</style>
@endpush

@section('contenido')
<div class="container-contenido">
    
    {{-- Banner de la Tienda --}}
    <div class="tienda-header text-center p-5 mb-5 rounded shadow-lg">
        <h1 class="display-3 fw-bold">{{ $tienda->nombre }}</h1>
        <!-- <p class="lead">{{ $tienda->descripcion ?? 'Tu tienda de confianza para encontrar los mejores productos.' }}</p> -->
    </div>
        
    {{-- Barra de Filtros --}}
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body">
            <form id="product-filters" class="row g-3 align-items-center">
                <div class="col-md-5">
                     <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fa-solid fa-tags"></i></span>
                        <select name="categoria_id" id="categoria_id_filter" class="form-select border-0 bg-light">
                            <option value="">Todas las categorías</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" id="q_filter" name="q" class="form-control border-0 bg-light" placeholder="Buscar producto por nombre..." value="{{ request('q') }}">
                    </div>
                </div>
                <div class="col-md-auto">
                   <a href="{{ route('tienda.public.index', $tienda) }}" class="btn btn-outline-secondary" title="Limpiar filtros"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div id="product-list-container">
        @include('tienda.producto', ['productos' => $productos, 'tienda' => $tienda])
    </div>
</div>
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let searchTimer;
    const ajaxSearchUrl = "{{ route('tienda.productos.buscar_ajax', $tienda) }}";
    const productListContainer = $('#product-list-container'); // Definimos el contenedor una vez
    const productFiltersForm = $('#product-filters'); // Referencia al formulario

    function fetchProducts() {
        const query = $('#q_filter').val();
        const categoryId = $('#categoria_id_filter').val();
        const categorySelect = $('#categoria_id_filter');

        productListContainer.html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>');

        $.ajax({
            url: ajaxSearchUrl,
            type: 'GET',
            data: { 
                q: query,
                categoria_id: categoryId
            },
            success: function(response) {
                productListContainer.html(response.products_html);

                let currentCategorySelection = categoryId; // Usamos el valor que ya teníamos
                categorySelect.empty().append('<option value="">Todas las categorías</option>');

                if (response.categories && response.categories.length > 0) {
                    $.each(response.categories, function(index, category) {
                        categorySelect.append($('<option>', {
                            value: category.id,
                            text: category.nombre
                        }));
                    });
                }
                
                categorySelect.val(currentCategorySelection);
            },
            error: function() {
                productListContainer.html('<div class="alert alert-danger text-center">Ocurrió un error al cargar los productos.</div>');
            }
        });
    }

    productFiltersForm.on('submit', function(event) {
        event.preventDefault(); 
        
        clearTimeout(searchTimer); 
        
        fetchProducts();
    });

    $('#q_filter').on('keyup', function(event) {
        if (event.key === 'Enter') {
            return;
        }
        clearTimeout(searchTimer);
        searchTimer = setTimeout(fetchProducts, 500); // 500ms de retraso
    });

    $('#categoria_id_filter').on('change', function() {
        fetchProducts();
    });

    productListContainer.on('click', '.pagination a', function(event) {
        event.preventDefault(); 
        const url = $(this).attr('href');
        
        productListContainer.html('<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>');

        $.get(url, function(data) {
            productListContainer.html(data.products_html);
        }).fail(function() {
            productListContainer.html('<div class="alert alert-danger text-center">Error al cargar la página.</div>');
        });
    });
});
</script>
@endpush
@endsection