@extends('plantilla.app')

@section('titulo', 'Gestión de Roles')

@section('contenido')
<main class="app-main">
    @livewire('role-management')
</main>
@endsection

@push('estilos')
<style>
    .card.border-start-primary {
        border-left: 4px solid var(--bs-primary);
        transition: all 0.2s ease-in-out;
    }
    .card.border-start-primary:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endpush

@push('scripts')
    {{-- Script para activar el menú lateral --}}
    <script>
        if (document.getElementById('mnuSeguridad')) {
            document.getElementById('mnuSeguridad').classList.add('menu-open');
        }
        if (document.getElementById('itemRole')) {
            document.getElementById('itemRole').classList.add('active');
        }
    </script>

    {{-- Incluimos el listener de notificaciones reutilizable --}}
    @include('plantilla.partials.sweetalert-listener')

    {{-- Script de tooltips --}}
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