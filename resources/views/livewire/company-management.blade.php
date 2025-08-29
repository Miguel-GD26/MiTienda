<div>
    {{-- SECCIÓN 1: CABECERA Y BOTÓN DE CREACIÓN --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-building me-2"></i>
            Gestión de Empresas
        </h2>
        <button wire:click="openModal()" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i>
            Crear Nueva Empresa
        </button>
    </div>

    {{-- SECCIÓN 2: TARJETA DE CONTENIDO --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
            <div class="material-form-group-with-icon">
                <i class="fas fa-search fa-fw form-icon"></i>
                <input id="companySearch" type="text" wire:model.live.debounce.500ms="search" class="material-form-control-with-icon" 
                       placeholder=" " autocomplete="off" />
                <label for="companySearch" class="material-form-label">Buscar por nombre o slug...</label>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">ID</th>
                            <th>Nombre</th>
                            <th>Slug (URL)</th>
                            <th>Teléfono</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($empresas as $empresa)
                        <tr class="align-middle">
                            <td class="text-center">{{ $empresa->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    {{-- CAMBIO: Mostrar logo real o fallback --}}
                                    @if ($empresa->logo_url)
                                        <img src="{{ cloudinary()->image($empresa->logo_url)->toUrl() }}" alt="Logo de {{ $empresa->nombre }}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover; filter: drop-shadow(0 0 1px black); background-color: transparent;">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($empresa->nombre) }}&background=random&color=fff&size=32" alt="Logo" class="rounded-circle me-2">
                                    @endif
                                    <span>{{ $empresa->nombre }}</span>
                                </div>
                            </td>
                            <td>{{ $empresa->slug }}</td>
                            <td>{{ $empresa->telefono_whatsapp ?? 'N/A' }}</td>
                            <td class="text-center">
                                <button wire:click="openModal({{ $empresa->id }})" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fa-solid fa-pencil-alt"></i>
                                </button>
                                <button wire:click="openConfirmModal({{ $empresa->id }})" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                    <i class="fa-solid fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="text-center p-5">
                                    <i class="fa-solid fa-building-circle-exclamation fa-3x text-secondary mb-3"></i>
                                    <p class="mb-0 text-muted">No se encontraron empresas.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($empresas->hasPages())
        <div class="card-footer bg-light border-top">
            {{ $empresas->links() }}
        </div>
        @endif
    </div>

    {{-- SECCIÓN 3: MODAL DE CREACIÓN/EDICIÓN MEJORADO --}}
    @if($showModal)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="saveCompany">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEditMode ? 'Editar Empresa' : 'Crear Nueva Empresa' }}</h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="material-form-group-with-icon mb-3">
                            <i class="fa-solid fa-building form-icon"></i>
                            <input type="text" id="nombre" wire:model.defer="nombre" class="material-form-control-with-icon @error('nombre') is-invalid @enderror" placeholder=" ">
                            <label for="nombre" class="material-form-label">Nombre de la Empresa</label>
                        </div>
                        @error('nombre') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror

                        <div class="material-form-group-with-icon mb-3">
                            <i class="fa-solid fa-briefcase form-icon"></i>
                            <input type="text" id="rubro" wire:model.defer="rubro" class="material-form-control-with-icon @error('rubro') is-invalid @enderror" placeholder=" ">
                            <label for="rubro" class="material-form-label">Rubro</label>
                        </div>
                        @error('rubro') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
                        
                        <div class="material-form-group-with-icon mb-3">
                            <i class="fa-brands fa-whatsapp form-icon"></i>
                            <input type="text" id="telefono_whatsapp" wire:model.defer="telefono_whatsapp" class="material-form-control-with-icon @error('telefono_whatsapp') is-invalid @enderror" placeholder=" ">
                            <label for="telefono_whatsapp" class="material-form-label">Teléfono / WhatsApp</label>
                        </div>
                        @error('telefono_whatsapp') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror

                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3">
                                <label for="logo" class="form-label">Logo de la Empresa</label>
                                <input type="file" class="form-control" id="logo" wire:model="logo">
                                @error('logo') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3 text-center">
                                <div wire:loading wire:target="logo" class="text-primary small">Subiendo...</div>
                                @if ($logo)
                                    <img src="{{ $logo->temporaryUrl() }}" class="img-thumbnail" style="max-width: 100px;filter: drop-shadow(0 0 2px black); background-color: transparent;">
                                @elseif ($existingLogoUrl)
                                    <img src="{{ $existingLogoUrl }}" class="img-thumbnail" style="max-width: 100px; filter: drop-shadow(0 0 2px black); background-color: transparent;">
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading.remove wire:target="saveCompany">{{ $isEditMode ? 'Actualizar' : 'Guardar' }}</span>
                            <span wire:loading wire:target="saveCompany">Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif

    {{-- SECCIÓN 4: MODAL DE CONFIRMACIÓN DE BORRADO (sin cambios de estilo) --}}
    @if($showConfirmModal)
     <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" wire:click="closeConfirmModal" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que quieres eliminar la empresa <strong>"{{ $companyToDelete->nombre ?? '' }}"</strong>? Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="closeConfirmModal" class="btn btn-secondary">Cancelar</button>
                    <button type="button" wire:click="deleteCompany" class="btn btn-danger">
                        <span wire:loading.remove wire:target="deleteCompany">Sí, Eliminar</span>
                        <span wire:loading wire:target="deleteCompany">Eliminando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>