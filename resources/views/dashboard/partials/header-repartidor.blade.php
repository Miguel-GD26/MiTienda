<div class="dashboard-header">
    <div class="d-flex align-items-center">
        <div class="logo-icon" style="color: var(--kpi-green);"><i class="fas fa-truck-fast"></i></div>
        <div class="ms-3">
            <h2 class="header-subtitle mb-0">Hola, {{ auth()->user()->name }}</h2>
            <h1 class="header-title">Panel de Entregas</h1>
        </div>
    </div>
    <div class="d-none d-lg-flex">
        <div class="header-metric"><div class="icon" style="background-color: var(--kpi-orange);"><i class="fas fa-box"></i></div><div><div class="value">{{ $kpi['enviado'] ?? 0 }}</div><div class="label">Por Entregar</div></div></div>
        <div class="header-metric"><div class="icon" style="background-color: var(--kpi-green);"><i class="fas fa-check-double"></i></div><div><div class="value">{{ $pedidosEntregadosHoy ?? 0 }}</div><div class="label">Entregados Hoy</div></div></div>
    </div>
</div>