@extends('plantilla.app')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">

        <!-- Cabecera con Título y Botón de Nuevo -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0">Listado de Usuarios</h2>
            @can('user-create')
                <a href="{{ route('usuarios.create') }}" class="btn btn-success shadow-sm">
                    <i class="fa-solid fa-user-plus me-1"></i> Nuevo Usuario
                </a>
            @endcan
        </div>

        <!-- Mensaje de éxito -->
        @if (session('mensaje'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('mensaje') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        <!-- Tarjeta de Contenido: Búsqueda y Tabla -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <form action="{{ route('usuarios.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="texto" placeholder="Buscar por nombre o email..."
                            value="{{ request('texto') }}">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Buscar
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
                                <th>Email</th>
                                @if(auth()->user()->hasRole('super_admin'))
                                    <th>Empresa</th>
                                @endif
                                <th>Rol</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center" style="width: 180px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($registros as $reg)
                            <tr class="align-middle">
                                <td class="text-center">{{ $reg->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($reg->name) }}&background=random&color=fff" alt="" class="rounded-circle me-2" width="32" height="32">
                                        <span>{{ $reg->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $reg->email }}</td>
                                @if(auth()->user()->hasRole('super_admin'))
                                    <td>
                                        <span class="badge bg-info-subtle text-info-emphasis fw-normal">{{ $reg->empresa->nombre ?? 'Sin empresa' }}</span>
                                    </td>
                                @endif
                                <td>
                                    @forelse ($reg->roles as $role)
                                        <span class="badge rounded-pill bg-primary fw-normal">{{ $role->name }}</span>
                                    @empty
                                        <span class="badge rounded-pill bg-secondary fw-normal">Sin rol</span>
                                    @endforelse
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $reg->activo ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger' }}">
                                        {{ $reg->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('user-edit')
                                        <a href="{{ route('usuarios.edit', $reg->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Editar">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                        @endcan
                                        @can('user-activate')
                                        <button class="btn btn-sm {{ $reg->activo ? 'btn-outline-warning' : 'btn-outline-success' }}" data-bs-toggle="modal"
                                            data-bs-target="#modal-toggle-{{$reg->id}}" data-bs-toggle="tooltip" title="{{ $reg->activo ? 'Desactivar' : 'Activar' }}">
                                            <i class="fa-solid {{ $reg->activo ? 'fa-ban' : 'fa-circle-check' }}"></i>
                                        </button>
                                        @endcan
                                        @can('user-delete')
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#modal-eliminar-{{$reg->id}}" data-bs-toggle="tooltip" title="Eliminar">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @can('user-delete')
                                @include('usuario.delete')
                            @endcan
                            @can('user-activate')
                                @include('usuario.activate')
                            @endcan
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('super_admin') ? '7' : '6' }}">
                                    <div class="text-center p-5">
                                        <i class="fa-solid fa-users-slash fa-3x text-muted mb-3"></i>
                                        <p class="mb-0 text-muted">No se encontraron usuarios que coincidan con la búsqueda.</p>
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
                {{ $registros->appends(['texto' => request('texto')])->links() }}
            </div>
            @endif
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    // Activar item del sidebar
    document.getElementById('mnuSeguridad').classList.add('menu-open');
    document.getElementById('itemUsuario').classList.add('active');

    // Inicializar todos los tooltips de Bootstrap en la página
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush