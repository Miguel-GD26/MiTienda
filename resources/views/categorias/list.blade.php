@extends('welcome.app')
@section('contenido')
    <div class="container-contenido">
        <!-- Título -->
        <h2 class="mb-3">Listado de Categorías</h2>
        <!-- Fila: Buscar -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-6">
                <form action="{{ route('categorias.list') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="texto" placeholder="Buscar categoría..."
                            value="{{ request('texto') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>

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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($registros as $reg)
                                <tr>
                                    <td>{{ $reg->id }}</td>
                                    <td>{{ $reg->nombre }}</td>
                                    <td>{{ $reg->descripcion }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No se encontraron categorías.</td>
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
@endsection
