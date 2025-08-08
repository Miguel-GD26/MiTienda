<div>
    <div class="container-fluid mt-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">
                <i class="fa-solid fa-user-shield me-2"></i>
                Gestión de Roles y Permisos
            </h2>
            @can('rol-create')
                <button wire:click="openModal()" class="btn btn-success shadow-sm">
                    <i class="fa-solid fa-plus me-1"></i> Nuevo Rol
                </button>
            @endcan
        </div>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o email...">
                    <span class="input-group-text" wire:loading wire:target="search">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </span>
                </div>
            </div>
        </div>

        <div wire:loading.class="opacity-50" class="row">
            @forelse($registros as $reg)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm border-start-primary">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title text-primary">{{ $reg->name }} </h5>
                                    <br>
                                    <small class="text-muted">ID: {{ $reg->id }} | {{ $reg->permissions->count() }} permisos</small>
                                </div>
                                <div class="btn-group">
                                    @can('rol-edit')
                                        <button wire:click="openModal({{ $reg->id }})" class="btn btn-sm btn-outline-info" title="Editar">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>
                                    @endcan
                                    @can('rol-delete')
                                        @if($reg->name != 'super_admin')
                                        <button wire:click="openConfirmModal({{ $reg->id }})" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                            <hr>

                            @if($reg->permissions->isNotEmpty())
                                @php
                                    $groupedPermissions = $reg->permissions->groupBy(function($item) {
                                        return explode('-', $item->name)[0];
                                    });
                                @endphp
                                
                                @foreach($groupedPermissions as $group => $permissions)
                                <div class="mb-2">
                                    <strong class="text-muted text-capitalize">{{ str_replace('_', ' ', $group) }}:</strong>
                                    <div class="d-flex flex-wrap mt-1">
                                        @foreach($permissions as $permission)
                                            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill me-1 mb-1 fw-normal">
                                                {{ Str::title(str_replace('-', ' ', explode('-', $permission->name, 2)[1] ?? $permission->name)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted p-3">
                                    <i class="fa-solid fa-key-skeleton d-block mb-2 fs-4"></i>
                                    <span>Sin permisos asignados</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center p-5 bg-white rounded shadow-sm">
                        <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="mb-0 text-muted">No se encontraron roles.</p>
                    </div>
                </div>
            @endforelse
        </div>
        
        @if ($registros->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $registros->links() }}
        </div>
        @endif
    </div>

    @if($showModal)
    <div class="modal fade show" style="display: block;" tabindex="-1" x-data @keydown.escape.window="$wire.closeModal()">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                {{-- 
                    CORRECCIÓN 1: El x-data se mueve aquí, al <form>, para que todo su contenido,
                    incluido el footer con el botón, esté dentro de su alcance.
                --}}
                <form wire:key="modal-role-{{ $roleId ?? 'new' }}"
                      x-data="{
                        allPermissions: {{ json_encode($allPermissions->pluck('name')) }},
                        selectedPermissions: {{ json_encode($selectedPermissions) }},

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
                        get groupedPermissions() {
                            return this.allPermissions.reduce((groups, permission) => {
                                const groupName = permission.split('-')[0];
                                if (!groups[groupName]) {
                                    groups[groupName] = [];
                                }
                                groups[groupName].push(permission);
                                return groups;
                            }, {});
                        }
                     }"
                     wire:loading.class.delay="pe-none opacity-50">
                      
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa-solid {{ $isEditMode ? 'fa-user-shield' : 'fa-plus-square' }} me-2"></i>
                            {{ $isEditMode ? 'Editar Rol' : 'Crear Rol' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    
                    {{-- CORRECCIÓN 2: El modal-body ya no necesita el x-data --}}
                    <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
                        
                        <div class="mb-4">
                            <label for="name" class="form-label">Nombre del Rol <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-tag"></i></span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    wire:model.defer="name" id="name" placeholder="Ej: vendedor, repartidor, etc." required
                                    @if($name === 'super_admin') readonly @endif>
                            </div>
                            <small class="text-muted">El nombre debe ser único y en minúsculas.</small>
                            @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title text-primary"><i class="fa-solid fa-key me-2"></i>Asignar Permisos</h5> 
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAllCheckbox" 
                                       :checked="allSelected" 
                                       @click="toggleAll()">
                                <label class="form-check-label" for="selectAllCheckbox">Seleccionar Todos</label>
                            </div>
                        </div>

                        <div class="row">
                            <template x-for="(permissionsInGroup, groupName) in groupedPermissions" :key="groupName">
                                <div class="col-md-6 col-lg-3 mb-4">
                                    <h6 class="text-muted border-bottom pb-2 mb-2 text-capitalize" x-text="groupName.replace('_', ' ')"></h6>
                                    <template x-for="permission in permissionsInGroup" :key="permission">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   :value="permission" 
                                                   :id="'permiso_' + permission.replace(/[^a-zA-Z0-9]/g, '_')"
                                                   x-model="selectedPermissions">
                                            <label class="form-check-label" :for="'permiso_' + permission.replace(/[^a-zA-Z0-9]/g, '_')" 
                                                   x-text="(permission.split('-')[1] || permission).replace(/\b\w/g, l => l.toUpperCase())">
                                            </label>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancelar</button>
                        
                        {{-- CORRECCIÓN 3: El botón vuelve a ser `type="button"` con un `@click` que llama al método con los datos --}}
                        <button type="button" class="btn btn-primary"
                                @click="$wire.saveRole(selectedPermissions)">
                            <span wire:loading.remove wire:target="saveRole"><i class="fa-solid fa-floppy-disk me-1"></i> Guardar Rol</span>
                            <span wire:loading wire:target="saveRole" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="display: @if($showModal) block @else none @endif;"></div>
    @endif

    {{-- MODAL DE CONFIRMACIÓN --}}
    @if($showConfirmModal)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Rol</h5>
                    <button type="button" class="btn-close" wire:click="closeConfirmModal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que quieres eliminar el rol <strong>{{ $roleToDelete->name ?? '' }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeConfirmModal">Cancelar</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteRole">
                         <span wire:loading.remove wire:target="deleteRole">Eliminar</span>
                         <span wire:loading wire:target="deleteRole" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="display: @if($showConfirmModal) block @else none @endif;"></div>
    @endif
</div>