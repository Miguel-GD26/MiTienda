<div>
    <div class="container-fluid mt-4">
        {{-- Cabecera --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800"><i class="fa-solid fa-key me-2"></i>Gestión de Permisos</h2>
            @can('permission-create')
            <button wire:click="openModal()" class="btn btn-success shadow-sm">
                <i class="fa-solid fa-plus me-1"></i>Nuevo Permiso
            </button>
            @endcan
        </div>

        {{-- Búsqueda --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <div class="material-form-group-with-icon">
                    <i class="fas fa-search fa-fw form-icon"></i>
                    <input id="searchProduct" type="text" wire:model.live.debounce.300ms="search"
                        class="material-form-control-with-icon" placeholder=" " autocomplete="off" />
                    <label for="searchProduct" class="material-form-label">Buscar permiso por nombre....</label>

                    <div class="spinner-container" wire:loading wire:target="search">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjetas de Permisos --}}
        <div wire:loading.class="opacity-50" class="row">
            @forelse($registros as $permiso)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100 shadow-sm text-center card-permission">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-circle bg-primary-subtle text-primary mx-auto mb-3"><i
                                    class="fa-solid fa-shield-halved"></i></div>
                            <!-- <h5 class="card-title">{{ explode('-', $permiso->name, 2)[1] ?? $permiso->name }}</h5> -->
                            <p class="card-text text-muted"><span
                                    class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">{{ explode('-', $permiso->name)[0] }}</span>
                            </p>
                            <small class="text-body-secondary d-block mt-2"><code
                                    class="text-primary">{{ $permiso->name }}</code></small>
                        </div>
                        <div class="mt-4">
                            <div class="btn-group">
                                @can('permission-edit')<button wire:click="openModal({{ $permiso->id }})"
                                    class="btn btn-sm btn-outline-info" title="Editar"><i
                                        class="fa-solid fa-pencil"></i></button>@endcan
                                @can('permission-delete')<button wire:click="openConfirmModal({{ $permiso->id }})"
                                    class="btn btn-sm btn-outline-danger" title="Eliminar"><i
                                        class="fa-solid fa-trash-can"></i></button>@endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center p-5 bg-white rounded shadow-sm">
                    <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="mb-0 text-muted">No se encontraron permisos.</p>
                </div>
            </div>
            @endforelse
        </div>

        @if($registros->hasPages())
            <div class="mt-2 w-100 text-end">
                {{ $registros->links() }}
            </div>
        @endif

    </div>

    {{-- MODAL DE CREACIÓN / EDICIÓN --}}
    @if($showModal)
    <div class="modal fade show" style="display: block;" tabindex="-1" x-data
        @keydown.escape.window="$wire.closeModal()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="savePermission" wire:loading.class.delay="pe-none opacity-50">
                    <div class="modal-header">
                        <h5 class="modal-title"><i
                                class="fa-solid {{ $isEditMode ? 'fa-key' : 'fa-plus-square' }} me-2"></i>{{ $isEditMode ? 'Editar Permiso' : 'Crear Permiso' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="material-form-group-with-icon">
                            <i class="fas fa-hashtag fa-fw form-icon"></i>
                            <input id="name" type="text" wire:model="name"
                                class="material-form-control-with-icon @error('name') is-invalid @enderror"
                                placeholder=" ">
                            <label for="name" class="material-form-label">Nombre del Permiso (Clave) <span
                                    class="text-danger">*</span></label>
                        </div>
                        <small class="text-muted d-block mt-1">Usa el formato `módulo-acción`, ej:
                            `producto-crear`.</small>
                        @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading.remove wire:target="savePermission"><i
                                    class="fa-solid fa-floppy-disk me-1"></i> Guardar</span>
                            <span wire:loading wire:target="savePermission"
                                class="spinner-border spinner-border-sm"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show {{ $showModal ? 'd-block' : 'd-none' }}"></div>
    @endif

    {{-- MODAL DE CONFIRMACIÓN --}}
    @if($showConfirmModal)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Permiso</h5><button type="button" class="btn-close"
                        wire:click="closeConfirmModal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Realmente desea eliminar el permiso "<strong>{{ $permissionToDelete->name ?? '' }}</strong>"?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeConfirmModal">Cancelar</button>
                    <button type="button" class="btn btn-danger" wire:click="deletePermission">
                        <span wire:loading.remove wire:target="deletePermission">Eliminar</span>
                        <span wire:loading wire:target="deletePermission"
                            class="spinner-border spinner-border-sm"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show {{ $showConfirmModal ? 'd-block' : 'd-none' }}"></div>
    @endif
</div>