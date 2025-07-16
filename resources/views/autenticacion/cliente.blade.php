@extends('welcome.app')

@section('titulo', 'Mi Perfil')

@section('contenido')
<main class="app-main">
    <div class="container-contenido">
        
        <h2 class="h3 mb-4">
            <i class="fa-solid fa-id-card-clip me-2"></i>
            Editar Mi Perfil
        </h2>

        <div class="row"> 
            <div class="col-md-12">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        @if (session('mensaje'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa-solid fa-circle-check me-2"></i>
                                {{ session('mensaje') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- SECCIÓN DATOS DE CUENTA --}}
                            <h5 class="mb-3 text-primary"><i class="fa-solid fa-user-lock me-2"></i>Datos de tu Cuenta de Acceso</h5>
                            <p class="text-muted small">Usa estos datos para iniciar sesión. Tu nombre se usará en todo el sistema.</p>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nombre Completo</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                     id="name" name="name" value="{{ old('name', $registro->name ?? '') }}" required>
                                     @error('name') <small class="text-danger">{{$message}}</small> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                     id="email" name="email"  value="{{ old('email', $registro->email ?? '') }}" required>
                                     @error('email') <small class="text-danger">{{$message}}</small> @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Nueva Contraseña</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                     id="password" name="password" placeholder="Dejar en blanco para no cambiar">
                                     @error('password') <small class="text-danger">{{$message}}</small> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                    <input type="password" class="form-control"
                                     id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>

                            {{-- SECCIÓN DATOS DEL CLIENTE --}}
                            @if(isset($cliente))
                                <hr class="my-4">
                                <h5 class="mb-3 text-primary"><i class="fa-solid fa-address-book me-2"></i>Tus Datos de Contacto</h5>
                                <p class="text-muted small">Esta información se usará para tus pedidos y facturación.</p>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="cliente_telefono" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control @error('cliente_telefono') is-invalid @enderror"
                                            id="cliente_telefono" name="cliente_telefono" value="{{ old('cliente_telefono', $cliente->telefono) }}"
                                            placeholder="Tu número de teléfono de contacto">
                                        @error('cliente_telefono') <small class="text-danger">{{$message}}</small> @enderror
                                    </div>
                                    {{-- Aquí puedes añadir más campos de CLIENTE si los necesitas --}}
                                </div>
                            @endif

                            {{-- SECCIÓN DATOS DE LA EMPRESA --}}
                            @if(isset($empresa))
                                <hr class="my-4">
                                <h5 class="mb-3 text-primary"><i class="fa-solid fa-building me-2"></i>Datos de tu Empresa</h5>
                                <p class="text-muted small">Información pública y de contacto de tu tienda.</p>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="empresa_nombre" class="form-label">Nombre de la Empresa</label>
                                        <input type="text" class="form-control @error('empresa_nombre') is-invalid @enderror"
                                            id="empresa_nombre" name="empresa_nombre" value="{{ old('empresa_nombre', $empresa->nombre) }}" required>
                                        @error('empresa_nombre') <small class="text-danger">{{$message}}</small> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="empresa_rubro" class="form-label">Rubro</label>
                                        <input type="text" class="form-control @error('empresa_rubro') is-invalid @enderror"
                                            id="empresa_rubro" name="empresa_rubro" value="{{ old('empresa_rubro', $empresa->rubro) }}" required>
                                        @error('empresa_rubro') <small class="text-danger">{{$message}}</small> @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="empresa_telefono_whatsapp" class="form-label">Teléfono / WhatsApp</label>
                                        <input type="text" class="form-control @error('empresa_telefono_whatsapp') is-invalid @enderror"
                                            id="empresa_telefono_whatsapp" name="empresa_telefono_whatsapp" value="{{ old('empresa_telefono_whatsapp', $empresa->telefono_whatsapp) }}" required>
                                        @error('empresa_telefono_whatsapp') <small class="text-danger">{{$message}}</small> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="empresa_logo" class="form-label">Logo</label>
                                        <input type="file" class="form-control @error('empresa_logo') is-invalid @enderror" id="empresa_logo" name="empresa_logo">
                                        @error('empresa_logo') <small class="text-danger">{{$message}}</small> @enderror
                                    </div>
                                </div>
                            @endif
                            
                            <div class="card-footer bg-white text-end border-0 pt-3 px-0">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Actualizar Perfil
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    // Lógica para marcar el menú activo en el sidebar (si tienes un ítem de "Perfil")
    const itemPerfil = document.getElementById('itemPerfil'); // Asegúrate de que este ID exista en tu menú
    if (itemPerfil) {
        itemPerfil.classList.add('active');
    }
</script>
@endpush