@extends('plantilla.app')
@section('contenido')
    <main class="app-main">
        <div class="container-fluid mt-4">
            <!-- Título -->
            <h2 class="mb-3">Listado de Categorías</h2>

            <!-- Fila: Buscar a la izquierda | Nueva Categoría a la derecha -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-6">
                    <form action="{{ route('categorias.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="texto" placeholder="Buscar categoría..."
                                value="{{ request('texto') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('categorias.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-lg"></i> Nueva Categoría
                    </a>
                </div>
            </div>

            <!-- Mensaje de éxito -->
            @if (session('mensaje'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
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
                                    <th style="width: 40px;">ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th class="text-center" style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($registros as $reg)
                                    <tr>
                                        <td>{{ $reg->id }}</td>
                                        <td>{{ $reg->nombre }}</td>
                                        <td>{{ $reg->descripcion }}</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('categorias.edit', $reg->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#modal-delete-{{$reg->id}}">
                                                    <i class="bi bi-trash-fill text-white"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @include('categoria.delete')
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No se encontraron categorías.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    {{ $registros->links() }}
                </div>
            </div>
        </div>
    </main>
@endsection