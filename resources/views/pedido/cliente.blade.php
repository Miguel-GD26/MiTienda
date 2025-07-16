@extends('welcome.app')
@section('title', 'Mis Compras')

@push('estilos')
<style>
    .text{color:white}
    .status-badge { font-size: 1rem; padding: 0.5em 0.9em; font-weight: 600; }
    .pedido-card {
        transition: box-shadow 0.2s ease-in-out;
    }
    .pedido-card:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endpush

@section('contenido')
<div class="container-contenido py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Mi Historial de Compras</h1>
    </div>

    @if($pedidos->isEmpty())
        <div class="text-center p-5 bg-light rounded shadow-sm">
            <i class="fa-solid fa-receipt fa-3x text-muted mb-3"></i>
            <h3>Aún no tienes pedidos.</h3>
            <p class="text-muted">Cuando realices tu primera compra, aparecerá aquí.</p>
            <a href="/" class="btn btn-primary mt-2">Explorar Tiendas</a>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                @foreach($pedidos as $pedido)
                <div class="card shadow-sm mb-3 pedido-card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold">Pedido #{{ $pedido->id }}</span>
                            <span class="mx-2 text-muted">|</span>
                            <small class="text-muted">Realizado el: {{ $pedido->created_at->format('d/m/Y') }}</small>
                        </div>
                        <span class="badge rounded-pill status-badge
                            @switch($pedido->estado)
                                @case('pendiente') bg-warning   @break
                                @case('atendido') bg-info text-dark @break
                                @case('enviado') bg-primary @break
                                @case('entregado') bg-success @break
                                @case('cancelado') bg-danger @break
                            @endswitch
                        " style="color:white">{{ ucfirst($pedido->estado) }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="card-title">Tienda: {{ $pedido->empresa->nombre }}</h5>
                                <p class="card-text mb-1">Total: <strong class="text-success fs-5">S/.{{ number_format($pedido->total, 2) }}</strong></p>
                                <p class="card-text"><small>Items: {{ $pedido->detalles->sum('cantidad') }}</small></p>
                            </div>
                            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                {{-- El botón de detalles debe ir a pedidos.show --}}
                                <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-outline-primary">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Paginación --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $pedidos->links() }}
        </div>
    @endif
</div>
@endsection