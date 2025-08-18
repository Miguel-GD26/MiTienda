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

