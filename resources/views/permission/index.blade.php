@extends('plantilla.app')

@section('titulo', 'Gestión de Permisos')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">
        
        {{-- Cabecera con título, búsqueda y botón --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">
                <i class="fa-solid fa-key me-2"></i>
                Gestión de Permisos
            </h2>
            @can('permission-create')
                <a href="{{ route('permisos.create') }}" class="btn btn-success shadow-sm">
                    <i class="fa-solid fa-plus me-1"></i> Nuevo Permiso
                </a>
            @endcan
        </div>

        {{-- Mensajes de sesión y búsqueda --}}
        @if (session('mensaje'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('mensaje') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card shadow-sm mb-4">
            <div class="card-body p-3">
                <form action="{{ route('permisos.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="texto" placeholder="Buscar por nombre..." value="{{ $texto ?? '' }}">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Contenedor de las tarjetas de permisos --}}
        <div class="row">
            @forelse($registros as $permiso)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm text-center card-permission">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <div class="icon-circle bg-primary-subtle text-primary mx-auto mb-3">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <h5 class="card-title">{{ $permiso->action_name }}</h5>
                                <p class="card-text text-muted">
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">{{ $permiso->group_name }}</span>
                                </p>
                                <small class="text-body-secondary d-block mt-2"><code style ="color: blue;">{{ $permiso->name }}</code></small>
                            </div>
                            <div class="mt-4">
                                @canany(['permission-edit', 'permission-delete'])
                                <div class="btn-group">
                                    @can('permission-edit')
                                        <a href="{{ route('permisos.edit', $permiso) }}" class="btn btn-sm btn-outline-info" title="Editar">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('permission-delete')
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#modal-delete-permission-{{$permiso->id}}" title="Eliminar">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        {{-- Aquí debes incluir tu modal de confirmación si lo tienes --}}
                                    @endcan
                                </div>
                                @endcanany
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center p-5 bg-white rounded shadow-sm">
                        <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="mb-0 text-muted">No se encontraron permisos.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        @if ($registros->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $registros->appends(['texto' => $texto ?? ''])->links() }}
        </div>
        @endif
    </div>
</main>
@endsection

@push('estilos')
<style>
    .card-permission {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card-permission:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
    }
    .icon-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-size: 1.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('mnuSeguridad').classList.add('menu-open');
    document.getElementById('itemPermiso').classList.add('active');
</script>
@endpush