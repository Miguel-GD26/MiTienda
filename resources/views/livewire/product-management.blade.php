<div>
    <div class="container-fluid mt-4">
        {{-- Cabecera y Filtros --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">
                <i class="fa-solid fa-box-open me-2"></i>Listado de Productos
            </h2>
            @can('producto-create')
            <button wire:click="openModal()" class="btn btn-success shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nuevo Producto
            </button>
            @endcan
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
                <div class="row g-2">
                    {{-- FILTRO DE EMPRESA (SOLO SUPER ADMIN) --}}
                    @if(auth()->user()->hasRole('super_admin'))
                    <div class="col-12 col-md-4 position-relative" x-data="{ open: true }" @click.away="open = false">
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

                    {{-- FILTRO DE CATEGORÍA --}}
                    <div class="col-12 {{ auth()->user()->hasRole('super_admin') ? 'col-md-4' : 'col-md-6' }} position-relative"
                        x-data="{ open: true }" @click.away="open = false">
                        @if($selectedCategoriaName)
                        <div class="material-form-group-with-icon is-selected">
                            <i class="fa-solid fa-tags fa-fw form-icon"></i>
                            <div class="material-form-control-with-icon selected-value d-flex align-items-center">
                                <span class="text-truncate">{{ $selectedCategoriaName }}</span>
                                <button type="button" wire:click="clearCategoriaFilter"
                                    class="btn-change-selection ms-auto" title="Limpiar filtro">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <label class="material-form-label active">Categoría Filtrada</label>
                        </div>
                        @else
                        <div class="material-form-group-with-icon" @focusin="open = true">
                            <i class="fa-solid fa-tags fa-fw form-icon"></i>
                            <input type="text" wire:model.live.debounce.300ms="categoriaSearch"
                                @keydown.space.prevent="if($wire.categoriaSearch.trim() === '') { $wire.call('listAllCategorias'); }"
                                id="categoria_search_filter" class="material-form-control-with-icon" placeholder=" "
                                autocomplete="off" @if(auth()->user()->hasRole('super_admin') && !$empresa_id_filter)
                            disabled @endif
                            />
                            <label for="categoria_search_filter" class="material-form-label">
                                @if(auth()->user()->hasRole('super_admin') && !$empresa_id_filter)
                                Seleccione una empresa
                                @else
                                Filtrar por Categoría (Espacio para ver todas)
                                @endif
                            </label>
                        </div>

                        @if($categorias_for_filter_paginator && $categorias_for_filter_paginator->total() > 0)
                        <div x-show="open" class="dropdown-menu d-block position-absolute w-100 shadow-lg mt-1"
                            style="z-index: 100;" x-transition>
                            <div class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">
                                @foreach($categorias_for_filter_paginator as $categoria)
                                <a href="#" wire:key="filter-cat-{{ $categoria->id }}"
                                    wire:click.prevent="selectCategoriaFilter({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}')"
                                    class="dropdown-item">
                                    {{ $categoria->nombre }}
                                </a>
                                @endforeach
                            </div>
                            @if($categorias_for_filter_paginator->hasPages())
                            <div class="p-2 border-top bg-light d-flex justify-content-center">
                                {{ $categorias_for_filter_paginator->links('livewire::bootstrap') }}
                            </div>
                            @endif
                        </div>

                        @elseif(strlen(trim($categoriaSearch)) > 0)
                            <div x-show="open"
                                class="dropdown-menu d-block position-absolute w-100 shadow-lg p-2 text-muted">
                                No se encontraron resultados.
                            </div>     
                        @endif
                        @endif
                    </div>

                    {{-- BÚSQUEDA DE PRODUCTO --}}
                    <div class="col-12 {{ auth()->user()->hasRole('super_admin') ? 'col-md-4' : 'col-md-6' }}">
                        <div class="material-form-group-with-icon">
                            <i class="fas fa-search fa-fw form-icon"></i>
                            <input id="searchProduct" type="text" wire:model.live.debounce.300ms="search"
                                class="material-form-control-with-icon" placeholder=" " autocomplete="off" />
                            <label for="searchProduct" class="material-form-label">Buscar por nombre de
                                producto...</label>
                            <div class="spinner-container" wire:loading
                                wire:target="search, empresa_id_filter, categoria_id_filter">
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
                                <th style="width: 70px;">Imagen</th>
                                <th>Nombre</th>
                                @if(auth()->user()->hasRole('super_admin'))<th>Empresa</th>@endif
                                <th>Categoría</th>
                                <th class="text-end">Precio / Oferta</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($productos as $producto)
                            <tr class="align-middle">
                                <td>
                                    @if ($producto->imagen_url)
                                    <img src="{{cloudinary()->image($producto->imagen_url)->toUrl()}}"
                                        alt="{{ $producto->nombre }}" class="img-thumbnail"
                                        style="width: 50px; height: 50px; object-fit: cover;"
                                        >
                                    @else
                                    <div class="icon-circle bg-secondary-subtle text-secondary mx-auto"><i
                                            class="fa-solid fa-image"></i></div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $producto->nombre }}</div>
                                </td>
                                @if(auth()->user()->hasRole('super_admin'))<td class="small text-muted">
                                    {{ $producto->empresa->nombre ?? 'N/A' }}</td>@endif
                                <td>{{ $producto->categoria->nombre ?? 'N/A' }}</td>
                                <td class="text-end">
                                    @if($producto->precio_oferta > 0 && $producto->precio_oferta < $producto->precio)
                                        <div><span
                                                class="fw-bold text-danger">S/.{{ number_format($producto->precio_oferta, 2) }}</span><del
                                                class="d-block text-muted small">S/.{{ number_format($producto->precio, 2) }}</del>
                                        </div>
                                        @else
                                        <span class="fw-bold">S/.{{ number_format($producto->precio, 2) }}</span>
                                        @endif
                                </td>
                                <td class="text-center"><span
                                        class="badge rounded-pill {{ $producto->stock <= 0 ? 'text-bg-danger' : ($producto->stock < 10 ? 'text-bg-warning' : 'text-bg-success') }}">{{ $producto->stock <= 0 ? 'Agotado' : $producto->stock }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        @can('producto-edit', $producto)<button
                                            wire:click="openModal({{ $producto->id }})"
                                            class="btn btn-sm btn-outline-primary" title="Editar"><i
                                                class="fa-solid fa-pencil"></i></button>@endcan
                                        @can('producto-delete', $producto)<button
                                            wire:click="openConfirmModal({{ $producto->id }})"
                                            class="btn btn-sm btn-outline-danger" title="Eliminar"><i
                                                class="fa-solid fa-trash-can"></i></button>@endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('super_admin') ? '7' : '6' }}">
                                    <div class="text-center p-5"><i
                                            class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                                        <p class="mb-0 text-muted">No se encontraron productos.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($productos->hasPages())<div class="card-footer bg-white border-0">{{ $productos->links() }}</div>@endif
        </div>
    </div>

    {{-- MODAL DE CREACIÓN / EDICIÓN --}}
    @if($showModal)
    <div class="modal fade show" style="display: block;" tabindex="-1" x-data
        @keydown.escape.window="$wire.closeModal()">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="saveProduct">
                    <div class="modal-header">
                        <h5 class="modal-title"><i
                                class="fa-solid {{ $isEditMode ? 'fa-pen-to-square' : 'fa-plus-circle' }} me-2"></i>{{ $isEditMode ? 'Editar Producto' : 'Nuevo Producto' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>

                    <div class="modal-body" style="overflow-y: auto; max-height: 75vh;">
                        {{-- BUSCADOR DE EMPRESA EN MODAL --}}
                        @if(auth()->user()->hasRole('super_admin'))
                        <div class="mb-4" x-data="{ openModalSearch: false }" @click.away="openModalSearch = false">
                            <div class="position-relative">
                                @if($selectedEmpresaNameInModal)
                                <div class="material-form-group-with-icon is-selected">
                                    <i class="fa-solid fa-building fa-fw form-icon"></i>
                                    <div
                                        class="material-form-control-with-icon selected-value d-flex align-items-center">
                                        <span class="text-truncate">{{ $selectedEmpresaNameInModal }}</span>
                                        @if(!$isEditMode)
                                        <button type="button" wire:click="clearSelectedEmpresa"
                                            class="btn-change-selection ms-auto" title="Limpiar selección"><i
                                                class="fas fa-times"></i></button>
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
                                    <label for="empresa_search_modal" class="material-form-label">Buscar Empresa <span
                                            class="text-danger">*</span></label>
                                </div>
                                @if($empresasForModal && $empresasForModal->total() > 0)
                                <div x-show="openModalSearch"
                                    class="dropdown-menu d-block position-absolute w-100 shadow-lg mt-1"
                                    style="z-index: 1060;" x-transition>
                                    <div wire:loading.remove wire:target="empresaSearchModal"
                                        class="list-group list-group-flush"
                                        style="max-height: 200px; overflow-y: auto;">
                                        @foreach($empresasForModal as $empresa)
                                        <a href="#" wire:key="modal-emp-{{ $empresa->id }}"
                                            wire:click.prevent="selectEmpresa({{ $empresa->id }}, '{{ addslashes($empresa->nombre) }}')"
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
                                    <span class="dropdown-item-text">Sin resultados para
                                        '{{ $empresaSearchModal }}'.</span>
                                </div>
                                @endif
                                @endif
                            </div>
                            @error('empresa_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        @endif

                        {{-- BUSCADOR DE CATEGORÍA EN MODAL --}}
                        <div class="mb-4" x-data="{ openCatSearch: false }" @click.away="openCatSearch = false">
                            <div class="position-relative">
                                @if($selectedCategoriaNameInModal)
                                <div class="material-form-group-with-icon is-selected">
                                    <i class="fa-solid fa-tags fa-fw form-icon"></i>
                                    <div
                                        class="material-form-control-with-icon selected-value d-flex align-items-center">
                                        <span class="text-truncate">{{ $selectedCategoriaNameInModal }}</span>
                                        <button type="button" wire:click="clearSelectedCategoria"
                                            class="btn-change-selection ms-auto" title="Cambiar categoría"><i
                                                class="fas fa-times"></i></button>
                                    </div>
                                    <label class="material-form-label active">Categoría</label>
                                </div>
                                @else
                                <div class="material-form-group-with-icon" @focusin="openCatSearch = true">
                                    <i class="fa-solid fa-tags fa-fw form-icon"></i>
                                    <input type="text" wire:model.live.debounce.300ms="categoriaSearchModal"
                                        @keydown.space.prevent="if($wire.categoriaSearchModal.trim() === '' && {{ $empresa_id ? 'true' : 'false' }}) { $wire.call('listAllCategoriasForModal'); }"
                                        id="categoria_search_modal" class="material-form-control-with-icon"
                                        placeholder=" " autocomplete="off" {{ !$empresa_id ? 'disabled' : '' }} />
                                    <label for="categoria_search_modal" class="material-form-label">
                                        {{ $empresa_id ? 'Buscar Categoría' : 'Primero seleccione una empresa' }} <span
                                            class="text-danger">*</span>
                                    </label>
                                </div>
                                @if($categoriasForModal && $categoriasForModal->total() > 0)
                                <div x-show="openCatSearch"
                                    class="dropdown-menu d-block position-absolute w-100 shadow-lg mt-1"
                                    style="z-index: 1055;" x-transition>
                                    <div wire:loading.remove wire:target="categoriaSearchModal"
                                        class="list-group list-group-flush"
                                        style="max-height: 200px; overflow-y: auto;">
                                        @foreach($categoriasForModal as $categoria)
                                        <a href="#" wire:key="modal-cat-{{ $categoria->id }}"
                                            wire:click.prevent="selectCategoria({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}')"
                                            @click="openCatSearch = false"
                                            class="dropdown-item">{{ $categoria->nombre }}</a>
                                        @endforeach
                                    </div>
                                    @if($categoriasForModal->hasPages())
                                    <div class="p-2 border-top bg-light d-flex justify-content-center">
                                        {{ $categoriasForModal->links('livewire::bootstrap') }}</div>
                                    @endif
                                </div>
                                @elseif(strlen(trim($categoriaSearchModal)) > 0)
                                <div x-show="openCatSearch" class="dropdown-menu d-block w-100 mt-1"
                                    style="z-index: 1055;">
                                    <span class="dropdown-item-text">Sin resultados para
                                        '{{ $categoriaSearchModal }}'.</span>
                                </div>
                                @endif
                                @endif
                            </div>
                            @error('categoria_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="material-form-group-with-icon mb-4">
                            <i class="fas fa-box fa-fw form-icon"></i>
                            <input type="text" wire:model.live="nombre" id="nombre"
                                class="material-form-control-with-icon @error('nombre') is-invalid @enderror"
                                placeholder=" ">
                            <label for="nombre" class="material-form-label">Nombre del Producto
                                <span class="text-danger">*</span>
                            </label>
                            @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="material-form-group-with-icon mb-4">
                                    <span class="form-icon" style="padding-right: 5px; font-weight: bold;">S/.</span>
                                    <input type="number" step="0.01" min="0" wire:model.live="precio" id="precio"
                                        class="material-form-control-with-icon @error('precio') is-invalid @enderror"
                                        placeholder=" "><label for="precio" class="material-form-label">Precio <span
                                            class="text-danger">*</span></label>@error('precio')<div
                                        class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="material-form-group-with-icon mb-4"><i
                                        class="fas fa-percent fa-fw form-icon"></i><input type="number" step="0.01"
                                        min="0" wire:model.live="precio_oferta" id="precio_oferta"
                                        class="material-form-control-with-icon @error('precio_oferta') is-invalid @enderror"
                                        placeholder=" "><label for="precio_oferta" class="material-form-label">Precio
                                        Oferta</label>@error('precio_oferta')<div class="text-danger small mt-1">
                                        {{ $message }}</div>@enderror</div>
                            </div>
                            <div class="col-md-4">
                                <div class="material-form-group-with-icon mb-4"><i
                                        class="fas fa-cubes fa-fw form-icon"></i><input type="number" min="0"
                                        wire:model.live="stock" id="stock"
                                        class="material-form-control-with-icon @error('stock') is-invalid @enderror"
                                        placeholder=" "><label for="stock" class="material-form-label">Stock <span
                                            class="text-danger">*</span></label>@error('stock')<div
                                        class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
                            </div>
                        </div>

                        <div class="material-form-group-with-icon mb-4">
                            <i class="fas fa-align-left fa-fw form-icon"></i>
                            <textarea wire:model.live="descripcion" id="descripcion"
                                class="material-form-control-with-icon" placeholder=" " style="height: 100px">
                            </textarea>
                            <label for="descripcion" class="material-form-label">Descripción (Opcional)</label>
                        </div>

                        <div class="mb-3 p-3 border rounded bg-light" @image-reset.window="imagePreview = null" x-data="{ 
                                imagePreview: null,
                                currentImageUrl: '{{ $imagen_url ? cloudinary()->image($imagen_url)->toUrl() : '' }}'
                            }">

                            <label for="new_imagen_url_input" class="form-label fw-bold">Imagen del Producto</label>

                            <input type="file" wire:model="new_imagen_url" id="new_imagen_url_input"
                                class="form-control @error('new_imagen_url') is-invalid @enderror" x-ref="newImageInput"
                                @change="
                                    if ($event.target.files.length > 0) { 
                                        let reader = new FileReader(); 
                                        reader.onload = (e) => { imagePreview = e.target.result; }; 
                                        reader.readAsDataURL($event.target.files[0]); 
                                    } else { 
                                        imagePreview = null; 
                                    }
                                ">

                            @error('new_imagen_url')<div class="text-danger small mt-1">{{ $message }}</div>@enderror

                            <div class="d-flex gap-3 mt-3 align-items-center">
                                @if($isEditMode)
                                <div x-show="!imagePreview && currentImageUrl"
                                    class=" position-relative d-inline-block text-center" style="display: none;">
                                    <img :src="currentImageUrl" loading="lazy" class="img-thumbnail"
                                        style="width:100px;height:100px;object-fit:cover;">
                                    <span
                                        class="position-absolute top-50 start-50 translate-middle fw-bold px-3 py-1 rounded"
                                        style="background-color: rgba(0,0,0,0.6); color: white; font-size: 1.2rem;">
                                        Actual
                                    </span>
                                </div>
                                @endif

                                <div x-show="imagePreview" class="position-relative text-center" style="display: none;">
                                    <img :src="imagePreview"  class="img-thumbnail" loading="lazy"
                                        style="width:100px;height:100px;object-fit:cover;">
                                    <button type="button"
                                        class="btn btn-danger btn-sm rounded-circle position-absolute top-0 start-100 translate-middle"
                                        style="padding: .1rem .4rem; line-height: 1;" title="Quitar imagen" @click="
                                                imagePreview = null;
                                                $refs.newImageInput.value = null;
                                                @this.set('new_imagen_url', null);
                                            ">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div wire:loading wire:target="saveProduct, new_imagen_url" class="progress mt-2"
                            style="height: 10px; width: 100%;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                role="progressbar" style="width: 100%; min-height: 10px; ">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"><i
                                class="fas fa-times me-1"></i>Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                            wire:target="saveProduct, new_imagen_url">
                            <span wire:loading.remove wire:target="saveProduct, new_imagen_url">
                                <i class="fa-solid fa-save me-1"></i> Guardar
                            </span>
                            <span wire:loading wire:target="saveProduct, new_imagen_url">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Guardando...</span>
                                </div>
                                <span class="ms-1">Guardando...</span>
                            </span>
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
                    <h5 class="modal-title">Eliminar Producto</h5>
                    <button type="button" class="btn-close" wire:click="closeConfirmModal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Realmente desea eliminar el producto "<strong>{{ $productToDelete->nombre ?? '' }}</strong>"?
                        Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeConfirmModal">Cancelar</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteProduct"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deleteProduct">Sí, Eliminar</span>
                        <span wire:loading wire:target="deleteProduct">Eliminando...</span>
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