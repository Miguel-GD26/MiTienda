@extends('plantilla.app')

@section('titulo', 'Gestión de Categorías')

@section('contenido')
    <main class="app-main">
        @livewire('category-management')
    </main>
@endsection

@push('estilos')
{{-- Estilos para el círculo del icono --}}
<style>
    .icon-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
    }
</style>
@endpush

<!-- @push('scripts')
    {{-- Script para activar el menú lateral --}}
    <script>
        if (document.getElementById('mnuSeguridad')) {
            document.getElementById('mnuSeguridad').classList.add('menu-open');
        }
        if (document.getElementById('itemRole')) {
            document.getElementById('itemRole').classList.add('active');
        }
    </script>


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
@endpush -->