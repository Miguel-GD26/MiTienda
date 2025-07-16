@extends('plantilla.app')

@section('titulo', isset($registro) ? 'Editar Rol' : 'Crear Rol')

@section('contenido')
<div class="container-fluid mt-4"
     x-data="permissionManager({ 
        permissions: {{ json_encode($permissions->pluck('name')) }}, 
        assignedPermissions: {{ json_encode(isset($registro) ? $registro->permissions->pluck('name') : []) }} 
     })">
    
    <h2 class="h3 mb-4">
        <i class="fa-solid {{ isset($registro) ? 'fa-user-shield' : 'fa-plus-square' }} me-2"></i>
        {{ isset($registro) ? 'Editar Rol' : 'Crear Rol' }}
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
        {{-- COLUMNA DEL FORMULARIO PRINCIPAL --}}
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <form action="{{ isset($registro) ? route('roles.update', $registro->id) : route('roles.store') }}" method="POST">
                    @csrf
                    @if(isset($registro))
                        @method('PUT')
                    @endif

                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="card-title mb-0 text-primary"><i class="fa-solid fa-tags me-2"></i>Información del Rol</h5>
                        <small><br>Los campos con <span class="text-danger">*</span> son obligatorios.</small>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del Rol <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $registro->name ?? '') }}" 
                                    placeholder="Ej: vendedor, repartidor, etc." required>
                            </div>
                            <small class="text-muted">El nombre debe ser único y en minúsculas (ej: `nombre_rol`).</small>
                            @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                             <h5 class="card-title mb-0 text-primary"><i class="fa-solid fa-key me-2"></i>Asignar Permisos <span class="text-danger">*</span></h5> 
                             <div class="form-check">
                                <input class="form-check-input" type="checkbox" @click="toggleAll" :checked="allSelected">
                                <label class="form-check-label">
                                    Seleccionar Todos
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            {{-- Bucle para renderizar permisos agrupados --}}
                            <template x-for="(group, groupName) in groupedPermissions" :key="groupName">
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <h6 class="text-muted border-bottom pb-2 mb-2" x-text="formatGroupName(groupName)"></h6>
                                    <template x-for="permission in group" :key="permission">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                   :value="permission" :id="'permiso_' + permission"
                                                   x-model="selectedPermissions">
                                            <label class="form-check-label" :for="'permiso_' + permission" x-text="formatPermissionName(permission)"></label>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white text-end border-0 pt-0 pb-4 px-4">
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary me-2"><i class="fa-solid fa-xmark me-1"></i> Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Guardar Rol</button>
                    </div>
                </form>
            </div>
        </div>
        
        {{-- COLUMNA DE AYUDA --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-circle-info me-2"></i> Panel de Ayuda</h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <img src="{{ asset('assets/img/roles.gif') }}" class="img-fluid" alt="Gestión de roles y permisos" style="max-height: 150px;">
                    </div>
                    
                    <h6 class="text-muted"><i class="fa-solid fa-shield-halved text-success me-2"></i>¿Qué es un Rol?</h6>
                    <p class="text-muted small">
                        Un rol es un conjunto de permisos que puedes asignar a múltiples usuarios. En lugar de dar permisos uno por uno, asignas un rol (como "Vendedor") y el usuario hereda automáticamente todos sus permisos.
                    </p>
                    <hr>
                    <h6 class="text-muted"><i class="fa-solid fa-lightbulb text-warning me-2"></i>Consejo</h6>
                    <p class="text-muted small">
                        Agrupa los permisos por funcionalidad (ej: `producto-list`, `producto-create`). Esto hace que la gestión de roles sea mucho más clara y mantenible a largo plazo.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Lógica de Alpine.js para gestionar los permisos
function permissionManager(config) {
    return {
        allPermissions: config.permissions,
        selectedPermissions: config.assignedPermissions,
        
        get groupedPermissions() {
            return this.allPermissions.reduce((groups, permission) => {
                const groupName = permission.split('-')[0];
                if (!groups[groupName]) {
                    groups[groupName] = [];
                }
                groups[groupName].push(permission);
                return groups;
            }, {});
        },

        get allSelected() {
            return this.allPermissions.length > 0 && this.selectedPermissions.length === this.allPermissions.length;
        },

        toggleAll() {
            if (this.allSelected) {
                this.selectedPermissions = [];
            } else {
                this.selectedPermissions = [...this.allPermissions];
            }
        },

        formatGroupName(name) {
            return name.charAt(0).toUpperCase() + name.slice(1).replace('_', ' ');
        },
        
        formatPermissionName(name) {
            return name.split('-').slice(1).join(' ').replace(/\b\w/g, l => l.toUpperCase());
        }
    }
}

// Lógica para el menú activo
document.getElementById('mnuSeguridad').classList.add('menu-open');
document.getElementById('itemRole').classList.add('active');
</script>
@endpush