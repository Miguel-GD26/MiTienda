@extends('plantilla.app')

@push('estilos')
<style>
    :root {
        --body-bg: #f4f7fe;
        --card-bg: #ffffff;
        --card-border-color: #eef0f7;
        --text-heading: #3f4d67;
        --text-body: #6e7a91;
        --kpi-blue: #2979ff; --kpi-blue-light: #82aaff;
        --kpi-red: #ff5252; --kpi-red-light: #ff8a80;
        --kpi-purple: #7c4dff; --kpi-purple-light: #b388ff;
        --kpi-green: #00e676; --kpi-green-light: #b9f6ca;
        --kpi-orange: #ff9100; --kpi-orange-light: #ffc947;
    }
    .app-content { background-color: var(--body-bg) !important; padding: 2rem; }

    /* Cabecera */
    .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .header-title-group .logo-icon { font-size: 2.5rem; color: var(--kpi-blue); }
    .header-title { color: var(--text-heading); font-weight: 600; font-size: 1.75rem; }
    .header-subtitle { color: var(--text-body); font-size: 1.1rem; }
    .header-metric { display: flex; align-items: center; margin-left: 1rem; background: var(--card-bg); padding: 0.5rem 1rem; border-radius: 0.75rem; border: 1px solid var(--card-border-color); }
    .header-metric .icon { font-size: 1.5rem; color: #fff; background-color: #aab2c7; padding: 0.75rem; border-radius: 0.5rem; margin-right: 0.75rem; }
    .header-metric .value { font-size: 1.25rem; font-weight: 700; color: var(--text-heading); }
    .header-metric .label { font-size: 0.8rem; color: var(--text-body); }
    
    /* KPI Cards */
    .kpi-card-gradient { color: white; border-radius: 3rem; padding: 1.5rem; display: flex; align-items: center; transition: all 0.2s ease-in-out; box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1); text-decoration: none; }
    .kpi-card-gradient:hover { transform: translateY(-5px); box-shadow: 0 10px 20px -5px rgba(0,0,0,0.2); }
    .kpi-card-gradient .icon-circle { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: rgba(255,255,255,0.2); font-size: 1.75rem; margin-right: 1.5rem; flex-shrink: 0; }
    .kpi-card-gradient .kpi-title { font-size: 1rem; opacity: 0.9; }
    .kpi-card-gradient .kpi-value { font-size: 2.25rem; font-weight: 700; line-height: 1; }
    .bg-gradient-blue { background: linear-gradient(90deg, var(--kpi-blue), var(--kpi-blue-light)); }
    .bg-gradient-red { background: linear-gradient(90deg, var(--kpi-red), var(--kpi-red-light)); }
    .bg-gradient-purple { background: linear-gradient(90deg, var(--kpi-purple), var(--kpi-purple-light)); }
    .bg-gradient-green { background: linear-gradient(90deg, var(--kpi-green), var(--kpi-green-light)); }
    .bg-gradient-orange { background: linear-gradient(90deg, var(--kpi-orange), var(--kpi-orange-light)); }
    
    /* Contenedores de Widgets */
    .content-card { background: var(--card-bg); border-radius: 1rem; padding: 1.5rem; box-shadow: 0 5px 15px rgba(0,0,0,0.05); height: 100%; }
    .card-title-custom { color: var(--text-heading); font-weight: 600; margin-bottom: 1.5rem; }
    
    /* Tabla de Actividad */
    .activity-table-sleek { border-collapse: separate; border-spacing: 0 0.75rem; width: 100%; }
    .activity-table-sleek thead th { text-align: left; font-size: 0.8rem; color: var(--text-body); text-transform: uppercase; padding: 0 1rem; font-weight: 600; }
    .activity-table-sleek tbody tr { background-color: #fcfdff; transition: all 0.2s ease; }
    .activity-table-sleek tbody tr:hover { background-color: var(--card-bg); box-shadow: 0 4px 10px rgba(0,0,0,0.07); transform: scale(1.02); z-index: 2; position: relative; }
    .activity-table-sleek tbody td { padding: 1rem; vertical-align: middle; }
    .activity-table-sleek tbody td:first-child { border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem; }
    .activity-table-sleek tbody td:last-child { border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
    .client-avatar { width: 32px; height: 32px; border-radius: 50%; margin-right: 0.75rem; }
    
    /* Widgets Inferiores */
    .progress-segmented { display: flex; width: 100%; height: 1.25rem; border-radius: 1rem; overflow: hidden; background-color: var(--card-border-color); }
    .progress-segment { height: 100%; transition: width 0.3s ease; }
    .top-list .top-list-item { display: flex; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--card-border-color); }
    .top-list .top-list-item:last-child { border-bottom: none; }
    .top-list .item-rank { font-weight: 700; color: var(--text-body); margin-right: 1rem; width: 25px; }
    .top-list .item-value { margin-left: auto; font-weight: 600; color: var(--text-heading); }
</style>
@endpush

@section('contenido')
<div class="app-content">
    <div class="container-fluid">
        @if(auth()->user()->hasRole('super_admin'))
            <!-- Cabecera Super Admin -->
            <div class="dashboard-header">
                <div class="d-flex align-items-center">
                    <div class="logo-icon"><i class="fas fa-globe-americas"></i></div>
                    <div class="ms-3">
                        <h2 class="header-subtitle mb-0">Bienvenido de nuevo,</h2>
                        <h1 class="header-title">Dashboard Global</h1>
                    </div>
                </div>
                <div class="d-none d-lg-flex">
                    <div class="header-metric">
                        <div class="icon" style="background-color: var(--kpi-blue);"><i class="fas fa-coins"></i></div>
                        <div>
                            <div class="value">S/{{ number_format($ingresosTotales ?? 0, 2) }}</div>
                            <div class="label">Ingresos</div>
                        </div>
                    </div>
                    <div class="header-metric">
                        <div class="icon" style="background-color: var(--kpi-purple);"><i class="fas fa-building"></i></div>
                        <div>
                            <div class="value">{{ $totalEmpresas ?? 0 }}</div>
                            <div class="label">Empresas</div>
                        </div>
                    </div>
                     <div class="header-metric">
                        <div class="icon" style="background-color: var(--kpi-green);"><i class="fas fa-box-open"></i></div>
                        <div>
                            <div class="value">{{ $totalProductos ?? 0 }}</div>
                            <div class="label">Productos</div>
                        </div>
                    </div>
                    <div class="header-metric">
                        <div class="icon" style="background-color: var(--kpi-orange);"><i class="fas fa-tags"></i></div>
                        <div>
                            <div class="value">{{ $totalCategorias ?? 0 }}</div>
                            <div class="label">Categorías</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal Super Admin -->
            @include('dashboard-content')

        @elseif(auth()->user()->hasRole('admin'))
            @if(auth()->user()->empresa)
                <div class="dashboard-header">
                    <div class="d-flex align-items-center">
                        <div class="logo-icon"><i class="fas fa-store"></i></div>
                        <div class="ms-3">
                            <h2 class="header-subtitle mb-0">Bienvenido, {{ auth()->user()->name }}</h2>
                            <h1 class="header-title">Panel de {{ auth()->user()->empresa->nombre }}</h1>
                        </div>
                    </div>
                    <div class="d-none d-lg-flex">
                        <div class="header-metric">
                            <div class="icon" style="background-color: var(--kpi-blue);"><i class="fas fa-coins"></i></div>
                            <div>
                                <div class="value">S/{{ number_format($ingresosTotales ?? 0, 2) }}</div>
                                <div class="label">Ingresos</div>
                            </div>
                        </div>
                        <div class="header-metric">
                            <div class="icon" style="background-color: #ef5350;"><i class="fas fa-users"></i></div>
                            <div>
                                <div class="value">{{ $totalClientes ?? 0 }}</div>
                                <div class="label">Clientes</div>
                            </div>
                        </div>
                        <div class="header-metric">
                            <div class="icon" style="background-color: var(--kpi-green);"><i class="fas fa-box-open"></i></div>
                            <div>
                                <div class="value">{{ $totalProductos ?? 0 }}</div>
                                <div class="label">Productos</div>
                            </div>
                        </div>
                         <div class="header-metric">
                            <div class="icon" style="background-color: var(--kpi-orange);"><i class="fas fa-tags"></i></div>
                            <div>
                                <div class="value">{{ $totalCategorias ?? 0 }}</div>
                                <div class="label">Categorías</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenido Principal Admin -->
                @include('dashboard-content')

            @else
                <div class="alert alert-warning">
                    <strong>Atención:</strong> No tienes una empresa asignada. Contacta al Super Administrador.
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('sparkline-chart')) {
            const sparklineOptions = {
              series: [{
                name: 'Ingresos',
                data: {!! json_encode($sparkline['series'] ?? []) !!}
              }],
              chart: { type: 'area', height: 160, sparkline: { enabled: true }, },
              stroke: { curve: 'smooth', width: 2 },
              fill: {
                type: 'gradient',
                gradient: { shade: 'light', type: "vertical", shadeIntensity: 0.3, opacityFrom: 0.7, opacityTo: 0.3, }
              },
              colors: ['var(--kpi-blue)'],
              xaxis: { type: 'datetime', categories: {!! json_encode($sparkline['labels'] ?? []) !!}, },
              tooltip: {
                x: { format: 'dd MMM yyyy' },
                y: {
                  formatter: function(val) { return "S/ " + val.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) },
                  title: { formatter: (seriesName) => 'Ingresos:' }
                }
              }
            };
            const chart = new ApexCharts(document.getElementById("sparkline-chart"), sparklineOptions);
            chart.render();
        }
    });
</script>
@endpush