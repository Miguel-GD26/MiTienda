
<div class="container my-4 mx-auto" style="max-width: 900px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0 text-center text-md-start">Casi listo, ¡completa tu perfil!</h4>
        </div>
        <div class="card-body p-4">
            <div class="alert alert-info">
                <p class="fw-bold mb-1">Bienvenido, {{ $name }}</p>
                <p class="mb-0 small">Tu correo <strong>{{ $email }}</strong> se ha verificado con Google. Solo
                    necesitamos unos datos más para crear tu cuenta.</p>
            </div>

            <form wire:submit="finalizeRegistration" class="mt-4">
                <div class="mb-4">
                    <label class="form-label fw-bold">¿Qué deseas hacer?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" wire:model.live="tipoUsuario" id="tipo_cliente"
                            value="cliente">
                        <label class="form-check-label" for="tipo_cliente">Quiero comprar (Ser Cliente)</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" wire:model.live="tipoUsuario" id="tipo_empresa"
                            value="empresa">
                        <label class="form-check-label" for="tipo_empresa">Quiero vender (Registrar mi Empresa)</label>
                    </div>
                    @error('tipoUsuario') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                @if ($tipoUsuario)
                <div x-data x-transition>
                    <hr>
                    <p class="text-muted small">Los campos con <span class="text-danger">*</span> son obligatorios.</p>

                    @if ($tipoUsuario === 'cliente')
                    <div x-transition>
                        <h5 class="mb-3 fw-bold text-secondary">Datos de tu cuenta</h5>
                        <div class="material-form-group-with-icon mb-4">
                            {{-- 1. Añadimos un ícono de teléfono --}}
                            <i class="fas fa-phone fa-fw form-icon"></i>

                            {{-- 2. El Input, con las clases correctas y el x-init --}}
                            <input 
                                id="cliente_telefono" 
                                type="text" 
                                wire:model.live="cliente_telefono"
                                class="material-form-control-with-icon @error('cliente_telefono') is-invalid @enderror"
                                placeholder=" " {{-- Placeholder vacío es clave --}}
                                maxlength="9"
                                x-init="
                                    $el.addEventListener('input', () => { 
                                        $el.value = $el.value.replace(/\D/g, ''); 
                                    });
                                    $el.addEventListener('paste', (e) => {
                                        e.preventDefault();
                                        const text = (e.clipboardData || window.clipboardData).getData('text');
                                        $el.value = text.replace(/\D/g, '').slice(0, 9);
                                        $el.dispatchEvent(new Event('input'));
                                    });
                                "
                            >
                            
                            {{-- 3. La Etiqueta flotante --}}
                            <label for="cliente_telefono" class="material-form-label">
                                Teléfono <span class="text-danger">*</span>
                            </label>

                            {{-- 4. El Mensaje de Error --}}
                            @error('cliente_telefono') 
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @endif

                    @if ($tipoUsuario === 'empresa')
                    <div x-transition>
                        <h5 class="mb-3 fw-bold text-secondary">Datos de tu Empresa</h5>
                        <div class="material-form-group-with-icon mb-4">
                            {{-- 1. Cambia el ícono a uno de edificio --}}
                            <i class="fas fa-building fa-fw form-icon"></i>

                            {{-- 2. El Input, adaptado para 'empresa_nombre' --}}
                            <input 
                                id="empresa_nombre" 
                                type="text" 
                                wire:model.blur="empresa_nombre"
                                class="material-form-control-with-icon @error('empresa_nombre') is-invalid @enderror"
                                placeholder=" " {{-- Placeholder vacío es clave --}}
                            >
                            
                            {{-- 3. La Etiqueta, adaptada para 'empresa_nombre' --}}
                            <label for="empresa_nombre" class="material-form-label">
                                Nombre de la Empresa <span class="text-danger">*</span>
                            </label>

                            {{-- 4. El Mensaje de Error, adaptado para 'empresa_nombre' --}}
                            @error('empresa_nombre') 
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="material-form-group-with-icon mb-4">
                            {{-- 1. El Ícono (posicionado absolutamente) --}}
                            <i class="fab fa-whatsapp fa-fw form-icon"></i>

                            {{-- 2. El Input (con padding para dejar espacio al ícono) --}}
                            <input 
                                id="empresa_telefono_whatsapp" 
                                type="text" 
                                wire:model.blur="empresa_telefono_whatsapp"
                                class="material-form-control-with-icon @error('empresa_telefono_whatsapp') is-invalid @enderror"
                                placeholder=" " {{-- Placeholder vacío es clave --}}
                                maxlength="9"
                                x-init="
                                    $el.addEventListener('input', () => { 
                                        $el.value = $el.value.replace(/\D/g, ''); 
                                    });
                                    $el.addEventListener('paste', (e) => {
                                        e.preventDefault();
                                        const text = (e.clipboardData || window.clipboardData).getData('text');
                                        $el.value = text.replace(/\D/g, '').slice(0, 9);
                                        $el.dispatchEvent(new Event('input'));
                                    });
                                "
                            >
                            
                            {{-- 3. La Etiqueta (posicionada absolutamente) --}}
                            <label for="empresa_telefono_whatsapp" class="material-form-label">
                                WhatsApp <span class="text-danger">*</span>
                            </label>

                            {{-- 4. El Mensaje de Error --}}
                            @error('empresa_telefono_whatsapp') 
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="material-form-group-with-icon mb-4">
                            {{-- 1. El Ícono (posicionado absolutamente) --}}
                            <i class="fas fa-tag fa-fw form-icon"></i>

                            {{-- 2. El Input (con padding para dejar espacio al ícono) --}}
                            <input 
                                id="empresa_rubro" 
                                type="text" 
                                wire:model.blur="empresa_rubro"
                                class="material-form-control-with-icon @error('empresa_rubro') is-invalid @enderror"
                                placeholder=" " {{-- Placeholder vacío es clave --}}
                            >
                            
                            {{-- 3. La Etiqueta (posicionada absolutamente) --}}
                            <label for="empresa_rubro" class="material-form-label">
                                Rubro <span class="text-danger">*</span>
                            </label>

                            {{-- 4. El Mensaje de Error --}}
                            @error('empresa_rubro') 
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @endif

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fas fa-check-circle me-1"></i> Finalizar Registro</span>
                            <span wire:loading><span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                                Procesando...</span>
                        </button>
                    </div>
                </div>
                @endif
            </form>
            @error('general')<div class="alert alert-danger mt-3">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

{{-- No necesitas la sección @push('scripts') para esta funcionalidad --}}