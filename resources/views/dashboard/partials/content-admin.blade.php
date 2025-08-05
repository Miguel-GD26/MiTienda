<!-- Grid de KPIs -->
<div class="row">
    {{-- Fila 1 de 3 KPIs --}}
    <div class="col-lg-4 mb-4">
        <a href="{{ route('pedidos.index') }}" class="kpi-card-gradient bg-gradient-blue">
            <div class="icon-circle"><i class="fas fa-boxes"></i></div>
            <div><div class="kpi-title">Pedidos Totales</div><div class="kpi-value">{{ $totalPedidos ?? 0 }}</div></div>
        </a>
    </div>
    <div class="col-lg-4 mb-4">
        <a href="{{ route('pedidos.index') }}" class="kpi-card-gradient bg-gradient-red">
            <div class="icon-circle"><i class="fas fa-hourglass-half"></i></div>
            <div><div class="kpi-title">Pendientes</div><div class="kpi-value">{{ $kpi['pendiente'] ?? 0 }}</div></div>
        </a>
    </div>
    <div class="col-lg-4 mb-4">
        <a href="{{ route('pedidos.index') }}" class="kpi-card-gradient" style="background: linear-gradient(90deg, #64b5f6, #bbdefb);">
            <div class="icon-circle"><i class="fas fa-clipboard-check"></i></div>
            <div><div class="kpi-title">Atendidos</div><div class="kpi-value">{{ $kpi['atendido'] ?? 0 }}</div></div>
        </a>
    </div>

    {{-- Fila 2 de 3 KPIs --}}
    <div class="col-lg-4 mb-4">
        <a href="{{ route('pedidos.index') }}" class="kpi-card-gradient bg-gradient-green">
            <div class="icon-circle"><i class="fas fa-truck"></i></div>
            <div><div class="kpi-title">Enviados</div><div class="kpi-value">{{ $kpi['enviado'] ?? 0 }}</div></div>
        </a>
    </div>
    <div class="col-lg-4 mb-4">
        <a href="{{ route('pedidos.index') }}" class="kpi-card-gradient bg-gradient-purple">
            <div class="icon-circle"><i class="fas fa-check-double"></i></div>
            <div><div class="kpi-title">Entregados</div><div class="kpi-value">{{ $kpi['entregado'] ?? 0 }}</div></div>
        </a>
    </div>
    <div class="col-lg-4 mb-4">
        <a href="{{ route('pedidos.index') }}" class="kpi-card-gradient bg-gradient-orange">
            <div class="icon-circle"><i class="fas fa-times-circle"></i></div>
            <div><div class="kpi-title">Cancelados</div><div class="kpi-value">{{ $kpi['cancelado'] ?? 0 }}</div></div>
        </a>
    </div>
</div>


<!-- Fila de Contenido Principal -->
<div class="row">
    <!-- Columna Izquierda: Tabla de Pedidos Recientes -->
    <div class="col-lg-7 mb-4">
        <div class="content-card">
            <h5 class="card-title-custom">
                @if(auth()->user()->hasRole('super_admin')) Pedidos Recientes @else Tus Pedidos Recientes @endif
            </h5>
            <div class="table-responsive">
                <table class="table activity-table-sleek">
                     <thead>
                        <tr>
                            <th>Pedido ID</th>
                            <th>Cliente</th>
                            @if(auth()->user()->hasRole('super_admin'))
                                <th>Empresa</th>
                            @endif
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pedidosRecientes as $pedido)
                        <tr>
                            <td><strong>#{{ $pedido->id }}</strong></td>
                            <td><div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($pedido->cliente->user->name ?? 'C') }}&background=eef0f7&color=6e7a91&bold=true" class="client-avatar"/>
                                <span>{{ $pedido->cliente->user->name ?? 'N/A' }}</span>
                            </div></td>
                            @if(auth()->user()->hasRole('super_admin'))
                                <td>{{ $pedido->empresa->nombre ?? 'N/A' }}</td>
                            @endif
                            <td>S/{{ number_format($pedido->total, 2) }}</td>
                            <td><span class="badge bg-primary rounded-pill
                            @switch($pedido->estado)
                                @case('pendiente') bg-warning  @break
                                @case('atendido') bg-info  @break
                                @case('enviado') bg-dark @break
                                @case('entregado') bg-success @break
                                @case('cancelado') bg-danger @break
                            @endswitch
                            ">{{ Str::ucfirst($pedido->estado) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center p-4">No hay pedidos recientes.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Widgets de Registros o Gráficos -->
    <div class="col-lg-5 mb-4">
        <div class="content-card">
             <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title-custom mb-0">Registros Recientes</h5>
            </div>
            <hr class="my-3">

            {{-- Widget de Nuevas Empresas (SOLO para Super Admin) --}}
            @if(auth()->user()->hasRole('super_admin'))
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted font-weight-bold small text-uppercase">Nuevas Empresas</h6>
                    <a href="{{ route('empresas.index') }}" class="small">Ver todas</a>
                </div>
                <div class="top-list">
                    @forelse($ultimasEmpresas as $empresa)
                    <div class="top-list-item">
                        <div class="client-avatar d-flex align-items-center justify-content-center" style="background-color: #e9f5ff;">
                            <i class="fas fa-building" style="color: #1e88e5;"></i>
                        </div>
                        <span class="ms-2">{{ $empresa->nombre }}</span>
                        <span class="item-value text-muted small">{{ $empresa->created_at->diffForHumans() }}</span>
                    </div>
                    @empty
                    <p class="text-muted text-center pt-2">No hay nuevas empresas.</p>
                    @endforelse
                </div>
                <hr class="my-4">
            </div>
            @endif

            {{-- Widget de Nuevos Clientes (Para AMBOS roles) --}}
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted font-weight-bold small text-uppercase">
                        @if(auth()->user()->hasRole('super_admin')) Nuevos Clientes @else Tus Nuevos Clientes @endif
                    </h6>
                    {{-- El enlace a usuarios.index es correcto ya que no hay una vista específica de 'clientes' --}}
                    <a href="@if(auth()->user()->hasRole('super_admin')) {{ route('usuarios.index') }} @else {{ route('clientes.mitienda') }} @endif" class="small">
                        Ver todos
                    </a>
                </div>
                 <div class="top-list">
                    @forelse($ultimosClientes as $cliente)
                    <div class="top-list-item">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($cliente->name) }}&background=fff0e9&color=ff5722&size=32&bold=true" class="client-avatar"/>
                        <span class="ms-2">{{ $cliente->name }}</span>
                        {{-- === CAMBIO LÓGICO AQUÍ === --}}
                        <span class="item-value text-muted small">
                            @if(auth()->user()->hasRole('super_admin'))
                                {{ $cliente->created_at->diffForHumans() }}
                            @else
                                {{-- La propiedad 'asociado_hace' la añadimos en el controlador --}}
                                {{ $cliente->asociado_hace }}
                            @endif
                        </span>
                    </div>
                    @empty
                    <p class="text-muted text-center pt-2">
                        @if(auth()->user()->hasRole('super_admin')) No hay nuevos clientes. @else Aún no tienes clientes. @endif
                    </p>
                    @endforelse
                </div>
            </div>

             <hr class="my-4">

            <div>
                 <h5 class="card-title-custom">
                     @if(auth()->user()->hasRole('super_admin')) Ingresos (Últimos 15 días) @else Tus Ingresos (Últimos 15 días) @endif
                </h5>
                <div id="sparkline-chart"></div>
            </div>
        </div>
    </div>
</div>