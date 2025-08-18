<div>
    <div class="container-fluid mt-4">
        {{-- Cabecera y Filtros --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">
                <i class="fa-solid fa-tags me-2"></i>Listado de Categorías
            </h2>
            @can('categoria-create')
            <button wire:click="openModal()" class="btn btn-success shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nueva Categoría
            </button>
            @endcan
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
                <div class="row g-2">
                    {{-- FILTRO DE EMPRESA (SOLO SUPER ADMIN) --}}
                    @if(auth()->user()->hasRole('super_admin'))
                    <div class="col-12 col-md-6 position-relative" x-data="{ open: true }" @click.away="open = false">
                        @if($selectedEmpresaName)
                        <div class="material-form-group-with-icon is-selected">
                            <i class="fa-solid fa-building fa-fw form-icon"></i>
                            <div class="material-form-control-with-icon selected-value d-flex align-items-center">
                                <span class="text-truncate">{{ $selectedEmpresaName }}</span>
                                <button type="button" wire:click="clearEmpresaFilter"
                                    class="btn-change-selection ms-auto" title="Limpiar filtro">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <label class="material-form-label active">Empresa Filtrada</label>
                        </div>
                        @else
                        <div class="material-form-group-with-icon" @focusin="open = true">
                            <i class="fa-solid fa-building fa-fw form-icon"></i>
                            <input type="text" wire:model.live.debounce.300ms="empresaSearch"
                                @keydown.space.prevent="if($wire.empresaSearch.trim() === '') { $wire.call('listAllEmpresas'); }"
                                id="empresa_search_filter" class="material-form-control-with-icon" placeholder=" "
                                autocomplete="off" />
                            <label for="empresa_search_filter" class="material-form-label">Filtrar por Empresa (Espacio
                                para ver todas)</label>
                        </div>

                        @if($empresas_for_filter_paginator && $empresas_for_filter_paginator->total() > 0)
                        <div x-show="open" class="dropdown-menu d-block position-absolute w-100 shadow-lg mt-1"
                            style="z-index: 100;" x-transition>
                            <div class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">
                                @foreach($empresas_for_filter_paginator as $empresa)
                                <a href="#" wire:key="filter-emp-{{ $empresa->id }}"
                                    wire:click.prevent="selectEmpresaFilter({{ $empresa->id }}, '{{ addslashes($empresa->nombre) }}')"
                                    class="dropdown-item">
                                    {{ $empresa->nombre }}
                                </a>
                                @endforeach
                            </div>
                            @if($empresas_for_filter_paginator->hasPages())
                            <div class="p-2 border-top bg-light d-flex justify-content-center">
                                {{ $empresas_for_filter_paginator->links('livewire::bootstrap') }}
                            </div>
                            @endif
                        </div>
                        @elseif(strlen(trim($empresaSearch)) > 0)
                            <div x-show="open"
                                class="dropdown-menu d-block position-absolute w-100 shadow-lg p-2 text-muted">
                                No se encontraron resultados.
                            </div>     
                        @endif
                        @endif
                    </div>
                    @endif

                    {{-- BÚSQUEDA DE CATEGORÍA --}}
                    <div class="col-12 {{ auth()->user()->hasRole('super_admin') ? 'col-md-6' : 'col-md-12' }}">
                        <div class="material-form-group-with-icon">
                            <i class="fas fa-search fa-fw form-icon"></i>
                            <input id="searchCategory" type="text" wire:model.live.debounce.300ms="search"
                                class="material-form-control-with-icon" placeholder=" " autocomplete="off" />
                            <label for="searchCategory" class="material-form-label">Buscar por nombre de
                                categoría...</label>
                            <div class="spinner-container" wire:loading
                                wire:target="search, empresa_id_filter">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                @if(auth()->user()->hasRole('super_admin'))<th>Empresa</th>@endif
                                <th>Descripción</th>
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($registros as $reg)
                            <tr class="align-middle">
                                <td>
                                    <div class="fw-bold">{{ $reg->nombre }}</div>
                                </td>
                                @if(auth()->user()->hasRole('super_admin'))<td class="small text-muted">
                                    {{ $reg->empresa->nombre ?? 'N/A' }}</td>@endif
                                <td>{{ Str::limit($reg->descripcion, 80) ?: 'Sin descripción' }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('categoria-edit', $reg)<button
                                            wire:click="openModal({{ $reg->id }})"
                                            class="btn btn-sm btn-outline-primary" title="Editar"><i
                                                class="fa-solid fa-pencil"></i></button>@endcan
                                        @can('categoria-delete', $reg)<button
                                            wire:click="openConfirmModal({{ $reg->id }})"
                                            class="btn btn-sm btn-outline-danger" title="Eliminar"><i
                                                class="fa-solid fa-trash-can"></i></button>@endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('super_admin') ? '4' : '3' }}">
                                    <div class="text-center p-5"><i
                                            class="fa-solid fa-folder-open fa-3x text-muted mb-3"></i>
                                        <p class="mb-0 text-muted">No se encontraron categorías.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($registros->hasPages())<div class="card-footer bg-white border-0">{{ $registros->links() }}</div>@endif
        </div>
    </div>

    {{-- MODAL DE CREACIÓN / EDICIÓN --}}
    @if($showModal)
    <div class="modal fade show" style="display: block;" tabindex="-1" x-data @keydown.escape.window="$wire.closeModal()">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="saveCategory">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa-solid {{ $isEditMode ? 'fa-pen-to-square' : 'fa-folder-plus' }} me-2"></i>{{ $isEditMode ? 'Editar Categoría' : 'Nueva Categoría' }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>

                    <div class="modal-body">
                        @if(auth()->user()->hasRole('super_admin'))
                        <div class="mb-4" x-data="{ openModalSearch: false }" @click.away="openModalSearch = false">
                            <div class="position-relative">
                                @if($selectedEmpresaNameInModal)
                                <div class="material-form-group-with-icon is-selected">
                                    <i class="fa-solid fa-building fa-fw form-icon"></i>
                                    <div class="material-form-control-with-icon selected-value d-flex align-items-center">
                                        <span class="text-truncate">{{ $selectedEmpresaNameInModal }}</span>
                                        @if(!$isEditMode)
                                        <button type="button" wire:click="clearSelectedEmpresaInModal" class="btn-change-selection ms-auto" title="Limpiar selección"><i class="fas fa-times"></i></button>
                                        @endif
                                    </div>
                                    <label class="material-form-label active">Empresa</label>
                                </div>
                                @else
                                <div class="material-form-group-with-icon" @focusin="openModalSearch = true">
                                    <i class="fa-solid fa-building fa-fw form-icon"></i>
                                    <input type="text" wire:model.live.debounce.300ms="empresaSearchModal"
                                        @keydown.space.prevent="if($wire.empresaSearchModal.trim() === '') { $wire.call('listAllEmpresasForModal'); }"
                                        id="empresa_search_modal" class="material-form-control-with-icon"
                                        placeholder=" " autocomplete="off" />
                                    <label for="empresa_search_modal" class="material-form-label">Buscar Empresa <span class="text-danger">*</span></label>
                                </div>
                                @if($empresasForModal && $empresasForModal->total() > 0)
                                <div x-show="openModalSearch"
                                    class="dropdown-menu d-block position-absolute w-100 shadow-lg mt-1"
                                    style="z-index: 1060;" x-transition>
                                    <div class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">
                                        @foreach($empresasForModal as $empresa)
                                        <a href="#" wire:key="modal-emp-{{ $empresa->id }}"
                                            wire:click.prevent="selectEmpresaInModal({{ $empresa->id }}, '{{ addslashes($empresa->nombre) }}')"
                                            @click="openModalSearch = false"
                                            class="dropdown-item">{{ $empresa->nombre }}</a>
                                        @endforeach
                                    </div>
                                    @if($empresasForModal->hasPages())
                                    <div class="p-2 border-top bg-light d-flex justify-content-center">
                                        {{ $empresasForModal->links('livewire::bootstrap') }}</div>
                                    @endif
                                </div>
                                @elseif(strlen(trim($empresaSearchModal)) > 0)
                                <div x-show="openModalSearch" class="dropdown-menu d-block w-100 mt-1"
                                    style="z-index: 1055;">
                                    <span class="dropdown-item-text">Sin resultados para '{{ $empresaSearchModal }}'.</span>
                                </div>
                                @endif
                                @endif
                            </div>
                            @error('empresa_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        @endif

                        <div class="material-form-group-with-icon mb-4">
                            <i class="fas fa-tag fa-fw form-icon"></i>
                            <input id="nombre" type="text" wire:model.live="nombre"
                                class="material-form-control-with-icon @error('nombre') is-invalid @enderror"
                                placeholder=" "
                                @if(auth()->user()->hasRole('super_admin') && !$empresa_id) disabled @endif
                            >
                            <label for="nombre" class="material-form-label">
                                @if(auth()->user()->hasRole('super_admin') && !$empresa_id)
                                    Primero seleccione una empresa
                                @else
                                    Nombre de la Categoría <span class="text-danger">*</span>
                                @endif
                            </label>
                            @error('nombre') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="material-form-group-with-icon">
                            <i class="fas fa-align-left fa-fw form-icon"></i>
                            <textarea id="descripcion" wire:model.live="descripcion"
                                class="material-form-control-with-icon" placeholder=" "
                                style="height: 100px"></textarea>
                            <label for="descripcion" class="material-form-label">Descripción (Opcional)</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                            wire:target="saveCategory">
                            <span wire:loading.remove wire:target="saveCategory"><i
                                    class="fa-solid fa-floppy-disk me-1"></i> Guardar</span>
                            <span wire:loading wire:target="saveCategory"><span
                                    class="spinner-border spinner-border-sm"></span> Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    
    @if($showConfirmModal)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Categoría</h5>
                    <button type="button" class="btn-close" wire:click="closeConfirmModal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Realmente desea eliminar la categoría "<strong>{{ $categoryToDelete->nombre ?? '' }}</strong>"?</p>
                    @if($categoryToDelete?->productos_count > 0)
                        <div class="alert alert-danger mt-2"><strong>Atención:</strong> Esta categoría tiene productos asociados y no puede ser eliminada.</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeConfirmModal">Cancelar</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteCategory"
                        @if($categoryToDelete?->productos_count > 0) disabled @endif
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deleteCategory">Eliminar</span>
                        <span wire:loading wire:target="deleteCategory"
                            class="spinner-border spinner-border-sm"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if($showModal || $showConfirmModal)
    <div class="modal-backdrop fade show"></div>
    @endif
</div>