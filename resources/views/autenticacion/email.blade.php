@extends('autenticacion.app')
@section('titulo', 'Sistema - Recuperar Contraseña')

@section('contenido')
  @livewire('auth.password-reset.request-form')
@endsection

@push('scripts')
    @include('plantilla.partials.sweetalert-listener')
@endpush