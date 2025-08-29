@extends('plantilla.app')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">

        {{-- Aquí se cargará todo el componente de gestión de empresas --}}
        @livewire('company-management')

    </div>
</main>
@endsection

@push('scripts')
<script>
    // Aquí puedes añadir scripts específicos para esta página si los necesitas en el futuro.
    // Por ejemplo, para activar un item del menú lateral:
    // document.getElementById('itemEmpresa').classList.add('active');
</script>
@endpush