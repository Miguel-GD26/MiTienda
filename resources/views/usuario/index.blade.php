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
<script>
    // Activa el menú lateral correspondiente
    if (document.getElementById('mnuSeguridad')) {
        document.getElementById('mnuSeguridad').classList.add('menu-open');
    }
    if (document.getElementById('itemUsuario')) {
        document.getElementById('itemUsuario').classList.add('active');
    }

    // Inicialización de tooltips de Bootstrap
    function initTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            // Si ya existe un tooltip, lo destruimos primero
            var existingTooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
            if (existingTooltip) {
                existingTooltip.dispose();
            }
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    document.addEventListener('livewire:navigated', () => {
        initTooltips();
    });
    
    // Listener para las notificaciones Toast con SweetAlert2
    document.addEventListener('livewire:init', () => {
        initTooltips(); // Inicializar tooltips al cargar la página

        Livewire.on('alert', (event) => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: event[0].type, // 'success' o 'error'
                title: event[0].message
            });
        });

        // Re-inicializar tooltips después de cada actualización del componente
        Livewire.hook('morph.updated', ({ el, component }) => {
            initTooltips();
        });
    });
</script>
@endpush