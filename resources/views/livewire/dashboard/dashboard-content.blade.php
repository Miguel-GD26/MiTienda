<div>
    <!-- Grid de KPIs (sin cambios) -->
    <div class="row">
        <div class="col-lg-4 mb-4">
            <a href="{{ route('pedidos.index') }}" class="kpi-card-gradient bg-gradient-blue">
                <div class="icon-circle"><i class="fas fa-boxes"></i></div>
                <div><div class="kpi-title">Pedidos Totales</div><div class="kpi-value">{{ $totalPedidos ?? 0 }}</div></div>
            </a>
        </div>
        <div class="col-lg-4 mb-4">
            <a href="{{ route('pedidos.index', ['status' => 'pendiente']) }}" class="kpi-card-gradient bg-gradient-red">
                <div class="icon-circle"><i class="fas fa-hourglass-half"></i></div>
                <div><div class="kpi-title">Pendientes</div><div class="kpi-value">{{ $kpi['pendiente'] ?? 0 }}</div></div>
            </a>
        </div>
        <div class="col-lg-4 mb-4">
            <a href="{{ route('pedidos.index', ['status' => 'atendido']) }}" class="kpi-card-gradient" style="background: linear-gradient(90deg, #64b5f6, #bbdefb);">
                <div class="icon-circle"><i class="fas fa-clipboard-check"></i></div>
                <div><div class="kpi-title">Atendidos</div><div class="kpi-value">{{ $kpi['atendido'] ?? 0 }}</div></div>
            </a>
        </div>
        <div class="col-lg-4 mb-4">
            <a href="{{ route('pedidos.index', ['status' => 'enviado']) }}" class="kpi-card-gradient bg-gradient-green">
                <div class="icon-circle"><i class="fas fa-truck"></i></div>
                <div><div class="kpi-title">Enviados</div><div class="kpi-value">{{ $kpi['enviado'] ?? 0 }}</div></div>
            </a>
        </div>
        <div class="col-lg-4 mb-4">
            <a href="{{ route('pedidos.index', ['status' => 'entregado']) }}" class="kpi-card-gradient bg-gradient-purple">
                <div class="icon-circle"><i class="fas fa-check-double"></i></div>
                <div><div class="kpi-title">Entregados</div><div class="kpi-value">{{ $kpi['entregado'] ?? 0 }}</div></div>
            </a>
        </div>
        <div class="col-lg-4 mb-4">
            <a href="{{ route('pedidos.index', ['status' => 'cancelado']) }}" class="kpi-card-gradient bg-gradient-orange">
                <div class="icon-circle"><i class="fas fa-times-circle"></i></div>
                <div><div class="kpi-title">Cancelados</div><div class="kpi-value">{{ $kpi['cancelado'] ?? 0 }}</div></div>
            </a>
        </div>
    </div>

    <!-- Fila de Contenido Principal -->
    <div class="row">
        <!-- Columna Izquierda: Tabla de Pedidos Recientes y Barra de Progreso (sin cambios)-->
        <div class="col-lg-7 mb-4">
            <div class="content-card">
                <h5 class="card-title-custom">
                    @if($view_type == 'super_admin') Pedidos Recientes @else Tus Pedidos Recientes @endif
                </h5>

                @if(!empty($statusBar))
                <div class="mb-4">
                    <h6 class="text-muted small">Distribución de Estados (Activos)</h6>
                    <div class="progress-segmented mt-2" style="height: 1rem;">
                        <div class="progress-segment" style="width: {{ $statusBar['pendiente'] ?? 0 }}%; background-color: #ffc107;" title="Pendiente: {{ $kpi['pendiente'] ?? 0 }} pedido(s)"></div>
                        <div class="progress-segment" style="width: {{ $statusBar['atendido'] ?? 0 }}%; background-color: #0dcaf0;" title="Atendido: {{ $kpi['atendido'] ?? 0 }} pedido(s)"></div>
                        <div class="progress-segment" style="width: {{ $statusBar['enviado'] ?? 0 }}%; background-color: #212529;" title="Enviado: {{ $kpi['enviado'] ?? 0 }} pedido(s)"></div>
                        <div class="progress-segment" style="width: {{ $statusBar['entregado'] ?? 0 }}%; background-color: #198754;" title="Entregado: {{ $kpi['entregado'] ?? 0 }} pedido(s)"></div>
                    </div>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table activity-table-sleek">
                         <thead>
                            <tr>
                                <th>Pedido ID</th>
                                <th>Cliente</th>
                                @if($view_type == 'super_admin') <th>Empresa</th> @endif
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pedidosRecientes as $pedido)
                            <tr class="clickable-row" onclick="window.location='{{ route('pedidos.show', $pedido) }}'">
                                <td><a href="{{ route('pedidos.show', $pedido) }}" class="fw-bold text-decoration-none text-primary">#{{ $pedido->id }}</a></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($pedido->cliente->user->name ?? 'C') }}&background=eef0f7&color=6e7a91&bold=true" class="client-avatar" />
                                        <span>{{ $pedido->cliente->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                @if($view_type == 'super_admin') <td>{{ $pedido->empresa->nombre ?? 'N/A' }}</td> @endif
                                <td>S/{{ number_format($pedido->total, 2) }}</td>
                                <td><span class="badge rounded-pill @switch($pedido->estado) @case('pendiente') bg-warning @break @case('atendido') bg-info @break @case('enviado') bg-dark @break @case('entregado') bg-success @break @case('cancelado') bg-danger @break @endswitch">{{ Str::ucfirst($pedido->estado) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center p-4">No hay pedidos recientes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Widgets de Acción, Tops y Registros (VERSIÓN MEJORADA) -->
        <div class="col-lg-5 mb-4">
            <div class="content-card">

                {{-- ===== WIDGET DE ACCIÓN UNIFICADO ===== --}}
                <div>
                    <h6 class="text-muted font-weight-bold small text-uppercase text-danger mb-1">
                        <i class="fas fa-exclamation-triangle"></i> Atención Requerida
                    </h6>
                    <p class="small text-muted mb-3">Tareas urgentes que necesitan tu intervención.</p>

                    <div class="top-list">
                        @if($pedidosAntiguos->isEmpty() && $productosBajoStock->isEmpty())
                            <div class="text-center p-3">
                                <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                <p class="text-muted mb-0">¡Excelente! No hay tareas urgentes.</p>
                            </div>
                        @else
                            {{-- Pedidos que llevan mucho tiempo pendientes --}}
                            @foreach($pedidosAntiguos as $pedido)
                            <div class="top-list-item clickable-row" onclick="window.location='{{ route('pedidos.show', $pedido) }}'">
                                <div class="client-avatar d-flex align-items-center justify-content-center bg-light"><i class="fas fa-hourglass-start text-danger"></i></div>
                                <div class="ms-2">
                                    <a href="{{ route('pedidos.show', $pedido) }}" class="text-decoration-none fw-bold">Pedido Antiguo</a>
                                    <div class="small text-muted">#{{$pedido->id}} de {{ Str::limit($pedido->cliente->user->name ?? 'N/A', 15) }}</div>
                                </div>
                                <span class="item-value text-danger small">hace {{ $pedido->created_at->diffForHumans(null, true) }}</span>
                            </div>
                            @endforeach

                            {{-- Productos con pocas unidades en stock --}}
                            @foreach($productosBajoStock as $producto)
                            <div class="top-list-item clickable-row" onclick="window.location='{{ route('productos.index') }}'">
                                <div class="client-avatar d-flex align-items-center justify-content-center" style="background-color: #fff8e1;"><i class="fas fa-box-open" style="color: #ffc107;"></i></div>
                                <div class="ms-2">
                                    <a href="{{ route('productos.index') }}" class="text-decoration-none fw-bold">Bajo Stock</a>
                                    <div class="small text-muted">{{ Str::limit($producto->nombre, 25) }}</div>
                                </div>
                                <span class="item-value"><span class="badge bg-warning rounded-pill text-dark">Quedan {{ $producto->stock }}</span></span>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    <hr class="my-4">
                </div>
                {{-- ===== FIN DEL WIDGET DE ACCIÓN ===== --}}
                
                <!-- El resto de los widgets permanecen igual -->
                <h5 class="card-title-custom mb-3">Resumen de Actividad</h5>
                
                <div>
                <div class="d-flex justify-content-between align-items-center mb-2"><h6 class="text-muted font-weight-bold small text-uppercase">Productos Más Vendidos</h6></div>
                <div class="top-list">
                    @forelse($topProductos as $producto)
                    {{-- Envolvemos todo el item en un enlace que pasa el nombre como parámetro de búsqueda --}}
                    <a href="{{ route('productos.index', ['search' => $producto->nombre]) }}" class="text-decoration-none text-body" title="Buscar '{{ $producto->nombre }}'">
                        <div class="top-list-item">
                            <div class="client-avatar d-flex align-items-center justify-content-center" style="background-color: #e8f5e9;"><i class="fas fa-box-open" style="color: #4caf50;"></i></div>
                            <span class="ms-2">{{ Str::limit($producto->nombre, 25) }}</span>
                            <span class="item-value"><span class="badge bg-success rounded-pill">{{ $producto->total_vendido }}</span></span>
                        </div>
                    </a>
                    @empty
                    <p class="text-muted text-center pt-2">No hay datos de ventas.</p>
                    @endforelse
                </div>
                <hr class="my-4">
            </div>

            {{-- ===== WIDGET DE CATEGORÍAS POPULARES (CON ENLACES) ===== --}}
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2"><h6 class="text-muted font-weight-bold small text-uppercase">Categorías Populares</h6></div>
                <div class="top-list">
                    @forelse($topCategorias as $categoria)
                    {{-- Hacemos lo mismo para las categorías --}}
                    <a href="{{ route('categorias.index', ['search' => $categoria->nombre]) }}" class="text-decoration-none text-body" title="Buscar '{{ $categoria->nombre }}'">
                        <div class="top-list-item">
                            <div class="client-avatar d-flex align-items-center justify-content-center" style="background-color: #f3e5f5;"><i class="fas fa-tags" style="color: #9c27b0;"></i></div>
                            <span class="ms-2">{{ Str::limit($categoria->nombre, 25) }}</span>
                            <span class="item-value"><span class="badge bg-purple rounded-pill" style="background-color: #9c27b0 !important;">{{ $categoria->total_vendido }}</span></span>
                        </div>
                    </a>
                    @empty
                    <p class="text-muted text-center pt-2">No hay datos de ventas.</p>
                    @endforelse
                </div>
                <hr class="my-4">
            </div>


                <h5 class="card-title-custom mb-3">Registros Recientes</h5>

                @if($view_type == 'super_admin')
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2"><h6 class="text-muted font-weight-bold small text-uppercase">Nuevas Empresas</h6><a href="{{ route('empresas.index') }}" class="small">Ver todas</a></div>
                    <div class="top-list">
                        @forelse($ultimasEmpresas as $empresa)
                        <div class="top-list-item">
                            <div class="client-avatar d-flex align-items-center justify-content-center" style="background-color: #e9f5ff;"><i class="fas fa-building" style="color: #1e88e5;"></i></div>
                            <span class="ms-2">{{ Str::limit($empresa->nombre, 25) }}</span>
                            <span class="item-value text-muted small">{{ $empresa->created_at->diffForHumans() }}</span>
                        </div>
                        @empty
                        <p class="text-muted text-center pt-2">No hay nuevas empresas.</p>
                        @endforelse
                    </div>
                    <hr class="my-4">
                </div>
                @endif
                
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2"><h6 class="text-muted font-weight-bold small text-uppercase">@if($view_type == 'super_admin') Nuevos Clientes @else Tus Nuevos Clientes @endif</h6><a href="@if($view_type == 'super_admin') {{ route('usuarios.index') }} @else {{ route('clientes.mitienda') }} @endif" class="small">Ver todos</a></div>
                     <div class="top-list">
                        @forelse($ultimosClientes as $cliente)
                        <div class="top-list-item">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($cliente->name) }}&background=fff0e9&color=ff5722&size=32&bold=true" class="client-avatar" />
                            <span class="ms-2">{{ Str::limit($cliente->name, 25) }}</span>
                            <span class="item-value text-muted small">@if($view_type == 'super_admin') {{ $cliente->created_at->diffForHumans() }} @else {{ $cliente->asociado_hace }} @endif</span>
                        </div>
                        @empty
                        <p class="text-muted text-center pt-2">@if($view_type == 'super_admin') No hay nuevos clientes. @else Aún no tienes clientes. @endif</p>
                        @endforelse
                    </div>
                </div>

                 <hr class="my-4">

                <div>
                     <h5 class="card-title-custom">@if($view_type == 'super_admin') Ingresos (Últimos 15 días) @else Tus Ingresos (Últimos 15 días) @endif</h5>
                    <div id="sparkline-chart" wire:ignore></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div wire:loading.block>
    <div class="top-list">
        {{-- Esqueleto para un item de la lista --}}
        @for ($i = 0; $i < 5; $i++)
        <div class="top-list-item">
            <div class="client-avatar bg-light"></div>
            <div class="ms-2 w-100">
                <div class="bg-light rounded" style="height: 10px; width: 60%;"></div>
                <div class="bg-light rounded mt-2" style="height: 8px; width: 40%;"></div>
            </div>
        </div>
        @endfor
    </div>
</div>