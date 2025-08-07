@extends('autenticacion.app')

@section('titulo', 'Sistema - Registro')

@section('contenido')
    @livewire('auth.register.register') 
@endsection

@push('scripts')
    @include('plantilla.partials.sweetalert-listener')
@endpush

