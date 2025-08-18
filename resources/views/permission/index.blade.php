@extends('plantilla.app')

@section('titulo', 'Gestión de Permisos')

@section('contenido')
    <main class="app-main">
        @livewire('permission-management')
    </main>
@endsection

@push('estilos')
<style>
    .card-permission {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card-permission:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
    }
    .icon-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-size: 1.5rem;
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