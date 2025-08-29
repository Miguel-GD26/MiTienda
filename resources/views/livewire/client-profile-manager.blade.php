<div>
    @if($showModal)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form wire:submit.prevent="updateProfile">
                    <div class="modal-header">
                        <h5 class="modal-title">Mi Perfil</h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        
                        {{-- SECCIÓN DATOS DEL CLIENTE --}}
                        <h5 class="mb-3 text-primary">Mis Datos Personales</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="material-form-group-with-icon">
                                    <i class="fa-solid fa-user form-icon"></i>
                                    {{-- El wire:model apunta a 'cliente_nombre' del modelo Cliente --}}
                                    <input type="text" id="client_profile_name"
                                        class="material-form-control-with-icon @error('cliente_nombre') is-invalid @enderror"
                                        wire:model.defer="cliente_nombre" placeholder=" ">
                                    <label for="client_profile_name" class="material-form-label">Nombre Completo</label>
                                </div>
                                @error('cliente_nombre') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="material-form-group-with-icon">
                                    <i class="fa-solid fa-phone form-icon"></i>
                                    {{-- El wire:model apunta a 'cliente_telefono' del modelo Cliente --}}
                                    <input type="text" id="client_profile_phone"
                                        class="material-form-control-with-icon @error('cliente_telefono') is-invalid @enderror"
                                        wire:model.defer="cliente_telefono" placeholder=" ">
                                    <label for="client_profile_phone" class="material-form-label">Teléfono</label>
                                </div>
                                @error('cliente_telefono') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- SECCIÓN DATOS DE LA CUENTA (USER) --}}
                        <h5 class="mb-3 text-primary">Datos de mi Cuenta</h5>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="material-form-group-with-icon">
                                    <i class="fa-solid fa-envelope form-icon"></i>
                                    {{-- Decidí dejarlo 'readonly' como en tu ejemplo, es buena práctica --}}
                                    <input type="email" id="client_profile_email" class="material-form-control-with-icon"
                                        wire:model.defer="email" placeholder=" " readonly>
                                    <label for="client_profile_email" class="material-form-label">Email (no se puede
                                        cambiar)</label>
                                </div>
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        
                        @if(!$isSocialUser)
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="material-form-group-with-icon">
                                    <i class="fa-solid fa-lock form-icon"></i>
                                    <input type="password" id="client_profile_password"
                                        class="material-form-control-with-icon @error('password') is-invalid @enderror"
                                        wire:model.defer="password" placeholder=" ">
                                    <label for="client_profile_password" class="material-form-label">Nueva Contraseña (opcional)</label>
                                </div>
                                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="material-form-group-with-icon">
                                    <i class="fa-solid fa-lock form-icon"></i>
                                    <input type="password" id="client_profile_password_confirmation"
                                        class="material-form-control-with-icon" wire:model.defer="password_confirmation"
                                        placeholder=" ">
                                    <label for="client_profile_password_confirmation" class="material-form-label">Confirmar
                                        Contraseña</label>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">Cerrar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>Actualizar Perfil</span>
                            <span wire:loading>Actualizando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- El fondo oscuro del modal --}}
    <div class="modal-backdrop fade show"></div>
    @endif
</div>