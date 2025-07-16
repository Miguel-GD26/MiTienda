@extends('plantilla.app')

@section('titulo', isset($registro) ? 'Editar Categoría' : 'Nueva Categoría')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">

        <h2 class="h3 mb-4">
            <i class="fa-solid fa-tags me-2"></i>
            Gestión de Categorías
        </h2>

        {{-- Mostrar errores de validación en la parte superior --}}
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
            <!-- Columna del Formulario -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="fa-solid {{ isset($registro) ? 'fa-pen-to-square' : 'fa-folder-plus' }} me-2"></i>
                            {{ isset($registro) ? 'Editar Categoría: ' . $registro->nombre : 'Nueva Categoría' }}
                        </h5>
                    </div>
                    
                    <form action="{{ isset($registro) ? route('categorias.update', $registro) : route('categorias.store') }}" method="post" autocomplete="off">
                        @csrf
                        @if(isset($registro))
                            @method('PUT')
                        @endif
                        
                        <div class="card-body p-4">
                            <p class="text-muted small">
                                {{ isset($registro) ? 'Actualiza los datos de la categoría.' : 'Completa los campos para añadir una nueva categoría.' }} Los campos con <span class="text-danger">*</span> son obligatorios. 
                            </p>
                            <hr class="mb-4">

                            {{-- SELECT DE EMPRESA (SOLO PARA SUPER ADMIN) --}}
                            @if(auth()->user()->hasRole('super_admin'))
                            <div class="form-floating mb-3">
                                <select class="form-select @error('empresa_id') is-invalid @enderror" 
                                        id="empresa_id" name="empresa_id" 
                                        {{ isset($registro) ? 'disabled' : '' }} required>
                                    
                                    <option value="" disabled {{ old('empresa_id', $registro->empresa_id ?? '') == '' ? 'selected' : '' }}>Seleccione una empresa...</option>
                                    @foreach($empresas as $empresa)
                                        <option value="{{ $empresa->id }}" 
                                            {{ old('empresa_id', $registro->empresa_id ?? '') == $empresa->id ? 'selected' : '' }}>
                                            {{ $empresa->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="empresa_id">Empresa a la que pertenece <span class="text-danger">*</span></label>
                                @if(isset($registro))
                                    <input type="hidden" name="empresa_id" value="{{ $registro->empresa_id }}">
                                @endif
                                @error('empresa_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            @endif

                            <!-- CAMPO NOMBRE -->
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" placeholder="Nombre de la Categoría" value="{{ old('nombre', $registro->nombre ?? '') }}" required>
                                <label for="nombre">Nombre de la Categoría <span class="text-danger">*</span></label>
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <!-- CAMPO DESCRIPCIÓN -->
                            <div class="form-floating mb-4">
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" placeholder="Descripción (Opcional)" style="height: 100px">{{ old('descripcion', $registro->descripcion ?? '') }}</textarea>
                                <label for="descripcion">Descripción (Opcional)</label>
                                @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white text-end border-0 pt-0 pb-4 px-4">
                            <a href="{{ route('categorias.index') }}" class="btn btn-secondary me-2"><i class="fa-solid fa-xmark me-1"></i> Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk me-1"></i> 
                                {{ isset($registro) ? 'Actualizar Cambios' : 'Guardar Categoría' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Columna Lateral del Panel de Ayuda -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fa-solid fa-circle-info me-2"></i> Panel de Ayuda</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            {{-- LÍNEA CORREGIDA --}}
                            <img src="{{ asset('assets/img/categorias.gif') }}"
                                 class="img-fluid" 
                                 alt="Ilustración de organización" 
                                 style="max-height: 150px;">
                        </div>
                        
                        <h6 class="text-muted"><i class="fa-solid fa-lightbulb text-warning me-2"></i>Consejos Clave</h6>
                        <ul class="list-unstyled text-muted small ps-3">
                            <li class="mb-2"><i class="fa-solid fa-check-double text-success me-1"></i> <strong>Nombres Cortos y Claros:</strong> Usa nombres que describan el grupo de productos que contendrán.</li>
                            <li class="mb-2"><i class="fa-solid fa-check-double text-success me-1"></i> <strong>Sé Consistente:</strong> Mantén un estilo similar en todas tus categorías para una mejor organización.</li>
                        </ul>
                        
                        <hr>
                        
                        <h6 class="text-muted"><i class="fa-solid fa-star me-2"></i>Buenos Ejemplos</h6>
                        <div class="list-group list-group-flush small">
                            <div class="list-group-item px-0"><i class="fa-solid fa-mug-hot text-brown me-2"></i> Bebidas Calientes</div>
                            <div class="list-group-item px-0"><i class="fa-solid fa-ice-cream text-info me-2"></i> Postres y Dulces</div>
                            <div class="list-group-item px-0"><i class="fa-solid fa-burger text-warning me-2"></i> Snacks Salados</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('estilos')
<style> .text-brown { color: #8B4513; } </style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuPrincipal = document.getElementById('mnuGestion');
        if (menuPrincipal) menuPrincipal.classList.add('menu-open');
        const itemCategoria = document.getElementById('itemCategoria');
        if (itemCategoria) itemCategoria.classList.add('active');
    });
</script>
@endpush