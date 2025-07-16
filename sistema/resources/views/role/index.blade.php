@extends('plantilla.app')
@section('contenido')
    <main class="app-main">
        <div class="container-fluid mt-4">
            <!-- Título -->
            <h2 class="mb-3">Listado de Roles</h2>

            <!-- Fila: Buscar a la izquierda | Nuevo Rol a la derecha -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-6">
                    <form action="{{ route('roles.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="texto" placeholder="Buscar rol..."
                                value="{{ request('texto') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    @can('rol-create')
                        <a href="{{ route('roles.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-lg"></i> Nuevo Rol
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Mensaje de éxito -->
            @if (Session::has('mensaje'))
                <div class="alert alert-info alert-dismissible fade show">
                    {{ Session::get('mensaje') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Tabla de roles -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive mt-2">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 20px">ID</th>
                                    <th>Nombre</th>
                                    <th>Permisos</th>
                                    <th style="width: 150px" class="text-center">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($registros) <= 0)
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No hay registros que coincidan con la
                                            búsqueda</td>
                                    </tr>
                                @else
                                    @foreach($registros as $reg)
                                        <tr class="align-middle">
                                            <td>{{ $reg->id }}</td>
                                            <td>{{ $reg->name }}</td>
                                            <td>
                                                @if($reg->permissions->isNotEmpty())
                                                                            {!! $reg->permissions->pluck('name')->map(function ($name) {
                                                        return "<span class='badge bg-primary me-1'>$name</span>";
                                                    })->implode(' ') !!}
                                                @else
                                                    <span class="badge bg-secondary">Sin permisos</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    @can('rol-edit')
                                                        <a href="{{ route('roles.edit', $reg->id) }}" class="btn btn-info btn-sm">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                    @endcan
                                                    @can('rol-delete')
                                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#modal-eliminar-{{ $reg->id }}">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    @endcan
                                                </div>

                                            </td>
                                        </tr>
                                        @can('rol-delete')
                                            @include('role.delete')
                                        @endcan
                                    @endforeach
                                @endif
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
        document.getElementById('itemRole').classList.add('active');
    </script>
@endpush