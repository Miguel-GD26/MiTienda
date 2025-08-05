{{-- resources/views/livewire/user-management.blade.php --}}
<div>
    <div class="container-fluid mt-4">
        {{-- SECCIÓN 1: CABECERA Y BÚSQUEDA --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0">Listado de Usuarios</h2>
            @can('user-create')
                {{-- Botón para abrir el modal de creación --}}
                <button wire:click="openModal()" class="btn btn-success shadow-sm">
                    <i class="fa-solid fa-user-plus me-1"></i> Nuevo Usuario
                </button>
            @endcan
        </div>

        {{-- Alertas de Livewire --}}
        @if (session()->has('mensaje'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                {{ session('mensaje') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-xmark me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Tarjeta de Contenido: Búsqueda y Tabla --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <div class="input-group">
                    {{-- Input de búsqueda bindeado a la propiedad $search --}}
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o email...">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        {{-- ... Tu thead es idéntico ... --}}
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
                                        <span class="badge bg-info-subtle text-info-emphasis fw-normal">{{ $reg->empresa->nombre ?? 'Sin empresa' }}</span>
                                    </td>
                                @endif
                                <td>
                                    @forelse ($reg->roles as $role)
                                        <span class="badge rounded-pill bg-primary fw-normal">{{ $role->name }}</span>
                                    @empty
                                        <span class="badge rounded-pill bg-secondary fw-normal">Sin rol</span>
                                    @endforelse
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $reg->activo ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger' }}">
                                        {{ $reg->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('user-edit', $reg)
                                            {{-- Botón para abrir el modal de edición --}}
                                            <button wire:click="openModal({{ $reg->id }})" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Editar">
                                                <i class="fa-solid fa-pencil"></i>
                                            </button>
                                        @endcan
                                        
                                        @can('user-activate', $reg)
                                            {{-- Botón para abrir el modal de confirmación de activar/desactivar --}}
                                            <button wire:click="openConfirmModal('toggle', {{ $reg->id }})" class="btn btn-sm {{ $reg->activo ? 'btn-outline-warning' : 'btn-outline-success' }}" data-bs-toggle="tooltip" title="{{ $reg->activo ? 'Desactivar' : 'Activar' }}">
                                                <i class="fa-solid {{ $reg->activo ? 'fa-ban' : 'fa-circle-check' }}"></i>
                                            </button>
                                        @endcan

                                        @can('user-delete', $reg)
                                            {{-- Botón para abrir el modal de confirmación de eliminar --}}
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
                                        <i class="fa-solid fa-users-slash fa-3x text-muted mb-3"></i>
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
            <div class="card-footer bg-white border-0">
                {{ $registros->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- MODAL DE CREACIÓN / EDICIÓN --}}
    @if($showModal)
    <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditMode ? 'Editar Usuario' : 'Crear Usuario' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    {{-- Formulario bindeado con wire:submit.prevent --}}
                    <form wire:submit.prevent="saveUser">
                        
                        {{-- SECCIÓN 1: INFORMACIÓN PERSONAL --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model.defer="name" id="name">
                                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model.defer="email" id="email">
                                @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Contraseña @if(!$isEditMode)<span class="text-danger">*</span>@endif</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model.defer="password" id="password" autocomplete="new-password">
                                @if($isEditMode)<small class="form-text text-muted">Dejar en blanco para no cambiar.</small>@endif
                                @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña @if(!$isEditMode)<span class="text-danger">*</span>@endif</label>
                                <input type="password" class="form-control" wire:model.defer="password_confirmation" id="password_confirmation">
                            </div>
                        </div>

                        {{-- SECCIÓN 2: GESTIÓN DE CUENTA --}}
                        @can('user-edit')
                        <hr>
                        <h5>Gestión de Cuenta</h5>
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Rol del Usuario <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" wire:model.live="role" id="role">
                                    <option value="">Seleccione un rol</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->name }}">{{ $r->name }}</option>
                                    @endforeach
                                </select>
                                @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            
                            @if(auth()->user()->hasRole('super_admin'))
                                @if(in_array($role, ['admin', 'vendedor', 'repartidor']))
                                    <div class="col-md-6 mb-3">
                                        <label for="empresaOption" class="form-label">Empresa <span class="text-danger">*</span></label>
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
                                        @error('empresaOption') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        @error('empresa_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                            @endif
                        </div>

                        @if($isEditMode)
                        <div class="row"
                            x-data="{ activo: @entangle('activo') }"> 
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Estado <span class="text-danger">*</span></label>
                                <div class="form-check form-switch fs-5">
                                    <input class="form-check-input" type="checkbox" role="switch" id="activoSwitch" 
                                        x-model="activo"
                                        wire:model.live="activo">

                                    <label class="form-check-label" 
                                        :class="activo ? 'text-success' : 'text-danger'" 
                                        for="activoSwitch"
                                        x-text="activo ? 'Activo' : 'Inactivo'">
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endcan

                        {{-- SECCIÓN 3: CREAR NUEVA EMPRESA (CONDICIONAL) --}}
                        @if(!$isEditMode && auth()->user()->hasRole('super_admin') && $empresaOption === 'crear_nueva')
                        <div class="p-3 mb-3 bg-light rounded border">
                             <h5>Datos de la Nueva Empresa</h5>
                             <div class="row">
                                 <div class="col-md-6 mb-3">
                                     <label for="empresa_nombre" class="form-label">Nombre Empresa <span class="text-danger">*</span></label>
                                     <input type="text" class="form-control @error('empresa_nombre') is-invalid @enderror" wire:model.defer="empresa_nombre" id="empresa_nombre">
                                     @error('empresa_nombre')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                 </div>
                                 <div class="col-md-6 mb-3">
                                     <label for="empresa_rubro" class="form-label">Rubro</label>
                                     <input type="text" class="form-control @error('empresa_rubro') is-invalid @enderror" wire:model.defer="empresa_rubro" id="empresa_rubro">
                                     @error('empresa_rubro')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                 </div>
                                 <div class="col-md-6 mb-3">
                                     <label for="empresa_telefono_whatsapp" class="form-label">Teléfono / WhatsApp</label>
                                     <input type="text" class="form-control @error('empresa_telefono_whatsapp') is-invalid @enderror" wire:model.defer="empresa_telefono_whatsapp" id="empresa_telefono_whatsapp">
                                     @error('empresa_telefono_whatsapp')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                 </div>
                             </div>
                         </div>
                        @endif

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="saveUser">Guardar</span>
                                <span wire:loading wire:target="saveUser">Guardando...</span>
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