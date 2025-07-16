@extends('welcome.app')
@section('title', 'Tienda de ' . $tienda->nombre)

@push('estilos')
<style>
    .tienda-header {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1556742502-ec7c0e9f34b1?q=80&w=1974&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        color: white;
    }
    .product-card-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    .custom-card { /* Este estilo lo tenías tú, lo mantenemos */
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background-color: #fff;
    }
    .custom-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
    .custom-card .img-box {
        height: 200px;
        overflow: hidden;
    }
    .custom-card .img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .custom-card .custom-content {
        padding: 1.25rem;
        text-align: center;
    }
    .custom-card h2 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .custom-card .price {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--bs-primary);
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('contenido')
<div class="container-contenido">
    
    {{-- Banner de la Tienda --}}
    <div class="tienda-header text-center p-5 mb-5 rounded shadow-lg">
        <h1 class="display-4 fw-bold">{{ $tienda->nombre }}</h1>
        <p class="lead">{{ $tienda->descripcion ?? 'Tu tienda de confianza para encontrar los mejores productos.' }}</p>
    </div>
        
    {{-- Barra de Filtros --}}
    <div class="card shadow-sm border-0 mb-5 sticky-top" style="top: 1rem; z-index: 1020;">
        <div class="card-body">
            <form id="product-filters" class="row g-3 align-items-center">
                <div class="col-md-5">
                     <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fa-solid fa-tags"></i></span>
                        {{-- El select de categorías ahora tiene un ID para poder manipularlo --}}
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

    {{-- Contenedor principal para productos y paginación --}}
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

    function fetchProducts() {
        const query = $('#q_filter').val();
        const categoryId = $('#categoria_id_filter').val();
        const productListContainer = $('#product-list-container');
        const categorySelect = $('#categoria_id_filter');

        productListContainer.html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div></div>');

        $.ajax({
            url: ajaxSearchUrl,
            type: 'GET',
            data: { 
                q: query,
                categoria_id: categoryId
            },
            success: function(response) {
                // 1. Reemplazar la cuadrícula de productos
                productListContainer.html(response.products_html);

                // 2. Actualizar las opciones del select de categorías
                let currentCategorySelection = categorySelect.val(); // Guardar la selección actual
                categorySelect.empty().append('<option value="">Todas las categorías</option>'); // Limpiar y añadir la opción por defecto

                // 3. Llenar el select con las categorías actualizadas
                if (response.categories && response.categories.length > 0) {
                    $.each(response.categories, function(index, category) {
                        categorySelect.append($('<option>', {
                            value: category.id,
                            text: category.nombre
                        }));
                    });
                }
                
                // 4. Restaurar la selección si todavía existe
                categorySelect.val(currentCategorySelection);
            },
            error: function() {
                productListContainer.html('<div class="alert alert-danger text-center">Ocurrió un error al cargar los productos.</div>');
            }
        });
    }

    // Eventos para disparar la búsqueda
    $('#q_filter').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(fetchProducts, 500);
    });

    $('#categoria_id_filter').on('change', function() {
        fetchProducts();
    });

    // Paginación con AJAX (importante: debe delegar el evento al contenedor estático)
    $('#product-list-container').on('click', '.pagination a', function(event) {
        event.preventDefault(); 
        const url = $(this).attr('href');
        const productListContainer = $('#product-list-container');
        
        productListContainer.html('<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>');

        $.get(url, function(data) {
            productListContainer.html(data.products_html); // Suponiendo que la paginación también devuelve el JSON completo
        }).fail(function() {
            productListContainer.html('<div class="alert alert-danger text-center">Error al cargar la página.</div>');
        });
    });
});
</script>
@endpush
@endsection