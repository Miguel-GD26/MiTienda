@extends('plantilla.app')

@section('titulo', isset($registro) ? 'Editar Permiso' : 'Crear Permiso')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">
    
        <h2 class="h3 mb-4">
            <i class="fa-solid {{ isset($registro) ? 'fa-key' : 'fa-plus-square' }} me-2"></i>
            {{ isset($registro) ? 'Editar Permiso: ' : 'Crear Nuevo Permiso' }}
        </h2>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">¡Ups! Revisa los siguientes errores:</h4>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <form action="{{ isset($registro) ? route('permisos.update', $registro->id) : route('permisos.store') }}" method="POST">
                        @csrf
                        @if(isset($registro))
                            @method('PUT')
                        @endif

                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="card-title mb-0 text-primary"><i class="fa-solid fa-file-signature me-2"></i>Detalles del Permiso</h5>
                            <small><br>Los campos con <span class="text-danger">*</span> son obligatorios.</small>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre del Permiso (Clave) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-hashtag"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $registro->name ?? '') }}" 
                                        placeholder="Ej: producto-listar, usuario-crear" required>
                                </div>
                                <small class="form-text text-muted">Debe ser único, en minúsculas y usar el formato `módulo-acción`.</small>
                                @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white text-end border-0 pt-0 pb-4 px-4">
                            <a href="{{ route('permisos.index') }}" class="btn btn-secondary me-2"><i class="fa-solid fa-xmark me-1"></i> Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk me-1"></i> 
                                {{ isset($registro) ? 'Actualizar Permiso' : 'Guardar Permiso' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                     <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fa-solid fa-circle-info me-2"></i> Panel de Ayuda</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <img src="{{ asset('assets/img/permisos.gif') }}" class="img-fluid" alt="Gestión de permisos" style="max-height: 150px;">
                        </div>
                        <h6 class="text-muted"><i class="fa-solid fa-unlock-keyhole text-success me-2"></i>¿Qué es un Permiso?</h6>
                        <p class="text-muted small">
                            Un permiso es una acción específica que un usuario puede realizar. Son la base de la seguridad. Los roles agrupan estos permisos para asignarlos fácilmente.
                        </p>
                        <hr>
                        <h6 class="text-muted"><i class="fa-solid fa-lightbulb text-warning me-2"></i>Formato Recomendado</h6>
                        <p class="text-muted small">
                            Usa el formato `módulo-acción` para mantener tus permisos organizados.
                        </p>
                        <ul class="list-unstyled small text-muted">
                            <li><i class="fa-solid fa-check text-success"></i> <strong>Bueno:</strong> `producto-listar`</li>
                            <li><i class="fa-solid fa-times text-danger"></i> <strong>Malo:</strong> `listarproductos`</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    document.getElementById('mnuSeguridad').classList.add('menu-open');
    document.getElementById('itemPermiso').classList.add('active');
</script>
@endpush