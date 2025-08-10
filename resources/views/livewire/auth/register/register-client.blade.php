<div>
    {{-- Indicador de carga --}}
    @if($isLoading)
    <div class="position-absolute w-100 h-100 top-0 start-0 bg-white bg-opacity-75 d-flex align-items-center justify-content-center rounded"
        style="z-index: 10;">
        <div class="text-center">
            <img src="{{ asset('assets/img/codigo.gif') }}" alt="Enviando correo..."
                style="width: 150px; height: auto;">
            <p class="mt-2 mb-0 fw-bold text-primary">Enviando código de verificación...</p>
        </div>
    </div>
    @endif

    {{-- Formulario con el nuevo estilo --}}
    <form wire:submit.prevent="submit" novalidate class="position-relative">
        @csrf
        <hr>
        <p class="text-muted small">Los campos con <span class="text-danger">*</span> son obligatorios.</p>
        <h5 class="mb-3 fw-bold text-secondary">Datos de tu cuenta</h5>

        {{-- Campo: Nombre Completo --}}
        <div class="material-form-group-with-icon mb-4">
            <i class="fas fa-user fa-fw form-icon"></i>
            <input id="name" type="text" wire:model.live="name"
                class="material-form-control-with-icon @error('name') is-invalid @enderror" placeholder=" ">
            <label for="name" class="material-form-label">Nombre completo <span class="text-danger">*</span></label>
            @error('name')
            <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo: Correo Electrónico --}}
        <div class="material-form-group-with-icon mb-4">
            <i class="fas fa-envelope fa-fw form-icon"></i>
            <input id="email" type="email" wire:model.live="email"
                class="material-form-control-with-icon @error('email') is-invalid @enderror" placeholder=" ">
            <label for="email" class="material-form-label">Correo electrónico <span class="text-danger">*</span></label>
            @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Fila para las Contraseñas --}}
        <div class="row" x-data="{ showPassword: false, showConfirmation: false }">

            {{-- Campo: Contraseña con botón de visibilidad --}}
            <div class="col-md-6">
                <div class="material-form-group-with-icon mb-4">
                    <i class="fas fa-lock fa-fw form-icon"></i>
                    <input id="password" :type="showPassword ? 'text' : 'password'" {{-- Tipo dinámico con Alpine --}}
                        wire:model.live="password"
                        class="material-form-control-with-icon @error('password') is-invalid @enderror" placeholder=" ">
                    <label for="password" class="material-form-label">Contraseña <span
                            class="text-danger">*</span></label>

                    {{-- Botón para alternar visibilidad --}}
                    <i @click="showPassword = !showPassword" :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"
                        class="password-toggle-icon" aria-label="Mostrar u ocultar contraseña"></i>

                    @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Campo: Confirmar Contraseña con botón de visibilidad --}}
            <div class="col-md-6">
                <div class="material-form-group-with-icon mb-4">
                    <i class="fas fa-lock fa-fw form-icon"></i>
                    <input id="password_confirmation" :type="showConfirmation ? 'text' : 'password'"
                        {{-- Tipo dinámico con Alpine --}} wire:model.live="password_confirmation"
                        class="material-form-control-with-icon" placeholder=" ">
                    <label for="password_confirmation" class="material-form-label">Confirmar contraseña <span
                            class="text-danger">*</span></label>

                    {{-- Botón para alternar visibilidad --}}
                    <i @click="showConfirmation = !showConfirmation"
                        :class="showConfirmation ? 'fas fa-eye-slash' : 'fas fa-eye'" class="password-toggle-icon"
                        aria-label="Mostrar u ocultar contraseña"></i>
                </div>
            </div>
        </div>

        {{-- Campo: Teléfono --}}
        <div class="material-form-group-with-icon mb-4">
            <i class="fas fa-phone fa-fw form-icon"></i>
            <input id="cliente_telefono" type="text" wire:model.live="cliente_telefono"
                class="material-form-control-with-icon @error('cliente_telefono') is-invalid @enderror" placeholder=" "
                maxlength="9" x-init="
                    $el.addEventListener('input', () => { $el.value = $el.value.replace(/\D/g, '') });
                    $el.addEventListener('paste', (e) => {
                        e.preventDefault();
                        const text = (e.clipboardData || window.clipboardData).getData('text');
                        $el.value = text.replace(/\D/g, '').slice(0, 9);
                        $el.dispatchEvent(new Event('input'));
                    });
                ">
            <label for="cliente_telefono" class="material-form-label">Teléfono <span
                    class="text-danger">*</span></label>
            @error('cliente_telefono')
            <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Botón de envío --}}
        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled" wire:target="submit">
                <span wire:loading.remove wire:target="submit">
                    <i class="fas fa-user-plus me-1"></i> Crear mi Cuenta
                </span>
                <span wire:loading wire:target="submit">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Enviando código...
                </span>
            </button>
        </div>
    </form>
</div>