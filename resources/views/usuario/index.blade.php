@extends('plantilla.app')

@section('contenido')
    <main class="app-main"> <!-- Añadido para consistencia con tu layout -->
        @livewire('user-management')
    </main>
@endsection

@push('estilos')
<style>
/* Estilos para el switch de Activo/Inactivo */
.form-check-input {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e") !important;
}

.form-check-input:checked {
    background-color: #198754 !important;
    border-color: #198754 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e") !important;
}


</style>
@endpush

@push('scripts')
    {{-- Script para activar el menú lateral --}}
    <script>
        if (document.getElementById('mnuSeguridad')) {
            document.getElementById('mnuSeguridad').classList.add('menu-open');
        }
        if (document.getElementById('itemUsuario')) {
            document.getElementById('itemUsuario').classList.add('active');
        }
    </script>

    {{-- Incluimos el listener de notificaciones reutilizable --}}
    @include('plantilla.partials.sweetalert-listener')

    {{-- El script de los tooltips también podría ir en su propio parcial si lo usas mucho --}}
    <script>
        function initTooltips() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                var existingTooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if (existingTooltip) { existingTooltip.dispose(); }
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
        document.addEventListener('livewire:init', () => {
            initTooltips();
            Livewire.hook('morph.updated', () => { initTooltips(); });
        });
    </script>
@endpush