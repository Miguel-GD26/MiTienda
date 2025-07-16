@extends('plantilla.app')
@section('titulo', 'Gestión de Pedidos')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">
        <h2 class="h3 mb-4"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Gestión de Pedidos</h2>

        @if (session('mensaje'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('mensaje') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-ban me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white p-3">
                <form action="{{ route('pedidos.index') }}" method="GET" class="row g-3 align-items-center">
                    @if(auth()->user()->hasRole('super_admin'))
                    <div class="col-md-4">
                        <select name="empresa_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">-- Seleccione una Empresa --</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa->id }}" {{ request('empresa_id') == $empresa->id ? 'selected' : '' }}>{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <select name="estado" class="form-select form-select-sm">
                            <option value="">-- Todos los Estados --</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado }}" {{ request('estado') == $estado ? 'selected' : '' }}>{{ ucfirst($estado) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="cliente_nombre" class="form-control form-control-sm" placeholder="Nombre del cliente..." value="{{ request('cliente_nombre') }}">
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    </div>
                </form>
            </div>

            @if(auth()->user()->hasRole('super_admin') && !request('empresa_id'))
                <div class="card-body text-center p-5">
                    <i class="fa-solid fa-store fa-3x text-primary mb-3"></i>
                    <h4 class="text-muted">Por favor, seleccione una empresa para ver sus pedidos.</h4>
                </div>
            @else
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th>Cliente</th>
                                    @if(auth()->user()->hasRole('super_admin'))
                                        <th>Empresa</th>
                                    @endif
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Estado</th>
                                    <th>Fecha</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pedidos as $pedido)
                                <tr class="align-middle">
                                    <td class="text-center fw-bold">#{{ $pedido->id }}</td>
                                    <td>{{ $pedido->cliente->nombre ?? 'N/A' }}</td>
                                    @if(auth()->user()->hasRole('super_admin'))
                                        <td>{{ $pedido->empresa->nombre ?? 'N/A' }}</td>
                                    @endif
                                    <td class="text-end fw-bold">S/.{{ number_format($pedido->total, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill 
                                            @switch($pedido->estado)
                                                @case('pendiente') bg-warning text-dark @break
                                                @case('atendido') bg-info text-dark @break
                                                @case('enviado') bg-dark @break
                                                @case('entregado') bg-success @break
                                                @case('cancelado') bg-danger @break
                                                @default bg-secondary
                                            @endswitch
                                        ">{{ ucfirst($pedido->estado) }}</span>
                                    </td>
                                    <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-sm btn-outline-info" title="Ver Detalles">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->hasRole('super_admin') ? '7' : '6' }}" class="text-center py-4">
                                        <p class="text-muted mb-0">No se encontraron pedidos con los filtros actuales.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($pedidos->hasPages())
                <div class="card-footer d-flex justify-content-end">
                    {{ $pedidos->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>
</main>
@endsection