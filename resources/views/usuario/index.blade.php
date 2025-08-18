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
