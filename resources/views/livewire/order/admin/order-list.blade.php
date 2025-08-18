<div>
    <div class="container-fluid mt-4">
        <h2 class="h3 mb-4"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Gestión de Pedidos</h2>
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white p-3">
                <div class="row g-3 align-items-center">
                    @if(auth()->user()->hasRole('super_admin'))
                    {{-- BUSCADOR INTERACTIVO DE EMPRESA (IDÉNTICO A PRODUCTOS) --}}
                    <div class="col-12 col-md-4 position-relative" x-data="{ open: true }" @click.away="open = false">
                        @if($selectedEmpresaName)
                        <div class="material-form-group-with-icon is-selected">
                            <i class="fa-solid fa-building fa-fw form-icon"></i>
                            <div class="material-form-control-with-icon selected-value d-flex align-items-center">
                                <span class="text-truncate">{{ $selectedEmpresaName }}</span>
                                <button type="button" wire:click="clearEmpresaFilter" class="btn-change-selection ms-auto" title="Limpiar filtro">
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
                            <label for="empresa_search_filter" class="material-form-label">Filtrar por Empresa</label>
                        </div>
                        @if($empresas_for_filter_paginator && $empresas_for_filter_paginator->total() > 0)
                        <div x-show="open" class="dropdown-menu d-block position-absolute w-100 shadow-lg mt-1" style="z-index: 100;" x-transition>
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
                            <div x-show="open" class="dropdown-menu d-block position-absolute w-100 shadow-lg p-2 text-muted">No se encontraron resultados.</div>     
                        @endif
                        @endif
                    </div>
                    @endif
                    <div class="col-md">
                        <div class="material-form-group-with-icon">
                            <i class="fas fa-clipboard-check fa-fw form-icon"></i>
                            <select wire:model.live="estado" class="material-form-control-with-icon form-select">
                                <option value="">Todos los Estados</option>
                                @foreach($estados as $est)<option value="{{ $est }}">{{ ucfirst($est) }}</option>
                                @endforeach
                            </select>
                            <label class="material-form-label active">Filtrar por Estado</label>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="material-form-group-with-icon position-relative">
                            <i class="fas fa-user fa-fw form-icon"></i>
                            <input type="text" wire:model.live.debounce.300ms="cliente_nombre"
                                class="material-form-control-with-icon" placeholder=" ">
                            <label class="material-form-label">Nombre del cliente...</label>
                            <div wire:loading wire:target="empresa_id, estado, cliente_nombre"
                                class="position-absolute top-50 end-0 translate-middle-y me-3">
                                <span class="spinner-border spinner-border-sm text-secondary"></span>
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
                                <th class="text-center">ID</th>
                                <th>Cliente</th>
                                @if(auth()->user()->hasRole('super_admin'))<th>Empresa</th>@endif
                                <th class="text-end">Total</th>
                                <th class="text-center">Estado</th>
                                <th>Fecha</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody wire:loading.class.delay="opacity-50" wire:key="orders-tbody">
                            @if(auth()->user()->hasRole('super_admin') && !$empresa_id)
                            <tr>
                                <td colspan="7" class="text-center p-5">
                                    <i class="fa-solid fa-store fa-3x text-primary mb-3"></i>
                                    <h4 class="text-muted">Por favor, seleccione una empresa para ver sus pedidos.</h4>
                                </td>
                            </tr>
                            @else
                            @forelse($pedidos as $pedido)
                            <tr wire:key="pedido-{{ $pedido->id }}" class="align-middle">
                                <td class="text-center fw-bold">#{{ $pedido->id }}</td>
                                <td>{{ $pedido->cliente->nombre ?? 'N/A' }}</td>
                                @if(auth()->user()->hasRole('super_admin'))<td>{{ $pedido->empresa->nombre ?? 'N/A' }}
                                </td>@endif
                                <td class="text-end fw-bold">S/.{{ number_format($pedido->total, 2) }}</td>
                                <td class="text-center"><span
                                        class="badge rounded-pill @switch($pedido->estado) @case('pendiente') bg-warning text-dark @break @case('atendido') bg-info text-dark @break @case('enviado') bg-dark @break @case('entregado') bg-success @break @case('cancelado') bg-danger @break @default bg-secondary @endswitch">{{ ucfirst($pedido->estado) }}</span>
                                </td>
                                <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center"><a href="{{ route('pedidos.show', $pedido) }}"
                                        class="btn btn-sm btn-outline-info" title="Ver Detalles"><i
                                            class="fa-solid fa-eye"></i></a></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('super_admin') ? '7' : '6' }}"
                                    class="text-center py-4">
                                    <p class="text-muted mb-0">No se encontraron pedidos con los filtros actuales.</p>
                                </td>
                            </tr>
                            @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($pedidos && $pedidos->hasPages())
            <div class="card-footer d-flex justify-content-end">
                {{ $pedidos->links() }}
            </div>
            @endif
        </div>
    </div>
</div>