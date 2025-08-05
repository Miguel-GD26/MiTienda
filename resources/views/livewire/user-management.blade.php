<div>
    <div class="container-fluid mt-4">
        {{-- SECCIÓN 1: CABECERA Y BÚSQUEDA --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0">Listado de Usuarios</h2>
            @can('user-create')
                <button wire:click="openModal()" class="btn btn-success shadow-sm">
                    <i class="fa-solid fa-user-plus me-1"></i> Nuevo Usuario
                </button>
            @endcan
        </div>

        {{-- Tarjeta de Contenido: Búsqueda y Tabla --}}
        <div class="card shadow-sm border-0">
            <div class="card-header">
                <div class="input-group">
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o email...">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                @if(auth()->user()->hasRole('super_admin'))
                                    <th>Empresa</th>
                                @endif
                                <th>Rol</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center" style="width: 180px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($registros as $reg)
                            <tr class="align-middle">
                                <td class="text-center">{{ $reg->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($reg->name) }}&background=random&color=fff" alt="" class="rounded-circle me-2" width="32" height="32">
                                        <span>{{ $reg->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $reg->email }}</td>
                                @if(auth()->user()->hasRole('super_admin'))
                                    <td>
                                        <span class="badge text-bg-info fw-normal">{{ $reg->empresa->nombre ?? 'Sin empresa' }}</span>
                                    </td>
                                @endif
                                <td>
                                    @forelse ($reg->roles as $role)
                                        <span class="badge rounded-pill text-bg-primary fw-normal">{{ $role->name }}</span>
                                    @empty
                                        <span class="badge rounded-pill text-bg-secondary fw-normal">Sin rol</span>
                                    @endforelse
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $reg->activo ? 'text-bg-success' : 'text-bg-danger' }}">
                                        {{ $reg->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('user-edit', $reg)
                                            <button wire:click="openModal({{ $reg->id }})" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Editar">
                                                <i class="fa-solid fa-pencil"></i>
                                            </button>
                                        @endcan
                                        
                                        @can('user-activate', $reg)
                                            <button wire:click="openConfirmModal('toggle', {{ $reg->id }})" class="btn btn-sm {{ $reg->activo ? 'btn-outline-warning' : 'btn-outline-success' }}" data-bs-toggle="tooltip" title="{{ $reg->activo ? 'Desactivar' : 'Activar' }}">
                                                <i class="fa-solid {{ $reg->activo ? 'fa-ban' : 'fa-circle-check' }}"></i>
                                            </button>
                                        @endcan

                                        @can('user-delete', $reg)
                                            <button wire:click="openConfirmModal('delete', {{ $reg->id }})" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Eliminar">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('super_admin') ? '7' : '6' }}">
                                    <div class="text-center p-5">
                                        <i class="fa-solid fa-users-slash fa-3x text-secondary mb-3"></i>
                                        <p class="mb-0 text-muted">No se encontraron usuarios.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($registros->hasPages())
            <div class="card-footer">
                {{ $registros->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- MODAL DE CREACIÓN / EDICIÓN --}}
    @if($showModal)
    <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog" x-data @keydown.escape.window="$wire.closeModal()">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid {{ $isEditMode ? 'fa-user-pen' : 'fa-user-plus' }} me-2"></i>
                        {{ $isEditMode ? 'Editar Usuario' : 'Crear Usuario' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveUser" wire:loading.class="pe-none opacity-50" novalidate>
                        
                        <h6 class="mb-3 text-primary border-bottom pb-2"><i class="fa-solid fa-id-card me-2"></i>Información Personal</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model.defer="name" id="name" placeholder="Ej: Juan Pérez">
                                </div>
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model.defer="email" id="email" placeholder="ejemplo@correo.com">
                                </div>
                                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row" 
                            x-data="{ 
                                showPassword: false, 
                                showConfirmPassword: false 
                            }">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Contraseña @if(!$isEditMode)<span class="text-danger">*</span>@endif</label>
                                {{-- Grupo de input para la contraseña --}}
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                    
                                    {{-- El tipo de input cambia dinámicamente con AlpineJS --}}
                                    <input :type="showPassword ? 'text' : 'password'" 
                                        class="form-control @error('password') is-invalid @enderror" 
                                        wire:model.defer="password" 
                                        id="password" 
                                        autocomplete="new-password">

                                    {{-- Botón para alternar la visibilidad --}}
                                    <button class="btn btn-outline-secondary" type="button" @click="showPassword = !showPassword">
                                        {{-- El icono cambia dinámicamente --}}
                                        <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                                @if($isEditMode)<small class="form-text text-muted">Dejar en blanco para no cambiar.</small>@endif
                                @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña @if(!$isEditMode)<span class="text-danger">*</span>@endif</label>
                                {{-- Grupo de input para confirmar la contraseña --}}
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>

                                    {{-- El tipo de input cambia dinámicamente con AlpineJS --}}
                                    <input :type="showConfirmPassword ? 'text' : 'password'" 
                                        class="form-control" 
                                        wire:model.defer="password_confirmation" 
                                        id="password_confirmation">
                                    
                                    {{-- Botón para alternar la visibilidad --}}
                                    <button class="btn btn-outline-secondary" type="button" @click="showConfirmPassword = !showConfirmPassword">
                                        {{-- El icono cambia dinámicamente --}}
                                        <i class="fa-solid" :class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        @can('user-edit')
                        <h6 class="mt-3 mb-3 text-primary border-bottom pb-2"><i class="fa-solid fa-user-gear me-2"></i>Gestión de Cuenta</h6>
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Rol del Usuario <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-user-tag"></i></span>
                                    <select class="form-select @error('role') is-invalid @enderror" wire:model.live="role" id="role">
                                        <option value="" disabled>Seleccione un rol</option>
                                        @foreach($roles as $r)
                                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('role') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            
                            @if(auth()->user()->hasRole('super_admin') && in_array($role, ['admin', 'vendedor', 'repartidor']))
                                <div class="col-md-6 mb-3">
                                    <label for="empresaOption" class="form-label">Empresa <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-building"></i></span>
                                        <select class="form-select @error('empresaOption') is-invalid @enderror @error('empresa_id') is-invalid @enderror" wire:model.live="empresaOption" id="empresaOption" @if($isEditMode) disabled @endif>
                                            @if(!$isEditMode)
                                                <option value="">Asignar o crear empresa</option>
                                                @foreach($empresas as $empresa)
                                                    <option value="{{ $empresa->id }}">{{ $empresa->nombre }}</option>
                                                @endforeach
                                                <option value="crear_nueva">Crear Nueva Empresa</option>
                                            @else
                                                <option value="{{ $empresa_id }}" selected>{{ \App\Models\Empresa::find($empresa_id)->nombre ?? 'Sin empresa' }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    @error('empresaOption') <span class="text-danger small">{{ $message }}</span> @enderror
                                    @error('empresa_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        </div>
                        
                        @if($isEditMode)
                        <div class="row" x-data="{ activo: @entangle('activo') }"> 
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Estado</label>
                                 <div class="form-check form-switch fs-5">
                                    <input class="form-check-input" type="checkbox" role="switch" id="activoSwitch" x-model="activo" wire:model.live="activo">
                                    <label class="form-check-label" :class="activo ? 'text-success' : 'text-danger'" for="activoSwitch" x-text="activo ? 'Activo' : 'Inactivo'"></label>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endcan
                        
                        @if(!$isEditMode && auth()->user()->hasRole('super_admin') && $empresaOption === 'crear_nueva')
                        <div x-data="{ show: false }" x-init="() => { setTimeout(() => show = true, 50) }" x-show="show" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform -translate-y-4"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             class="p-3 my-3 bg-light rounded border">
                            <h6 class="text-info"><i class="fa-solid fa-plus-circle me-2"></i>Datos de la Nueva Empresa</h6>
                            <div class="row">
                                 <div class="col-md-6 mb-3">
                                     <label for="empresa_nombre" class="form-label">Nombre Empresa <span class="text-danger">*</span></label>
                                     <input type="text" class="form-control @error('empresa_nombre') is-invalid @enderror" wire:model.defer="empresa_nombre" id="empresa_nombre">
                                     @error('empresa_nombre')<span class="text-danger small">{{ $message }}</span>@enderror
                                 </div>
                                 <div class="col-md-6 mb-3">
                                     <label for="empresa_rubro" class="form-label">Rubro</label>
                                     <input type="text" class="form-control @error('empresa_rubro') is-invalid @enderror" wire:model.defer="empresa_rubro" id="empresa_rubro">
                                     @error('empresa_rubro')<span class="text-danger small">{{ $message }}</span>@enderror
                                 </div>
                                 <div class="col-md-6 mb-3">
                                     <label for="empresa_telefono_whatsapp" class="form-label">Teléfono / WhatsApp</label>
                                     <input type="text" class="form-control @error('empresa_telefono_whatsapp') is-invalid @enderror" wire:model.defer="empresa_telefono_whatsapp" id="empresa_telefono_whatsapp">
                                     @error('empresa_telefono_whatsapp')<span class="text-danger small">{{ $message }}</span>@enderror
                                 </div>
                             </div>
                         </div>
                        @endif

                        <div class="modal-footer mt-4 border-top pt-3">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal"><i class="fa-solid fa-xmark me-1"></i> Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="saveUser">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Guardar
                                </span>
                                <span wire:loading wire:target="saveUser">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Guardando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="display: @if($showModal) block @else none @endif;"></div>
    @endif

    {{-- MODAL DE CONFIRMACIÓN --}}
    @if($showConfirmModal)
    <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Acción</h5>
                    <button type="button" class="btn-close" wire:click="closeConfirmModal"></button>
                </div>
                <div class="modal-body">
                    @if($userToDeleteOrToggle)
                        @if($confirmModalType === 'delete')
                            ¿Estás seguro de que quieres eliminar a <strong>{{ $userToDeleteOrToggle->name }}</strong>? Esta acción no se puede deshacer.
                        @else
                            ¿Estás seguro de que quieres {{ $userToDeleteOrToggle->activo ? 'desactivar' : 'activar' }} a <strong>{{ $userToDeleteOrToggle->name }}</strong>?
                        @endif
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeConfirmModal">Cancelar</button>
                    <button type="button" class="btn btn-{{ $confirmModalType === 'delete' ? 'danger' : 'warning' }}" wire:click="confirmAction">
                        <span wire:loading.remove wire:target="confirmAction">Confirmar</span>
                        <span wire:loading wire:target="confirmAction">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="display: @if($showConfirmModal) block @else none @endif;"></div>
    @endif
</div>