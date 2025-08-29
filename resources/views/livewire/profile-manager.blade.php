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
                        <h5 class="mb-3 text-primary">Datos de tu Cuenta</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="material-form-group-with-icon">
                                    <i class="fa-solid fa-user form-icon"></i>
                                    <input type="text" id="profile_name"
                                        class="material-form-control-with-icon @error('name') is-invalid @enderror"
                                        wire:model.defer="name" placeholder=" ">
                                    <label for="profile_name" class="material-form-label">Nombre</label>
                                </div>
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="material-form-group-with-icon">
                                    <i class="fa-solid fa-envelope form-icon"></i>
                                    <input type="email" id="profile_email" class="material-form-control-with-icon"
                                        wire:model.defer="email" placeholder=" " readonly>
                                    <label for="profile_email" class="material-form-label">Email (no se puede
                                        cambiar)</label>
                                </div>
                            </div>
                        </div>

                        @if(!$isSocialUser)
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="material-form-group-with-icon">
                                    <i class="fa-solid fa-lock form-icon"></i>
                                    <input type="password" id="profile_password"
                                        class="material-form-control-with-icon @error('password') is-invalid @enderror"
                                        wire:model.defer="password" placeholder=" ">
                                    <label for="profile_password" class="material-form-label">Nueva Contraseña</label>
                                </div>
                                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="material-form-group-with-icon">
                                    <i class="fa-solid fa-lock form-icon"></i>
                                    <input type="password" id="profile_password_confirmation"
                                        class="material-form-control-with-icon" wire:model.defer="password_confirmation"
                                        placeholder=" ">
                                    <label for="profile_password_confirmation" class="material-form-label">Confirmar
                                        Contraseña</label>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($empresa)
                        <hr class="my-4">
                        <h5 class="mb-3 text-primary">Datos de tu Empresa</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="material-form-group-with-icon">
                                    <i class="fa-solid fa-building form-icon"></i>
                                    <input type="text" id="profile_empresa_nombre"
                                        class="material-form-control-with-icon @error('empresa_nombre') is-invalid @enderror"
                                        wire:model.defer="empresa_nombre" placeholder=" ">
                                    <label for="profile_empresa_nombre" class="material-form-label">Nombre de la
                                        Empresa</label>
                                </div>
                                @error('empresa_nombre') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="material-form-group-with-icon">
                                    <i class="fa-solid fa-briefcase form-icon"></i>
                                    <input type="text" id="profile_empresa_rubro"
                                        class="material-form-control-with-icon" wire:model.defer="empresa_rubro"
                                        placeholder=" ">
                                    <label for="profile_empresa_rubro" class="material-form-label">Rubro</label>
                                </div>
                                @error('empresa_rubro') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="material-form-group-with-icon">
                                    <i class="fa-brands fa-whatsapp form-icon"></i>
                                    <input type="text" id="profile_empresa_telefono"
                                        class="material-form-control-with-icon @error('empresa_telefono_whatsapp') is-invalid @enderror"
                                        wire:model.defer="empresa_telefono_whatsapp" placeholder=" ">
                                    <label for="profile_empresa_telefono" class="material-form-label">Teléfono /
                                        WhatsApp</label>
                                </div>
                                @error('empresa_telefono_whatsapp') <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3">
                                <label for="profile_empresa_logo" class="form-label">Logo de la Empresa</label>
                                <input type="file" class="form-control" id="profile_empresa_logo"
                                    wire:model="empresa_logo">
                                @error('empresa_logo') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div wire:loading wire:target="empresa_logo" class="text-primary small">Subiendo...
                                </div>
                                @if ($empresa_logo)
                                <img src="{{ $empresa_logo->temporaryUrl() }}"
                                    style="max-width: 100px; filter: drop-shadow(0 0 2px black); background-color: transparent;">
                                @elseif ($empresa && $empresa->logo_url)
                                <img src="{{ cloudinary()->image($empresa->logo_url)->toUrl() }}"
                                    style="max-width: 100px; filter: drop-shadow(0 0 2px black); background-color: transparent;">
                                @endif

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
    <div class="modal-backdrop fade show"></div>
    @endif
</div>