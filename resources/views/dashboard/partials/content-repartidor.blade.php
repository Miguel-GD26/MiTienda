<!-- Grid de KPIs para Repartidor -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="kpi-card-gradient bg-gradient-orange">
            <div class="icon-circle"><i class="fas fa-box-open"></i></div>
            <div><div class="kpi-title">Pedidos por Entregar</div><div class="kpi-value">{{ $kpi['enviado'] ?? 0 }}</div></div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="kpi-card-gradient bg-gradient-green">
            <div class="icon-circle"><i class="fas fa-calendar-check"></i></div>
            <div><div class="kpi-title">Pedidos Entregados Hoy</div><div class="kpi-value">{{ $pedidosEntregadosHoy ?? 0 }}</div></div>
        </div>
    </div>
</div>

<!-- Tabla de Pedidos a Entregar -->
<div class="row">
    <div class="col-12">
        <div class="content-card">
            <h4 class="card-title-custom"><i class="fas fa-boxes-stacked me-2"></i>Ruta de Entrega</h4>
            @if(isset($pedidosParaEntregar) && $pedidosParaEntregar->isNotEmpty())
                <div class="table-responsive">
                    <table class="activity-table-sleek">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Dirección</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pedidosParaEntregar as $pedido)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($pedido->cliente->user->name ?? 'N/A') }}&background=random&color=fff" alt="" class="client-avatar">
                                        {{ $pedido->cliente->user->name ?? 'Cliente Anónimo' }}
                                    </div>
                                </td>
                                <td>{{ $pedido->direccion_envio ?? 'No especificada' }}</td>
                                <td>S/{{ number_format($pedido->total, 2) }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-success">Entregado</a>
                                    <a href="#" class="btn btn-sm btn-info ms-1"><i class="fas fa-map-marker-alt"></i> Ver Mapa</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted p-4">
                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                    <p class="h5">¡Felicidades! No tienes entregas pendientes.</p>
                </div>
            @endif
        </div>
    </div>
</div>