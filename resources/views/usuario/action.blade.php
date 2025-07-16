@extends('plantilla.app')

@section('titulo', isset($registro) ? 'Editar Usuario' : 'Crear Usuario')

@section('contenido')
<div class="container-fluid mt-4" x-data="{
             selectedRole: '{{ old('role', isset($registro) ? ($registro->roles->first()->name ?? '') : '') }}',
             empresaOption: '{{ old('empresa_id', isset($registro) ? $registro->empresa_id : '') }}'
         }">

    <h2 class="h3 mb-4">
        <i class="fa-solid {{ isset($registro) ? 'fa-user-pen' : 'fa-user-plus' }} me-2"></i>
        {{ isset($registro) ? 'Editar Usuario' : 'Crear Usuario' }}
    </h2>

    {{-- Bloque para mostrar mensajes de éxito --}}
    @if (session('mensaje'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('mensaje') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Bloque para mostrar todos los errores de validación en la parte superior --}}
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
                <form
                    action="{{ isset($registro) ? route('usuarios.update', $registro->id) : route('usuarios.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($registro))
                    @method('PUT')
                    @endif

                    {{-- SECCIÓN 1: INFORMACIÓN PERSONAL (SIEMPRE VISIBLE) --}}
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="card-title mb-0 text-primary"><i class="fa-solid fa-user me-2"></i>Información
                            Personal</h5>
                        <small><br>Los campos con <span class="text-danger">*</span> son obligatorios.</small>
                        
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $registro->name ?? '') }}" placeholder="Ingresa tu nombre" required >
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $registro->email ?? '') }}"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" autocomplete="new-password"
                                        {{ isset($registro) ? '' : 'required' }}>
                                </div>
                                @if(isset($registro))<small class="form-text text-muted">Dejar en blanco para no
                                    cambiar.</small>@endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN 2: GESTIÓN DE CUENTA (SOLO PARA ADMINS) --}}
                    @can('user-edit')
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="card-title mb-0 text-primary"><i class="fa-solid fa-user-gear me-2"></i>Gestión de
                            Cuenta</h5>
                        <small><br>Los campos con <span class="text-danger">*</span> son obligatorios.</small>
                    </div>
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Rol del Usuario <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-user-tag"></i></span>
                                    <select name="role" id="role"
                                        class="form-select @error('role') is-invalid @enderror" x-model="selectedRole"
                                        required>
                                        <option value="" disabled>Seleccione un rol</option>
                                        @foreach($roles as $role)
                                        <option value="{{ $role->name }}"
                                            {{ (old('role') == $role->name) || (isset($registro) && $registro->hasRole($role->name) && !old('role')) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if(auth()->user()->hasRole('super_admin'))
                            <div class="col-md-6 mb-3"
                                x-show="['admin', 'vendedor', 'repartidor'].includes(selectedRole)"
                                x-transition.opacity>
                                <label for="empresa_id" class="form-label">Empresa <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-building"></i></span>

                                    <select name="empresa_id" id="empresa_id"
                                        class="form-select @error('empresa_id') is-invalid @enderror"
                                        x-model="empresaOption"
                                        {{-- ESTA ES LA LÍNEA CLAVE: se añade 'disabled' si estamos en modo edición --}}
                                        {{ isset($registro) ? 'disabled' : '' }}>

                                        {{-- MODO CREACIÓN --}}
                                        @if(!isset($registro))
                                        <option value="" disabled selected>Asignar o crear empresa</option>
                                        @foreach($empresas as $empresa)
                                        <option value="{{ $empresa->id }}"
                                            {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
                                            {{ $empresa->nombre }}
                                        </option>
                                        @endforeach
                                        <option value="crear_nueva"
                                            {{ old('empresa_id') == 'crear_nueva' ? 'selected' : '' }}>
                                            Crear Nueva Empresa
                                        </option>

                                        {{-- MODO EDICIÓN --}}
                                        @else
                                        {{-- Mostramos solo la empresa ya asignada --}}
                                        @if($registro->empresa)
                                        <option value="{{ $registro->empresa_id }}" selected>
                                            {{ $registro->empresa->nombre }}
                                        </option>
                                        @else
                                        <option value="" selected disabled>Sin empresa asignada</option>
                                        @endif
                                        @endif
                                    </select>
                                </div>

                                {{-- CAMPO OCULTO para enviar el valor cuando el select está deshabilitado --}}
                                @if(isset($registro) && $registro->empresa_id)
                                <input type="hidden" name="empresa_id" value="{{ $registro->empresa_id }}">
                                @endif
                            </div>
                            @endif
                        </div>

                        {{-- CAMBIO: El bloque de "Estado" solo se muestra al editar --}}
                        @if(isset($registro))
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Estado <span class="text-danger">*</span></label>
                                <div class="form-check form-switch fs-5">
                                    <input class="form-check-input" type="checkbox" role="switch" id="activoSwitch"
                                        name="activo" value="1"
                                        {{ old('activo', $registro->activo ?? 1) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activoSwitch" id="activoLabel"></label>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                    @endcan

                    {{-- SECCIÓN 3: CREAR NUEVA EMPRESA (CONDICIONAL) --}}
                    @if(auth()->user()->hasRole('super_admin') && !isset($registro))
                    <div x-show="empresaOption === 'crear_nueva' && ['admin'].includes(selectedRole)"
                        x-transition.opacity>
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="card-title mb-0 text-info"><i class="fa-solid fa-plus-circle me-2"></i>Datos de
                                la Nueva Empresa</h5>
                            <small><br>Los campos con <span class="text-danger">*</span> son obligatorios.</small>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="empresa_nombre" class="form-label">Nombre Empresa <span class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('empresa_nombre') is-invalid @enderror"
                                        id="empresa_nombre" name="empresa_nombre" value="{{ old('empresa_nombre') }}">
                                    @error('empresa_nombre')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="empresa_rubro" class="form-label">Rubro <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('empresa_rubro') is-invalid @enderror"
                                        id="empresa_rubro" name="empresa_rubro" value="{{ old('empresa_rubro') }}">
                                    @error('empresa_rubro')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="empresa_telefono_whatsapp" class="form-label">Teléfono /
                                        WhatsApp <span class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('empresa_telefono_whatsapp') is-invalid @enderror"
                                        id="empresa_telefono_whatsapp" name="empresa_telefono_whatsapp"
                                        value="{{ old('empresa_telefono_whatsapp') }}">
                                    @error('empresa_telefono_whatsapp')<small
                                        class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- SECCIÓN 4: EDITAR EMPRESA EXISTENTE (CONDICIONAL) --}}
                    @if(auth()->user()->hasRole('super_admin') && isset($empresaDelPerfil))
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="card-title mb-0 text-info"><i class="fa-solid fa-building me-2"></i>Datos de la
                            Empresa Asociada</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="empresa_nombre_edit" class="form-label">Nombre Empresa</label>
                                <input type="text" class="form-control @error('empresa_nombre') is-invalid @enderror"
                                    id="empresa_nombre_edit" name="empresa_nombre"
                                    value="{{ old('empresa_nombre', $empresaDelPerfil->nombre) }}">
                                @error('empresa_nombre')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="empresa_rubro_edit" class="form-label">Rubro</label>
                                <input type="text" class="form-control @error('empresa_rubro') is-invalid @enderror"
                                    id="empresa_rubro_edit" name="empresa_rubro"
                                    value="{{ old('empresa_rubro', $empresaDelPerfil->rubro) }}">
                                @error('empresa_rubro')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="empresa_telefono_whatsapp_edit" class="form-label">Teléfono /
                                    WhatsApp</label>
                                <input type="text"
                                    class="form-control @error('empresa_telefono_whatsapp') is-invalid @enderror"
                                    id="empresa_telefono_whatsapp_edit" name="empresa_telefono_whatsapp"
                                    value="{{ old('empresa_telefono_whatsapp', $empresaDelPerfil->telefono_whatsapp) }}">
                                @error('empresa_telefono_whatsapp')<small
                                    class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="empresa_logo" class="form-label">Logo</label>
                                <input type="file" class="form-control @error('empresa_logo') is-invalid @enderror"
                                    name="empresa_logo">
                                @error('empresa_logo')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="card-footer bg-white text-end border-0 pt-0 pb-4 px-4">
                        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary me-2"><i
                                class="fa-solid fa-xmark me-1"></i> Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i>
                            Guardar</button>
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
                        <img src="{{ asset('assets/img/usuario.gif') }}" class="img-fluid" alt="Gestión de usuarios"
                            style="max-height: 150px;">
                    </div>
                    @can('user-edit')
                    <h6 class="text-muted"><i class="fa-solid fa-key text-warning me-2"></i>Seguridad y Roles</h6>
                    <p class="text-muted small">Asigna el rol correcto para controlar el acceso del usuario. Un rol
                        determina los permisos que puede realizar.</p>
                    <hr>
                    <h6 class="text-muted"><i class="fa-solid fa-tags me-2"></i>Descripción de Roles</h6>
                    <div class="list-group list-group-flush small">
                        @foreach($roles as $role)
                        <div class="list-group-item d-flex justify-content-between align-items-start px-0">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">{{ $role->name }}</div>
                                <span class="text-muted">Acceso a funciones de {{ $role->name }}.</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <h6 class="text-muted"><i class="fa-solid fa-user-shield me-2"></i>Tu Perfil</h6>
                    <p class="text-muted small">Aquí puedes actualizar tus datos personales como tu nombre, correo
                        electrónico y contraseña.</p>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('estilos')
<style>
.form-check-input {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e") !important;
}

.form-check-input:checked {
    background-color: #198754 !important;
    border-color: #198754 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e") !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const activoSwitch = document.getElementById('activoSwitch');
    const activoLabel = document.getElementById('activoLabel');
    if (activoSwitch && activoLabel) {
        function actualizarEstadoLabel() {
            activoLabel.textContent = activoSwitch.checked ? 'Activo' : 'Inactivo';
            activoLabel.className = 'form-check-label ' + (activoSwitch.checked ? 'text-success' :
                'text-danger');
        }
        actualizarEstadoLabel();
        activoSwitch.addEventListener('change', actualizarEstadoLabel);
    }
    const mnuSeguridad = document.getElementById('mnuSeguridad');
    const itemUsuario = document.getElementById('itemUsuario');
    if (mnuSeguridad && itemUsuario) {
        mnuSeguridad.classList.add('menu-open');
        itemUsuario.classList.add('active');
    }
});
</script>
@endpush