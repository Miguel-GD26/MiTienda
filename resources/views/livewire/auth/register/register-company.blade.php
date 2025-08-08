<div>
    {{-- Indicador de carga --}}
    @if($isLoading)
    <div class="position-absolute w-100 h-100 top-0 start-0 bg-white bg-opacity-75 d-flex align-items-center justify-content-center rounded"
        style="z-index: 10;">
        <div class="text-center">
            <img src="{{ asset('assets/img/codigo.gif') }}" alt="Enviando correo..."
                style="width: 180px; height: auto;">
            <p class="mt-2 mb-0 fw-bold text-primary">Enviando código de verificación...</p>
        </div>
    </div>
    @endif

    {{-- Formulario con el nuevo estilo --}}
    <form wire:submit.prevent="submit" novalidate class="position-relative">
        <hr>
        <hr>
        <p class="text-muted small">Los campos con <span class="text-danger">*</span> son obligatorios.</p>
        <h5 class="mb-3 fw-bold text-secondary">Datos de tu cuenta</h5>

        {{-- Campo: Nombre Completo --}}
        <div class="material-form-group-with-icon mb-4">
            <i class="fas fa-user fa-fw form-icon"></i>
            <input id="name" type="text" wire:model.live="name"
                class="material-form-control-with-icon @error('name') is-invalid @enderror" placeholder=" ">
            <label for="name" class="material-form-label">Nombre completo <span class="text-danger">*</span></label>
            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Campo: Correo Electrónico --}}
        <div class="material-form-group-with-icon mb-4">
            <i class="fas fa-envelope fa-fw form-icon"></i>
            <input id="email" type="email" wire:model.live="email"
                class="material-form-control-with-icon @error('email') is-invalid @enderror" placeholder=" ">
            <label for="email" class="material-form-label">Correo electrónico <span class="text-danger">*</span></label>
            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Fila para las Contraseñas --}}
        <div class="row" x-data="{ showPassword: false, showConfirmation: false }">
            {{-- Campo: Contraseña --}}
            <div class="col-md-6">
                <div class="material-form-group-with-icon mb-4">
                    <i class="fas fa-lock fa-fw form-icon"></i>
                    <input id="password" :type="showPassword ? 'text' : 'password'" wire:model.live="password"
                        class="material-form-control-with-icon @error('password') is-invalid @enderror" placeholder=" ">
                    <label for="password" class="material-form-label">Contraseña <span
                            class="text-danger">*</span></label>
                    <i @click="showPassword = !showPassword" :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"
                        class="password-toggle-icon"></i>
                    @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
            {{-- Campo: Confirmar Contraseña (sin error visible) --}}
            <div class="col-md-6">
                <div class="material-form-group-with-icon mb-4">
                    <i class="fas fa-lock fa-fw form-icon"></i>
                    <input id="password_confirmation" :type="showConfirmation ? 'text' : 'password'"
                        wire:model.live="password_confirmation" class="material-form-control-with-icon" placeholder=" ">
                    <label for="password_confirmation" class="material-form-label">Confirmar contraseña <span
                            class="text-danger">*</span></label>
                    <i @click="showConfirmation = !showConfirmation"
                        :class="showConfirmation ? 'fas fa-eye-slash' : 'fas fa-eye'" class="password-toggle-icon"></i>
                </div>
            </div>
        </div>

        <hr>
        <h5 class="mb-3 fw-bold text-secondary">Datos de tu Empresa</h5>

        {{-- Campo: Nombre de la Empresa --}}
        <div class="material-form-group-with-icon mb-4">
            <i class="fas fa-building fa-fw form-icon"></i>
            <input id="empresa_nombre" type="text" wire:model.live="empresa_nombre"
                class="material-form-control-with-icon @error('empresa_nombre') is-invalid @enderror" placeholder=" ">
            <label for="empresa_nombre" class="material-form-label">Nombre de la Empresa <span
                    class="text-danger">*</span></label>
            @error('empresa_nombre') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Campo: WhatsApp --}}
        <div class="material-form-group-with-icon mb-4">
            <i class="fab fa-whatsapp fa-fw form-icon"></i>
            <input id="empresa_telefono_whatsapp" type="text" wire:model.live="empresa_telefono_whatsapp"
                class="material-form-control-with-icon @error('empresa_telefono_whatsapp') is-invalid @enderror"
                placeholder=" " maxlength="9" x-init="...">
            <label for="empresa_telefono_whatsapp" class="material-form-label">WhatsApp <span
                    class="text-danger">*</span></label>
            @error('empresa_telefono_whatsapp') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Campo: Rubro --}}
        <div class="material-form-group-with-icon mb-4">
            <i class="fas fa-tag fa-fw form-icon"></i>
            <input id="empresa_rubro" type="text" wire:model.live="empresa_rubro"
                class="material-form-control-with-icon @error('empresa_rubro') is-invalid @enderror" placeholder=" ">
            <label for="empresa_rubro" class="material-form-label">Rubro <span class="text-danger">*</span></label>
            @error('empresa_rubro') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        {{-- Botón de envío --}}
        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled" wire:target="submit">
                <span wire:loading.remove wire:target="submit"><i class="fas fa-user-plus me-1"></i> Completar
                    Registro</span>
                <span wire:loading wire:target="submit"><span class="spinner-border spinner-border-sm"></span> Enviando
                    código...</span>
            </button>
        </div>
    </form>
</div>