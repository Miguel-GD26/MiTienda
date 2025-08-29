<div>
    {{-- SECCIÓN 1: CABECERA --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800">
            <i class="fa-solid fa-users me-2"></i>
            {{-- Usamos la propiedad pública $company del componente --}}
            Mis Clientes en {{ $company->nombre }}
        </h2>
    </div>

    {{-- SECCIÓN 2: TARJETA DE CONTENIDO --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
            <div class="material-form-group-with-icon">
                <i class="fas fa-search fa-fw form-icon"></i>
                <input id="searchText" type="text" wire:model.live.debounce.500ms="search" class="material-form-control-with-icon" 
                       placeholder=" " autocomplete="off" />
                <label for="searchText" class="material-form-label">Buscar por nombre o email...</label>
            </div>
        </div>
        <div class="card-body p-0">
            <div wire:loading.class="opacity-50" class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Celular</th>
                            <th>Registrado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- La variable ahora es $customers y el item es $customer --}}
                        @forelse ($customers as $customer)
                        <tr class="align-middle">
                            <td class="text-center">{{ $customer->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=random&color=fff" 
                                         alt="Avatar de {{ $customer->name }}" class="rounded-circle me-2" width="32" height="32">
                                    <span>{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->cliente->telefono ?? 'No registrado' }}</td>
                            <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                {{-- El método ahora es openConfirmModal() --}}
                                <button wire:click="openConfirmModal({{ $customer->id }})" class="btn btn-sm btn-outline-danger" title="Eliminar cliente">
                                    <i class="fa-solid fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="text-center p-5">
                                    <i class="fa-solid fa-users-slash fa-3x text-secondary mb-3"></i>
                                    <p class="mb-0 text-muted">
                                        @if(empty($search))
                                            Aún no tienes clientes registrados en tu tienda.
                                        @else
                                            No se encontraron clientes que coincidan con "{{ $search }}".
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- SECCIÓN 3: PAGINACIÓN --}}
        @if ($customers->hasPages())
        <div class="card-footer bg-light border-top">
            {{ $customers->links() }}
        </div>
        @endif
    </div>

    {{-- SECCIÓN 4: MODAL DE CONFIRMACIÓN --}}
    @if($showConfirmModal)
    <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" wire:click="closeConfirmModal" class="btn-close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que quieres eliminar al cliente <strong>{{ $customerToDelete->name ?? '' }}</strong>? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="closeConfirmModal" class="btn btn-secondary">Cancelar</button>
                    {{-- El método ahora es deleteCustomer() --}}
                    <button type="button" wire:click="deleteCustomer" class="btn btn-danger">
                        <span wire:loading wire:target="deleteCustomer" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span wire:loading.remove wire:target="deleteCustomer">Sí, Eliminar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>