@extends('plantilla.app')

@section('titulo', 'Listado de Productos')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">

        <!-- Cabecera con Título y Botón de Nuevo -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">
                <i class="fa-solid fa-box-open me-2"></i>
                Listado de Productos
            </h2>
            @can('producto-create')
                <a href="{{ route('productos.create') }}" class="btn btn-success shadow-sm">
                    <i class="fa-solid fa-plus me-1"></i> Nuevo Producto
                </a>
            @endcan
        </div>

        <!-- Mensajes de sesión -->
        @if (session('mensaje'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('mensaje') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if (session('error') || session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            {{ session('error') ?? session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <!-- Tarjeta de Contenido: Filtros y Tabla -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <form action="{{ route('productos.index') }}" method="GET" class="d-flex flex-column flex-md-row gap-2">
                    @if(auth()->user()->hasRole('super_admin'))
                        <div class="flex-grow-1">
                            <select name="empresa_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Filtrar por Empresa --</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}" {{ request('empresa_id') == $empresa->id ? 'selected' : '' }}>
                                        {{ $empresa->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="input-group">
                        <input type="text" class="form-control" name="texto" placeholder="Buscar por nombre de producto..." value="{{ request('texto') }}">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th style="width: 70px;">Imagen</th>
                                <th>Nombre</th>
                                @if(auth()->user()->hasRole('super_admin'))
                                    <th>Empresa</th>
                                @endif
                                <th>Categoría</th>
                                <th class="text-end">Precio</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($productos as $producto)
                            <tr class="align-middle">
                                <td class="text-center">{{ $producto->id }}</td>
                                <td>
                                    @if ($producto->imagen_url)
                                        <img src="{{cloudinary()->image($producto->imagen_url)->toUrl()}}" alt="{{ $producto->nombre }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="icon-circle bg-secondary-subtle text-secondary">
                                            <i class="fa-solid fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $producto->nombre }}</div>
                                    <small class="text-muted d-block d-md-none">{{ $producto->categoria->nombre ?? 'N/A' }}</small>
                                </td>
                                @if(auth()->user()->hasRole('super_admin'))
                                    <td class="small text-muted">{{ $producto->empresa->nombre ?? 'N/A' }}</td>
                                @endif
                                <td class="d-none d-md-table-cell">{{ $producto->categoria->nombre ?? 'N/A' }}</td>
                                <td class="text-end fw-bold text-success">S/.{{ number_format($producto->precio, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge {{ ($producto->stock ?? 0) > 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }} rounded-pill">
                                        {{ $producto->stock ?? 'Ilimitado' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('producto-edit')
                                        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Editar">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                        @endcan
                                        @can('producto-delete')
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modal-delete-{{ $producto->id }}" data-bs-toggle="tooltip" title="Eliminar">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @can('producto-delete')
                                @include('producto.delete', ['producto' => $producto])
                            @endcan
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('super_admin') ? '8' : '7' }}">
                                    <div class="text-center p-5">
                                        <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                                        <p class="mb-0 text-muted">No se encontraron productos.</p>
                                        <small>Intenta con otros filtros o <a href="{{ route('productos.create') }}">crea un nuevo producto</a>.</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($productos->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $productos->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</main>
@endsection

@push('estilos')
{{-- Estilo para el círculo del icono, si un producto no tiene imagen --}}
<style>
    .icon-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border-radius: 50%;
    }
</style>
@endpush

@push('scripts')
<script>
    // Inicializar los tooltips de Bootstrap para los botones de acción
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>
@endpush