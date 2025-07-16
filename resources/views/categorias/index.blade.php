@extends('plantilla.app')

@section('titulo', 'Gestión de Categorías')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">

        <!-- Cabecera con Título y Botón de Nuevo -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">
                <i class="fa-solid fa-tags me-2"></i>
                Listado de Categorías
            </h2>
            @can('categoria-create')
                <a href="{{ route('categorias.create') }}" class="btn btn-success shadow-sm">
                    <i class="fa-solid fa-plus me-1"></i> Nueva Categoría
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
        @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <!-- Tarjeta de Contenido: Búsqueda y Tabla -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <form action="{{ route('categorias.index') }}" method="GET" class="d-flex flex-column flex-md-row gap-2">
                    <div class="input-group">
                        <input type="text" class="form-control" name="texto" placeholder="Buscar por nombre..."
                            value="{{ request('texto') }}">
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
                                <th class="text-center" style="width: 60px;">ID</th>
                                <th>Nombre</th>
                                @if(auth()->user()->hasRole('super_admin'))
                                    <th>Empresa</th>
                                @endif
                                <th>Descripción</th>
                                <th class="text-center" style="width: 150px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($registros as $reg)
                            <tr class="align-middle">
                                <td class="text-center">{{ $reg->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle bg-primary-subtle text-primary me-3">
                                            <i class="fa-solid fa-tag"></i>
                                        </div>
                                        <span>{{ $reg->nombre }}</span>
                                    </div>
                                </td>
                                @if(auth()->user()->hasRole('super_admin'))
                                    <td class="small text-muted">{{ $reg->empresa->nombre ?? 'N/A' }}</td>
                                @endif
                                <td class="text-muted">{{ $reg->descripcion ?? 'Sin descripción' }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('categoria-edit', $reg)
                                        <a href="{{ route('categorias.edit', $reg) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Editar">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                        @endcan
                                        @can('categoria-delete', $reg)
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#modal-delete-{{$reg->id}}" data-bs-toggle="tooltip" title="Eliminar">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @can('categoria-delete', $reg)
                                @include('categorias.delete', ['registro' => $reg])
                            @endcan
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('super_admin') ? '5' : '4' }}">
                                    <div class="text-center p-5">
                                        <i class="fa-solid fa-folder-open fa-3x text-muted mb-3"></i>
                                        <p class="mb-0 text-muted">No se encontraron categorías.</p>
                                        <small>Intenta con otros filtros o <a href="{{ route('categorias.create') }}">crea una nueva categoría</a>.</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($registros->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $registros->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</main>
@endsection

@push('estilos')
{{-- Estilos para el círculo del icono --}}
<style>
    .icon-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
    }
</style>
@endpush

@push('scripts')
<script>
    // Activar item del sidebar
    const menuGestion = document.getElementById('mnuGestion'); // Ajusta el ID si es diferente
    if (menuGestion) menuGestion.classList.add('menu-open');
    
    const itemCategoria = document.getElementById('itemCategoria'); // Ajusta el ID si es diferente
    if (itemCategoria) itemCategoria.classList.add('active');

    // Inicializar todos los tooltips de Bootstrap en la página
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>
@endpush