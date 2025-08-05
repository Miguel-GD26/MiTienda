{{-- resources/views/usuario/index.blade.php --}}
@extends('plantilla.app')

@section('contenido')
    {{-- Simplemente llama a tu componente Livewire --}}
    @livewire('user-management')
@endsection

@push('estilos')
<style>
/* 
  Estos estilos se aplicarán a cualquier .form-check-input en la página,
  incluyendo el que está dentro de nuestro modal de Livewire.
*/
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
    // Tu código para activar el menú lateral sigue siendo útil
    document.getElementById('mnuSeguridad').classList.add('menu-open');
    document.getElementById('itemUsuario').classList.add('active');

    // Livewire manejará los tooltips, pero podemos reiniciarlos si es necesario
    // después de cada renderizado de Livewire.
    document.addEventListener('livewire:navigated', () => {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush