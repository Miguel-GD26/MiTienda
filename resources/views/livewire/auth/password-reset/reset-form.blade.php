<div class="container my-4 mx-auto" style="max-width: 900px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0 text-center text-md-start">Restablecer la Contraseña</h4>
        </div>
        <div class="card-body">
            <div class="row g-4 align-items-center mt-3">
                <div class="col-12 col-md-4 text-center">
                    <img src="{{ asset('assets/img/usuario.gif') }}" alt="Icono" class="img-fluid" style="max-height: 200px;">
                </div>
                <div class="col-12 col-md-8">
                    <p class="text-muted">Confirma tu correo y elige una nueva contraseña.</p>
                    <form wire:submit.prevent="resetPassword">
                        <div class="material-form-group-with-icon mb-4">
                            <i class="fas fa-envelope fa-fw form-icon"></i>
                            <input id="email" type="email" wire:model.live="email" class="material-form-control-with-icon @error('email') is-invalid @enderror" placeholder=" ">
                            <label for="email" class="material-form-label">Correo electrónico</label>
                            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="row" x-data="{ showPassword: false, showConfirmation: false }">
                            <div class="col-md-6">
                                <div class="material-form-group-with-icon mb-4">
                                    <i class="fas fa-lock fa-fw form-icon"></i>
                                    <input id="password" :type="showPassword ? 'text' : 'password'" wire:model.live="password" class="material-form-control-with-icon @error('password') is-invalid @enderror" placeholder=" ">
                                    <label for="password" class="material-form-label">Nueva Contraseña</label>
                                    <i @click="showPassword = !showPassword" :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="password-toggle-icon"></i>
                                    @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="material-form-group-with-icon mb-4">
                                    <i class="fas fa-lock fa-fw form-icon"></i>
                                    <input id="password_confirmation" :type="showConfirmation ? 'text' : 'password'" wire:model.live="password_confirmation" class="material-form-control-with-icon" placeholder=" ">
                                    <label for="password_confirmation" class="material-form-label">Confirmar contraseña</label>
                                    <i @click="showConfirmation = !showConfirmation" :class="showConfirmation ? 'fas fa-eye-slash' : 'fas fa-eye'" class="password-toggle-icon"></i>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="fas fa-save me-1"></i> Actualizar Contraseña</span>
                                <span wire:loading><span class="spinner-border spinner-border-sm"></span> Actualizando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>