@extends('plantilla.app')
@section('titulo', 'Gestión de Roles')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">
                <i class="fa-solid fa-user-shield me-2"></i>
                Gestión de Roles y Permisos
            </h2>
            @can('rol-create')
                <a href="{{ route('roles.create') }}" class="btn btn-success shadow-sm">
                    <i class="fa-solid fa-plus me-1"></i> Nuevo Rol
                </a>
            @endcan
        </div>

        {{-- Mensaje de éxito --}}
        @if (Session::has('mensaje'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                {{ Session::get('mensaje') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        {{-- Barra de búsqueda --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body p-3">
                <form action="{{ route('roles.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="texto" placeholder="Buscar por nombre del rol..." value="{{ request('texto') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Contenedor de las tarjetas de roles --}}
        <div class="row">
            @forelse($registros as $reg)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm border-start-primary">
                        <div class="card-body d-flex flex-column">
                            {{-- Cabecera de la tarjeta --}}
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title text-primary">{{ $reg->name }} </h5>
                                    <br>
                                    <small class="text-muted"> ID: {{ $reg->id }} | {{ $reg->permissions->count() }} permisos</small>
                                </div>
                                <div class="btn-group">
                                    @can('rol-edit')
                                        <a href="{{ route('roles.edit', $reg->id) }}" class="btn btn-sm btn-outline-info" title="Editar">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('rol-delete')
                                        @if(($reg->name != 'super_admin' || auth()->user()->hasRole('super_admin')) )
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#modal-eliminar-{{ $reg->id }}" title="Eliminar">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                            <hr>

                            @if($reg->permissions->isNotEmpty())
                                @php
                                    $groupedPermissions = $reg->permissions->groupBy(function($item) {
                                        return explode('-', $item->name)[0];
                                    });
                                @endphp
                                
                                @foreach($groupedPermissions as $group => $permissions)
                                <div class="mb-2">
                                    <strong class="text-muted">{{ $group }}:</strong>
                                    <div class="d-flex flex-wrap mt-1">
                                        @foreach($permissions as $permission)
                                            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill me-1 mb-1">
                                                @php
                                                    $parts = explode('-', $permission->name, 2);
                                                    $displayName = isset($parts[1]) ? $parts[1] : $parts[0];
                                                @endphp
                                                {{ $displayName }}
                                                
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted p-3">
                                    <i class="fa-solid fa-key-skeleton d-block mb-2 fs-4"></i>
                                    <span>Sin permisos asignados</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                {{-- Se incluye el modal de eliminación para cada rol --}}
                @can('rol-delete')
                    @include('role.delete')
                @endcan
            @empty
                <div class="col-12">
                    <div class="text-center p-5 bg-white rounded shadow-sm">
                        <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="mb-0 text-muted">No se encontraron roles que coincidan con la búsqueda.</p>
                    </div>
                </div>
            @endforelse
        </div>
        
        {{-- Paginación --}}
        @if ($registros->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $registros->appends(['texto' => request('texto')])->links() }}
        </div>
        @endif
    </div>
</main>
@endsection

@push('estilos')
<style>
    .card.border-start-primary {
        border-left: 4px solid var(--bs-primary);
        transition: all 0.2s ease-in-out;
    }
    .card.border-start-primary:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('mnuSeguridad').classList.add('menu-open');
    document.getElementById('itemRole').classList.add('active');
</script>
@endpush