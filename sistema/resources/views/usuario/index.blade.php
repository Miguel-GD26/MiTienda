@extends('plantilla.app')
@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">
        <!-- Título -->
        <h2 class="mb-3">Listado de Usuarios</h2>

        <!-- Fila: Buscar a la izquierda | Nuevo Usuario a la derecha -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-6">
                <form action="{{ route('usuarios.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="texto" placeholder="Buscar usuario..."
                            value="{{ request('texto') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-end">
                @can('user-create')
                <a href="{{ route('usuarios.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Nuevo Usuario
                </a>
                @endcan
            </div>
        </div>

        <!-- Mensaje de éxito -->
        @if (session('mensaje'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('mensaje') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Tabla -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                
                                <th style="width: 60px;">ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th class="text-center" style="width: 150px;">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($registros as $reg)
                            <tr class="align-middle">
                                
                                <td>{{ $reg->id }}</td>
                                <td>{{ $reg->name }}</td>
                                <td>{{ $reg->email }}</td>
                                <td>
                                    @if ($reg->roles->isNotEmpty())
                                        <span class="badge bg-primary">
                                            {!! $reg->roles->pluck('name')->implode('</span> <span class="badge bg-primary ">') !!}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Sin rol</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $reg->activo ? 'bg-success' : 'bg-danger' }}">
                                        {{ $reg->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        @can('user-edit')
                                        <a href="{{ route('usuarios.edit', $reg->id) }}" class="btn btn-info btn-sm">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        @endcan
                                        @can('user-delete')
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#modal-eliminar-{{$reg->id}}">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                        @endcan
                                        @can('user-activate')
                                        <button class="btn btn-sm {{ $reg->activo ? 'btn-warning' : 'btn-success' }}" data-bs-toggle="modal"
                                            data-bs-target="#modal-toggle-{{$reg->id}}">
                                            <i class="bi {{ $reg->activo ? 'bi-ban' : 'bi-check-circle' }}"></i>
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
                                <td colspan="6" class="text-center text-muted">No se encontraron usuarios.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end">
                {{ $registros->appends(['texto' => request('texto')])->links() }}
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    document.getElementById('mnuSeguridad').classList.add('menu-open');
    document.getElementById('itemUsuario').classList.add('active');
</script>
@endpush
