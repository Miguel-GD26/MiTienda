<div class="dashboard-header">
    <div class="d-flex align-items-center">
        <div class="logo-icon"><i class="fas fa-store"></i></div>
        <div class="ms-3">
            <h2 class="header-subtitle mb-0">Bienvenido, {{ auth()->user()->name }}</h2>
            <h1 class="header-title">Panel de {{ auth()->user()->empresa->nombre ?? 'Mi Empresa' }}</h1>
        </div>
    </div>
    <div class="d-none d-lg-flex">
        <div class="header-metric"><div class="icon" style="background-color: var(--kpi-blue);"><i class="fas fa-coins"></i></div><div><div class="value">S/{{ number_format($ingresosTotales ?? 0, 2) }}</div><div class="label">Ingresos</div></div></div>
        <div class="header-metric"><div class="icon" style="background-color: #ef5350;"><i class="fas fa-users"></i></div><div><div class="value">{{ $totalClientes ?? 0 }}</div><div class="label">Clientes</div></div></div>
        <div class="header-metric"><div class="icon" style="background-color: var(--kpi-green);"><i class="fas fa-box-open"></i></div><div><div class="value">{{ $totalProductos ?? 0 }}</div><div class="label">Productos</div></div></div>
        <div class="header-metric"><div class="icon" style="background-color: var(--kpi-orange);"><i class="fas fa-tags"></i></div><div><div class="value">{{ $totalCategorias ?? 0 }}</div><div class="label">Categorías</div></div></div>
    </div>
</div>