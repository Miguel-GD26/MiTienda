@extends('plantilla.app')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">
        @livewire('customer-list')
    </div>
</main>
@endsection