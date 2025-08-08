<div class="container my-4 mx-auto" style="max-width: 900px;">
    <div class="card shadow">

        @if($step == 1)
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0 text-center text-md-start">Nuevo Registro</h4>
        </div>
        <div class="card-body">
            <div class="row g-4 align-items-center mt-0">
                <div class="col-12 col-md-4 text-center d-none d-md-block">
                    <img src="{{ asset('assets/img/usuario.gif') }}" alt="Icono de registro" class="img-fluid">
                </div>
                <div class="col-12 col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-bold">¿Qué deseas hacer?</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model.live="tipo_usuario"
                                id="tipo_cliente" value="cliente">
                            <label class="form-check-label" for="tipo_cliente">Quiero comprar</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model.live="tipo_usuario"
                                id="tipo_empresa" value="empresa">
                            <label class="form-check-label" for="tipo_empresa">Quiero vender</label>
                        </div>
                        @error('tipo_usuario') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    @if($tipo_usuario === 'cliente')
                    @livewire('auth.register.register-client', key('client-form'))
                    @elseif($tipo_usuario === 'empresa')
                    @livewire('auth.register.register-company', key('company-form'))
                    @endif
                </div>
            </div>
        </div>
        @elseif($step == 2)
        @include('livewire.auth.register.verify-code-step')
        @endif

        <div class="card-footer text-center py-3">
            <small>¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="fw-bold">Inicia Sesión aquí</a></small>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('verificationForm', () => ({
        handleInput(index) {
            let currentInput = this.$refs['codeInput' + index];
            if (!currentInput) return;
            let value = currentInput.value;

            // Si se introduce algo que no es un número, se borra.
            if (!/^[0-9]$/.test(value)) {
                currentInput.value = '';
                @this.set('verification_code.' + index, '');
                return;
            }

            // Si se introduce un número y no es el último campo, saltar al siguiente.
            if (value.length === 1 && index < 5) {
                this.$refs['codeInput' + (index + 1)].focus();
            }
        },

        handleBackspace(index) {
            let currentInput = this.$refs['codeInput' + index];
            if (!currentInput) return;
            // Si se presiona retroceso en un campo vacío y no es el primero, saltar al anterior.
            if (currentInput.value === '' && index > 0) {
                this.$refs['codeInput' + (index - 1)].focus();
            }
        },

        handlePaste(event) {
            const pasteData = (event.clipboardData || window.clipboardData).getData('text').trim();
            if (/^[0-9]+$/.test(pasteData)) {
                event.preventDefault();
                const digits = pasteData.slice(0, 6).split('');

                digits.forEach((digit, i) => {
                    if (this.$refs['codeInput' + i]) {
                        this.$refs['codeInput' + i].value = digit;
                        // ¡@this FUNCIONA AQUÍ!
                        @this.set('verification_code.' + i, digit);
                    }
                });

                const focusIndex = Math.min(digits.length, 5);
                if (this.$refs['codeInput' + focusIndex]) {
                    this.$refs['codeInput' + focusIndex].focus();
                }
            }
        }
    }));
});
</script>

@endpush