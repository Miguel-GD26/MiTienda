<div>
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body">
            <div class="row gy-4">



                {{-- Filtro de Categorías Interactivo --}}
                <div class="col-md-5 mb-3 position-relative" x-data="{ open: true }" @click.away="open = false">
                    @if($selectedCategoriaName)
                    <div class="material-form-group-with-icon is-selected">
                        <i class="fa-solid fa-tags fa-fw form-icon"></i>
                        {{-- Aplicamos las clases al input para que se vea "seleccionado" --}}
                        <input type="text" class="material-form-control-with-icon selected-value"
                            value="{{ $selectedCategoriaName }}" readonly>
                        <label class="material-form-label">Categoría Filtrada</label>
                        <button type="button" wire:click="clearCategoriaFilter" class="btn-change-selection"
                            title="Limpiar filtro">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @else
                    <div class="material-form-group-with-icon" @focusin="open = true">
                        <i class="fa-solid fa-tags fa-fw form-icon"></i>
                        <input type="text" wire:model.live.debounce.300ms="categoriaSearch"
                            @keydown.space ="if($wire.categoriaSearch.trim() === '') { $wire.call('listAllCategorias'); }"
                            id="categoria_search_filter" class="material-form-control-with-icon" placeholder=" "
                            autocomplete="off" />
                        <label for="categoria_search_filter" class="material-form-label">Filtrar por Categoría (Espacio
                            para ver todas)</label>
                        <div class="spinner-container" wire:loading
                            wire:target="categoriaSearch, selectCategoriaFilter, clearCategoriaFilter">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                        </div>
                    </div>

                    @if($categorias_for_filter_paginator && $categorias_for_filter_paginator->isNotEmpty())
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
                    <div x-show="open" class="dropdown-menu d-block position-absolute w-100 shadow-lg p-2 text-muted">
                        No se encontraron resultados.
                    </div>
                    @endif
                    @endif
                </div>

                {{-- Buscador de Productos --}}
                <div class="col-md mb-3">
                    <div class="material-form-group-with-icon">
                        <i class="fa-solid fa-magnifying-glass fa-fw form-icon"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" id="q_filter"
                            class="material-form-control-with-icon" placeholder=" ">
                        <label for="q_filter" class="material-form-label">Buscar producto por nombre...</label>

                        <div class="spinner-container" wire:loading wire:target="search">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- La inclusión de la vista parcial no cambia --}}
    @include('tienda.producto', ['productos' => $productos, 'cartItems' => $cartItems])
</div>