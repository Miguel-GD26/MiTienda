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
            <div class="card-header bg-light">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                        placeholder="Buscar por nombre o email...">
                    <span class="input-group-text" wire:loading wire:target="search">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </span>
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
                                <th>Suscripción</th>
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
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($reg->name) }}&background=random&color=fff"
                                            alt="Avatar de {{ $reg->name }}" class="rounded-circle me-2" width="32"
                                            height="32">
                                        <span>{{ $reg->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $reg->email }}</td>
                                @if(auth()->user()->hasRole('super_admin'))
                                <td>
                                    <span
                                        class="badge text-bg-info fw-normal">{{ $reg->empresa->nombre ?? 'Sin empresa' }}</span>
                                </td>
                                <td>
                                    @if($reg->empresa)
                                    @php
                                    $status = $reg->empresa->subscription_status;
                                    $badgeClass = 'text-bg-secondary';
                                    $text = 'N/A';

                                    switch ($status) {
                                    case 'trialing':
                                    if ($reg->empresa->trial_ends_at?->isPast()) {
                                    $badgeClass = 'text-bg-danger';
                                    $text = 'Prueba Expirada';
                                    } else {
                                    $badgeClass = 'text-bg-warning';
                                    $text = 'En Prueba';
                                    }
                                    break;
                                    case 'active':
                                    $badgeClass = 'text-bg-success';
                                    $text = 'Activo';
                                    break;
                                    case 'past_due':
                                    $badgeClass = 'text-bg-danger';
                                    $text = 'Pago Vencido';
                                    break;
                                    case 'canceled':
                                    $badgeClass = 'text-bg-dark';
                                    $text = 'Cancelado';
                                    break;
                                    }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $text }}</span>
                                    @if($status === 'trialing' && $reg->empresa->trial_ends_at)
                                    <small class="d-block text-muted" data-bs-toggle="tooltip"
                                        title="Finaliza el {{ $reg->empresa->trial_ends_at->format('d/m/Y') }}">
                                        {{ $reg->empresa->trial_ends_at->diffForHumans(null, true) }}
                                    </small>
                                    @endif
                                    @else
                                    <span class="badge text-bg-secondary">N/A</span>
                                    @endif
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
                                        <button wire:click="openModal({{ $reg->id }})"
                                            class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip"
                                            title="Editar"><i class="fa-solid fa-pencil"></i></button>
                                        @endcan
                                        @can('user-activate', $reg)
                                        <button wire:click="openConfirmModal('toggle', {{ $reg->id }})"
                                            class="btn btn-sm {{ $reg->activo ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                            data-bs-toggle="tooltip"
                                            title="{{ $reg->activo ? 'Desactivar' : 'Activar' }}"><i
                                                class="fa-solid {{ $reg->activo ? 'fa-ban' : 'fa-circle-check' }}"></i></button>
                                        @endcan
                                        @can('user-delete', $reg)
                                        <button wire:click="openConfirmModal('delete', {{ $reg->id }})"
                                            class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip"
                                            title="Eliminar"><i class="fa-solid fa-trash-can"></i></button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('super_admin') ? '8' : '7' }}">
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
            <div class="card-footer bg-light border-top">
                {{ $registros->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- MODAL DE CREACIÓN / EDICIÓN --}}
    @if($showModal)
    <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog" x-data
        @keydown.escape.window="$wire.closeModal()">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid {{ $isEditMode ? 'fa-user-pen' : 'fa-user-plus' }} me-2"></i>
                        {{ $isEditMode ? 'Editar Usuario' : 'Crear Usuario' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveUser" wire:loading.class="pe-none opacity-50" novalidate>
                        <h6 class="mb-3 text-primary border-bottom pb-2"><i
                                class="fa-solid fa-id-card me-2"></i>Información Personal</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="material-form-group-with-icon mb-4">
                                    <i class="fas fa-user fa-fw form-icon"></i>
                                    <input id="name" type="text" wire:model.live="name"
                                        class="material-form-control-with-icon @error('name') is-invalid @enderror"
                                        placeholder=" ">
                                    <label for="name" class="material-form-label">Nombre completo <span
                                            class="text-danger">*</span></label>
                                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="material-form-group-with-icon mb-4">
                                    <i class="fas fa-envelope fa-fw form-icon"></i>
                                    <input id="email" type="email" wire:model.live="email"
                                        class="material-form-control-with-icon @error('email') is-invalid @enderror"
                                        placeholder=" ">
                                    <label for="email" class="material-form-label">Correo Electrónico <span
                                            class="text-danger">*</span></label>
                                    @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row" x-data="{ showPassword: false, showConfirmation: false }">
                            <div class="col-md-6">
                                <div class="material-form-group-with-icon mb-4">
                                    <i class="fas fa-lock fa-fw form-icon"></i>
                                    <input id="password" :type="showPassword ? 'text' : 'password'"
                                        wire:model.live="password"
                                        class="material-form-control-with-icon @error('password') is-invalid @enderror"
                                        placeholder=" " autocomplete="new-password">
                                    <label for="password" class="material-form-label">Contraseña @if(!$isEditMode)<span
                                            class="text-danger">*</span>@endif</label>
                                    <i @click="showPassword = !showPassword"
                                        :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"
                                        class="password-toggle-icon"></i>
                                    @if($isEditMode)<small class="form-text text-muted d-block mt-1"></small>@endif
                                    @error('password') <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="material-form-group-with-icon mb-4">
                                    <i class="fas fa-lock fa-fw form-icon"></i>
                                    <input id="password_confirmation" :type="showConfirmation ? 'text' : 'password'"
                                        wire:model.live="password_confirmation" class="material-form-control-with-icon"
                                        placeholder=" ">
                                    <label for="password_confirmation" class="material-form-label">Confirmar Contraseña
                                        @if(!$isEditMode)<span class="text-danger">*</span>@endif</label>
                                    <i @click="showConfirmation = !showConfirmation"
                                        :class="showConfirmation ? 'fas fa-eye-slash' : 'fas fa-eye'"
                                        class="password-toggle-icon"></i>
                                </div>
                            </div>
                        </div>

                        @can('user-edit')
                        <h6 class="mt-3 mb-3 text-primary border-bottom pb-2"><i
                                class="fa-solid fa-user-gear me-2"></i>Gestión de Cuenta</h6>
                        <div class="row align-items-center" x-data="{ selectedRole: @entangle('role') }">
                            <div class="col-md-6">
                                <div class="material-form-group-with-icon mb-4">
                                    <i class="fas fa-user-tag fa-fw form-icon"></i>
                                    <select id="role" wire:model.live="role"
                                        @change="selectedRole = $event.target.value"
                                        class="material-form-control-with-icon @error('role') is-invalid @enderror">
                                        <option value="" disabled>Seleccione un rol</option>
                                        @foreach($roles as $r) <option value="{{ $r->name }}">{{ $r->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="role" class="material-form-label">Rol del Usuario <span
                                            class="text-danger">*</span></label>
                                    @error('role') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6" x-show="['admin', 'vendedor', 'repartidor'].includes(selectedRole)"
                                x-transition>
                                @if(auth()->user()->hasRole('super_admin') && !$isEditMode)

                                @if($empresa_id && $empresaSeleccionadaNombre)
                                <div class="material-form-group-with-icon is-selected mb-4">
                                    <i class="fas fa-building fa-fw form-icon"></i>
                                    <div class="material-form-control-with-icon selected-value">
                                        {{ $empresaSeleccionadaNombre }}</div>
                                    <label class="material-form-label">Empresa Seleccionada</label>
                                    <button type="button" wire:click="cambiarEmpresa" class="btn-change-selection"
                                        title="Cambiar selección"><i class="fas fa-times"></i></button>
                                </div>
                                @else
                                <div 
                                    class="position-relative"
                                    x-data="{ open: true }"
                                    @click.away="open = false"
                                    @focusin="open = true"
                                >
                                    <div class="material-form-group-with-icon mb-1">
                                        <i class="fas fa-search fa-fw form-icon"></i>
                                        <input 
                                            id="searchEmpresa" 
                                            type="text" 
                                            wire:model.live.debounce.300ms="searchEmpresa"
                                            @keydown.space="
                                                if ($wire.searchEmpresa.trim() === '') {
                                                    $wire.call('buscarTodo');
                                                }
                                            "
                                            class="material-form-control-with-icon @error('empresa_id') is-invalid @enderror"
                                            placeholder=" "
                                            autocomplete="off"
                                        />
                                        <label for="searchEmpresa" class="material-form-label">Presiona Espacio para ver todas <span class="text-danger">*</span></label>
                                    </div>

                                    {{-- La lista de resultados --}}
                                    @if($empresaPaginator && $empresaPaginator->total() > 0)
                                    <div x-show="open" 
                                        wire:key="empresa-dropdown-wrapper"
                                        class="dropdown-menu d-block position-absolute w-100 shadow-lg" 
                                        style="z-index: 1000;"
                                        x-transition>
                                        
                                        <div class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">
                                            @foreach($empresaPaginator as $empresa)
                                                <a href="#"
                                                wire:key="empresa-{{ $empresa->id }}"
                                                @click="open = false" 
                                                wire:click.prevent="selectEmpresa({{ $empresa->id }}, '{{ addslashes($empresa->nombre) }}')"
                                                class="list-group-item list-group-item-action">
                                                    {{ $empresa->nombre }}
                                                </a>
                                            @endforeach
                                        </div>

                                        @if($empresaPaginator->hasPages())
                                        <div class="p-2 border-top bg-light d-flex justify-content-center">
                                            {{ $empresaPaginator->links('livewire::bootstrap') }}
                                        </div>
                                        @endif

                                    </div>
                                    @elseif(strlen(trim($searchEmpresa)) > 0)
                                        <div x-show="open" class="dropdown-menu d-block position-absolute w-100 shadow-lg p-2 text-muted">
                                            No se encontraron resultados.
                                        </div>
                                    @endif

                                    @error('empresa_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="text-center my-3"><small class="text-muted fw-bold">O</small></div>
                                <div class="d-grid">
                                    <button type="button" wire:click="$set('empresaOption', 'crear_nueva')"
                                        class="btn {{ $empresaOption === 'crear_nueva' ? 'btn-primary' : 'btn-outline-primary' }}"><i
                                            class="fas fa-plus-circle me-2"></i> 2. Crear nueva empresa</button>
                                </div>
                                @endif

                                {{-- En modo edición, mostramos el nombre de la empresa como texto plano --}}
                                @elseif ($isEditMode && $empresa_id)
                                <div class="material-form-group-with-icon mb-4">
                                    <i class="fas fa-building fa-fw form-icon"></i>
                                    <input type="text" class="material-form-control-with-icon"
                                        value="{{ \App\Models\Empresa::find($empresa_id)?->nombre ?? 'Sin empresa' }}"
                                        disabled>
                                    <label for="empresa_nombre" class="material-form-label active">Empresa</label>
                                </div>
                                @endif
                            </div>
                        </div>

                        @if($isEditMode && auth()->user()->hasRole('super_admin') && $empresa_id)
                        <h6 class="mt-4 mb-3 text-primary border-bottom pb-2"><i
                                class="fa-solid fa-star me-2"></i>Gestión de Suscripción</h6>
                        <div class="row">
                            <div class="col-12">
                                <div class="material-form-group-with-icon mb-4">
                                    <i class="fas fa-check-circle fa-fw form-icon"></i>
                                    <select id="subscription_status" wire:model="subscription_status"
                                        class="material-form-control-with-icon">
                                        <option value="trialing">En Prueba (Trial)</option>
                                        <option value="active">Activo</option>
                                        <option value="past_due">Pago Vencido</option>
                                        <option value="canceled">Cancelado</option>
                                    </select>
                                    <label for="subscription_status" class="material-form-label">Estado de la
                                        Suscripción</label>
                                </div>
                                @php $empresa = \App\Models\Empresa::find($empresa_id); @endphp
                                @if($empresa && $empresa->trial_ends_at)
                                <div class="alert alert-info small p-2">
                                    @if($empresa->trial_ends_at->isPast()) La prueba expiró
                                    {{ $empresa->trial_ends_at->diffForHumans() }}.
                                    @else La prueba termina {{ $empresa->trial_ends_at->diffForHumans() }}.
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($isEditMode)
                        <div class="mt-3">
                            <label class="form-label d-block">Estado del Usuario</label>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" role="switch" id="activoSwitch"
                                    wire:model.live="activo">
                                <label class="form-check-label {{ $activo ? 'text-success' : 'text-danger' }}"
                                    for="activoSwitch">{{ $activo ? 'Activo' : 'Inactivo' }}</label>
                            </div>
                        </div>
                        @endif
                        @endcan

                        @if(!$isEditMode && auth()->user()->hasRole('super_admin') && $empresaOption === 'crear_nueva')
                        <div class="p-3 my-3 bg-light rounded border">
                            <h6 class="text-info"><i class="fa-solid fa-plus-circle me-2"></i>Datos de la Nueva Empresa
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="material-form-group-with-icon mb-4">
                                        <i class="fas fa-building fa-fw form-icon"></i>
                                        <input id="empresa_nombre_nueva" type="text" wire:model.live="empresa_nombre"
                                            class="material-form-control-with-icon @error('empresa_nombre') is-invalid @enderror"
                                            placeholder=" ">
                                        <label for="empresa_nombre_nueva" class="material-form-label">Nombre Empresa
                                            <span class="text-danger">*</span></label>
                                        @error('empresa_nombre')<span
                                            class="text-danger small mt-1">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="material-form-group-with-icon mb-4">
                                        <i class="fas fa-tag fa-fw form-icon"></i>
                                        <input id="empresa_rubro_nueva" type="text" wire:model.live="empresa_rubro"
                                            class="material-form-control-with-icon @error('empresa_rubro') is-invalid @enderror"
                                            placeholder=" ">
                                        <label for="empresa_rubro_nueva" class="material-form-label">Rubro <span
                                                class="text-danger">*</span></label>
                                        @error('empresa_rubro')<span
                                            class="text-danger small mt-1">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="material-form-group-with-icon mb-4">
                                        <i class="fab fa-whatsapp fa-fw form-icon"></i>
                                        <input id="empresa_telefono_nueva" type="text"
                                            wire:model.live="empresa_telefono_whatsapp"
                                            class="material-form-control-with-icon @error('empresa_telefono_whatsapp') is-invalid @enderror"
                                            placeholder=" " maxlength="9" x-init="
                                                $el.addEventListener('input', () => { $el.value = $el.value.replace(/\D/g, '') });
                                                $el.addEventListener('paste', (e) => {
                                                    e.preventDefault();
                                                    const text = (e.clipboardData || window.clipboardData).getData('text');
                                                    $el.value = text.replace(/\D/g, '').slice(0, 9);
                                                    $el.dispatchEvent(new Event('input'));
                                                });
                                            ">
                                        <label for="empresa_telefono_nueva" class="material-form-label">Teléfono /
                                            WhatsApp<span class="text-danger">*</span></label>
                                        @error('empresa_telefono_whatsapp')<span
                                            class="text-danger small mt-1">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="modal-footer mt-4 border-top pt-3">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal"><i
                                    class="fa-solid fa-xmark me-1"></i> Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="saveUser"><i
                                        class="fa-solid fa-floppy-disk me-1"></i> Guardar</span>
                                <span wire:loading wire:target="saveUser"><span
                                        class="spinner-border spinner-border-sm"></span> Guardando...</span>
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
                    ¿Estás seguro de que quieres eliminar a <strong>{{ $userToDeleteOrToggle->name }}</strong>? Esta
                    acción no se puede deshacer.
                    @else
                    ¿Estás seguro de que quieres {{ $userToDeleteOrToggle->activo ? 'desactivar' : 'activar' }} a
                    <strong>{{ $userToDeleteOrToggle->name }}</strong>?
                    @endif
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeConfirmModal">Cancelar</button>
                    <button type="button" class="btn btn-{{ $confirmModalType === 'delete' ? 'danger' : 'warning' }}"
                        wire:click="confirmAction">
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