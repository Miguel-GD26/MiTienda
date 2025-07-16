@extends('plantilla.app')
@section('contenido')
<div class="app-content">
    <div class="container-fluid">
        <div class="row"> 
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Mi Perfil</h3>
                    </div>
                    <div class="card-body">
                        @if (session('mensaje'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('mensaje') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- SECCIÓN DE DATOS DEL USUARIO (Siempre visible) --}}
                            <h5 class="mb-3 text-primary">Datos de tu Cuenta</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nombre</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                     id="name" name="name" value="{{ old('name', $registro->name ?? '') }}" required>
                                     @error('name') <small class="text-danger">{{$message}}</small> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                     id="email" name="email" value="{{ old('email', $registro->email ?? '') }}" required>
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

                            {{-- SECCIÓN DE DATOS DE LA EMPRESA (Visible solo si es admin y tiene empresa) --}}
                            @if(isset($empresa))
                                <hr class="my-4">
                                <h5 class="mb-3 text-primary">Datos de tu Empresa</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="empresa_nombre" class="form-label">Nombre de la Empresa</label>
                                        <input type="text" class="form-control @error('empresa_nombre') is-invalid @enderror"
                                            id="empresa_nombre" name="empresa_nombre" value="{{ old('empresa_nombre', $empresa->nombre) }}">
                                        @error('empresa_nombre') <small class="text-danger">{{$message}}</small> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="empresa_rubro" class="form-label">Rubro</label>
                                        <input type="text" class="form-control @error('empresa_rubro') is-invalid @enderror"
                                            id="empresa_rubro" name="empresa_rubro" value="{{ old('empresa_rubro', $empresa->rubro) }}">
                                        @error('empresa_rubro') <small class="text-danger">{{$message}}</small> @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="empresa_telefono_whatsapp" class="form-label">Teléfono / WhatsApp de la Empresa</label>
                                        <input type="text" class="form-control @error('empresa_telefono_whatsapp') is-invalid @enderror"
                                            id="empresa_telefono_whatsapp" name="empresa_telefono_whatsapp" value="{{ old('empresa_telefono_whatsapp', $empresa->telefono_whatsapp) }}">
                                        @error('empresa_telefono_whatsapp') <small class="text-danger">{{$message}}</small> @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="empresa_logo" class="form-label">Logo de la Empresa</label>
                                            <input type="file" class="form-control @error('empresa_logo') is-invalid @enderror" id="empresa_logo" name="empresa_logo" accept="image/*">
                                            @error('empresa_logo') <small class="text-danger">{{$message}}</small> @enderror
                                            <small class="form-text text-muted">Sube un nuevo logo solo si deseas reemplazar el actual.</small>
                                        </div>

                                        {{-- NUEVO: Sección para mostrar el logo actual --}}
                                        @if(isset($empresa) && $empresa->logo_url)
                                            <div class="col-md-6 mb-3">
                                                <label>Logo Actual:</label><br>
                                                <img src="{{ cloudinary()->image($empresa->logo_url)->toUrl() }}"
                                                    alt="Logo de {{ $empresa->nombre }}" 
                                                    class="img-thumbnail"
                                                    style="max-width: 150px; max-height: 150px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <hr>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection