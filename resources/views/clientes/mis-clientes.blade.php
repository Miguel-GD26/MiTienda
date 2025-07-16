@extends('plantilla.app')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">

        <!-- Cabecera con Título -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0">Mis Clientes en {{ $empresa->nombre }}</h2>
        </div>

        <!-- Mensajes de sesión -->
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
                {{-- La acción de la ruta debe apuntar a la misma página --}}
                <form action="{{ route('clientes.mitienda') }}" method="GET">
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
                                <th>Registrado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clientes as $cliente)
                            <tr class="align-middle">
                                <td class="text-center">{{ $cliente->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($cliente->name) }}&background=random&color=fff&size=32" alt="Avatar" class="rounded-circle me-2">
                                        <span>{{ $cliente->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $cliente->email }}</td>
                                <td>{{ $cliente->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">
                                    <div class="text-center p-5">
                                        <i class="fa-solid fa-users-slash fa-3x text-muted mb-3"></i>
                                        <p class="mb-0 text-muted">No se encontraron clientes para tu tienda.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($clientes->hasPages())
            <div class="card-footer bg-white border-0">
                {{-- Asegúrate de que la paginación mantenga el término de búsqueda --}}
                {{ $clientes->appends(['texto' => request('texto')])->links() }}
            </div>
            @endif
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    // Aquí puedes activar el item del menú lateral si es necesario
    // document.getElementById('itemMiTienda').classList.add('active');
</script>
@endpush