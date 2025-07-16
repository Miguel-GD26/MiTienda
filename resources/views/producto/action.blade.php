@extends('plantilla.app')

@section('titulo', isset($producto) ? 'Editar Producto' : 'Nuevo Producto')

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4" 
         x-data="{ 
             // Inicializa la empresa seleccionada con el valor actual o el antiguo si hay un error de validación
             empresaSeleccionada: '{{ old('empresa_id', $producto->empresa_id ?? '') }}',
             // Pasa todas las categorías a AlpineJS, agrupadas por su empresa_id (solo para Super Admin)
             categorias: {{ auth()->user()->hasRole('super_admin') ? json_encode($categorias->groupBy('empresa_id')) : '[]' }}
         }">
        
        <h2 class="h3 mb-4"><i class="fa-solid fa-box-open me-2"></i>Gestión de Productos</h2>

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
                            <i class="fa-solid {{ isset($producto) ? 'fa-pen-to-square' : 'fa-plus-circle' }} me-2"></i>
                            {{ isset($producto) ? 'Editar Producto: ' . $producto->nombre : 'Nuevo Producto' }}
                        </h5>
                    </div>
                    
                    <form action="{{ isset($producto) ? route('productos.update', $producto) : route('productos.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @if(isset($producto))
                            @method('PUT')
                        @endif
                        
                        <div class="card-body p-4">
                            <p class="text-muted small">
                                Completa los detalles del producto. Los campos con <span class="text-danger">*</span> son obligatorios.
                            </p>
                            <hr class="mb-4">

                            {{-- SELECT DE EMPRESA (SOLO PARA SUPER ADMIN) --}}
                            @if(auth()->user()->hasRole('super_admin'))
                            <div class="mb-3">
                                <label for="empresa_id" class="form-label">Empresa <span class="text-danger">*</span></label>
                                <select name="empresa_id" id="empresa_id" class="form-select @error('empresa_id') is-invalid @enderror" 
                                        x-model="empresaSeleccionada" 
                                        {{-- El campo se deshabilita en modo edición --}}
                                        {{ isset($producto) ? 'disabled' : '' }} required>
                                    
                                    <option value="" disabled {{ old('empresa_id', $producto->empresa_id ?? '') == '' ? 'selected' : '' }}>-- Seleccione una empresa --</option>
                                    @foreach($empresas as $empresa)
                                        <option value="{{ $empresa->id }}" {{ old('empresa_id', $producto->empresa_id ?? '') == $empresa->id ? 'selected' : '' }}>
                                            {{ $empresa->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                {{-- Campo oculto para enviar el ID de la empresa en modo edición --}}
                                @if(isset($producto))
                                    <input type="hidden" name="empresa_id" value="{{ $producto->empresa_id }}">
                                @endif
                                @error('empresa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            @endif

                            <!-- Fila 1: Nombre y Categoría -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" id="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}" required>
                                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="categoria_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                                    <select name="categoria_id" id="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" 
                                            :disabled="!empresaSeleccionada && {{ auth()->user()->hasRole('super_admin') ? 'true' : 'false' }}" required>
                                        
                                        @if(auth()->user()->hasRole('super_admin'))
                                            <option value="">-- Seleccione una empresa primero --</option>
                                            <template x-if="empresaSeleccionada">
                                                <template x-for="categoria in categorias[empresaSeleccionada]" :key="categoria.id">
                                                    <option :value="categoria.id" :selected="categoria.id == '{{ old('categoria_id', $producto->categoria_id ?? '') }}'" x-text="categoria.nombre"></option>
                                                </template>
                                            </template>
                                        @else
                                            <option value="">-- Seleccione una categoría --</option>
                                            @foreach ($categorias as $categoria)
                                                <option value="{{ $categoria->id }}" {{ old('categoria_id', $producto->categoria_id ?? '') == $categoria->id ? 'selected' : '' }}>
                                                    {{ $categoria->nombre }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('categoria_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    @if ($categorias->isEmpty() && !auth()->user()->hasRole('super_admin'))
                                        <div class="form-text text-danger mt-2">
                                            No puedes crear un producto sin categorías. <a href="{{ route('categorias.create') }}">Crea una primero</a>.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Fila 2: Precio y Stock -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="precio" class="form-label">Precio <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control @error('precio') is-invalid @enderror" name="precio" id="precio" value="{{ old('precio', $producto->precio ?? '0.00') }}" required>
                                    </div>
                                    @error('precio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="stock" class="form-label">Stock</label>
                                    <input type="number" class="form-control @error('stock') is-invalid @enderror" name="stock" id="stock" value="{{ old('stock', $producto->stock ?? '') }}" placeholder="Dejar vacío si no aplica">
                                    @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <!-- Fila 3: Descripción -->
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción (Opcional)</label>
                                <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="4">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
                                @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Fila 4: Subida de Imagen -->
                            <div class="mb-3">
                                <label for="imagen_url" class="form-label">Imagen del Producto</label>
                                <input type="file" class="form-control @error('imagen_url') is-invalid @enderror" name="imagen_url" id="imagen_url" accept="image/*">
                                @error('imagen_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @isset($producto->imagen_url)
                                    <small class="form-text text-muted">Sube una nueva imagen solo si deseas reemplazar la actual.</small>
                                @endisset
                            </div>

                            <!-- Sección para mostrar la imagen actual -->
                            @isset($producto->imagen_url)
                                <div class="mb-3">
                                    <label>Imagen Actual:</label><br>
                                    <img src="{{ cloudinary()->image($producto->imagen_url)->toUrl() }}"
                                         alt="{{ $producto->nombre }}" 
                                         class="img-thumbnail"
                                         style="max-width: 150px; max-height: 150px;">
                                </div>
                            @endisset
                        </div>
                        
                        <div class="card-footer bg-white text-end border-0 pt-0 pb-4 px-4">
                            <a href="{{ route('productos.index') }}" class="btn btn-secondary me-2"><i class="fa-solid fa-xmark me-1"></i> Cancelar</a>
                            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i> Guardar Producto</button>
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
                            <img src="{{ asset('assets/img/productos.gif') }}" class="img-fluid" alt="Gestión de productos" style="max-height: 150px;">
                        </div>
                        <h6 class="text-muted"><i class="fa-solid fa-lightbulb text-warning me-2"></i>Buenas Prácticas</h6>
                        <ul class="list-unstyled text-muted small ps-3">
                            <li class="mb-2"><i class="fa-solid fa-check text-success me-1"></i> <strong>Nombres claros:</strong> El nombre debe ser descriptivo y fácil de entender.</li>
                            <li class="mb-2"><i class="fa-solid fa-check text-success me-1"></i> <strong>Precio correcto:</strong> Asegúrate de que el precio sea el final para el cliente.</li>
                            <li class="mb-2"><i class="fa-solid fa-check text-success me-1"></i> <strong>Imágenes de calidad:</strong> Una buena imagen vende más. Usa fotos claras y atractivas.</li>
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
    // Activar el menú correspondiente en el sidebar
    const menuGestion = document.getElementById('mnuGestion');
    if (menuGestion) menuGestion.classList.add('menu-open');
    const itemProducto = document.getElementById('itemProducto');
    if (itemProducto) itemProducto.classList.add('active');
</script>
@endpush