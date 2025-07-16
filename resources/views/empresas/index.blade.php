@extends('plantilla.app')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">

        <!-- Cabecera con Título -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0">Listado de Empresas</h2>
        </div>

        <!-- Mensajes de sesión -->
        @if (session('mensaje'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('mensaje') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Tarjeta de Contenido: Búsqueda y Tabla -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <form action="{{ route('empresas.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="texto" placeholder="Buscar por nombre..."
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
                                <th>Slug (URL)</th>
                                <th>Fecha de Creación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($empresas as $empresa)
                            <tr class="align-middle">
                                <td class="text-center">{{ $empresa->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($empresa->nombre) }}&background=random&color=fff&size=32" alt="Logo" class="rounded-circle me-2">
                                        <span>{{ $empresa->nombre }}</span>
                                    </div>
                                </td>
                                <td>/tienda/{{ $empresa->slug }}</td>
                                <td>{{ $empresa->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                {{-- El colspan se ajusta a 4 columnas --}}
                                <td colspan="4">
                                    <div class="text-center p-5">
                                        <i class="fa-solid fa-shop-slash fa-3x text-muted mb-3"></i>
                                        <p class="mb-0 text-muted">No se encontraron empresas que coincidan con la búsqueda.</p>
                                    </div>
                                    </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($empresas->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $empresas->appends(['texto' => request('texto')])->links() }}
            </div>
            @endif
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    // Aquí puedes añadir scripts específicos para esta página si los necesitas en el futuro.
    // Por ejemplo, para activar un item del menú lateral:
    // document.getElementById('itemEmpresa').classList.add('active');
</script>
@endpush