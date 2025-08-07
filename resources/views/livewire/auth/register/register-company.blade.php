<div>

        @if($isLoading)
            <div class="position-absolute w-100 h-100 top-0 start-0 bg-white bg-opacity-75 d-flex align-items-center justify-content-center rounded" style="z-index: 10;">
                <div class="text-center">
                    <img src="{{ asset('assets/img/codigo.gif') }}" alt="Enviando correo..." style="width: 180px; height: auto;">
                    <p class="mt-2 mb-0 fw-bold text-primary">Enviando código de verificación...</p>
                </div>
            </div>
        @endif

    <form wire:submit.prevent="submit" novalidate class="position-relative">
        <hr>
        <h5 class="mb-3 fw-bold text-secondary">Datos de tu cuenta</h5>

        <div class="mb-3">
            <label for="name" class="form-label">Nombre completo<span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user fa-fw"></i></span>
                <input id="name" type="text" wire:model.blur="name" class="form-control @error('name') is-invalid @enderror" placeholder="Ej. Juan Pérez">
            </div>
            @error('name') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico<span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope fa-fw"></i></span>
                <input id="email" type="email" wire:model.blur="email" class="form-control @error('email') is-invalid @enderror" placeholder="ejemplo@correo.com">
            </div>
            @error('email') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Contraseña<span class="text-danger">*</span></label>
                <div x-data="{ show: false }" class="input-group">
                    <input id="password" :type="show ? 'text' : 'password'" wire:model.blur="password" class="form-control @error('password') is-invalid @enderror">
                    <button @click="show = !show" type="button" class="btn btn-outline-secondary" aria-label="Mostrar u ocultar contraseña">
                        <i class="fas fa-eye" x-show="!show"></i>
                        <i class="fas fa-eye-slash" x-show="show"></i>
                    </button>
                </div>
                @error('password') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label">Confirmar contraseña<span class="text-danger">*</span></label>
                <div x-data="{ show: false }" class="input-group">
                    <input id="password_confirmation" :type="show ? 'text' : 'password'" wire:model.blur="password_confirmation" class="form-control">
                    <button @click="show = !show" type="button" class="btn btn-outline-secondary" aria-label="Mostrar u ocultar contraseña">
                        <i class="fas fa-eye" x-show="!show"></i>
                        <i class="fas fa-eye-slash" x-show="show"></i>
                    </button>
                </div>
            </div>
        </div>

        <hr>
        <h5 class="mb-3 fw-bold text-secondary">Datos de tu Empresa</h5>

        <div class="mb-3">
            <label for="empresa_nombre" class="form-label">Nombre de la Empresa<span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-building fa-fw"></i></span>
                <input id="empresa_nombre" type="text" wire:model.blur="empresa_nombre" class="form-control @error('empresa_nombre') is-invalid @enderror" placeholder="Ej. Mi Tienda">
            </div>
            @error('empresa_nombre') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="empresa_telefono_whatsapp" class="form-label">WhatsApp<span class="text-danger">*</span></label>
             <div class="input-group">
                <span class="input-group-text"><i class="fab fa-whatsapp fa-fw"></i></span>
                <input id="empresa_telefono_whatsapp" type="text" wire:model.blur="empresa_telefono_whatsapp" class="form-control @error('empresa_telefono_whatsapp') is-invalid @enderror" placeholder="9 dígitos">
            </div>
            @error('empresa_telefono_whatsapp') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="empresa_rubro" class="form-label">Rubro<span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-tag fa-fw"></i></span>
                <input id="empresa_rubro" type="text" wire:model.blur="empresa_rubro" class="form-control @error('empresa_rubro') is-invalid @enderror" placeholder="Ej: Restaurante, Ropa">
            </div>
            @error('empresa_rubro') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled" wire:target="submit">
                <span wire:loading.remove wire:target="submit">
                    <i class="fas fa-user-plus me-1"></i> Completar Registro
                </span>
                <span wire:loading wire:target="submit">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Enviando código...
                </span>
            </button>
        </div>
    </form>

    {{-- Enlace para iniciar sesión --}}
    <div class="text-center mt-4">
        <small>¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="fw-bold">Inicia Sesión aquí</a></small>
    </div>
</div>
@script
<script>
    const inputEmpresa = document.querySelector('#empresa_telefono_whatsapp');

    if (inputEmpresa && !inputEmpresa.dataset.phoneFormatted) {
        
        const formatPhone = (el) => {
            el.value = el.value.replace(/\D/g, '').slice(0, 9);
        };
        
        const pastePhone = (e, el) => {
            e.preventDefault();
            const texto = (e.clipboardData || window.clipboardData).getData('text');
            const limpio = texto.replace(/\D/g, '').slice(0, 9);
            el.value = limpio;
            // Disparamos un evento 'input' para que wire:model se actualice
            el.dispatchEvent(new Event('input'));
        };

        inputEmpresa.addEventListener('input', () => formatPhone(inputEmpresa));
        inputEmpresa.addEventListener('paste', (e) => pastePhone(e, inputEmpresa));
        
        inputEmpresa.dataset.phoneFormatted = 'true';
    }
</script>
@endscript