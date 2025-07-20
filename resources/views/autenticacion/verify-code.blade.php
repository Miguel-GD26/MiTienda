@extends('autenticacion.app')
@section('titulo', 'Sistema - Verificar Cuenta')

@push('estilos')
<style>
    .verification-code-input {
        width: 45px;
        height: 50px;
        font-size: 1.5rem;
        text-align: center;
        border: 2px solid #dee2e6;
        border-radius: .5rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .verification-code-input:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, .25);
        outline: 0;
    }
</style>
@endpush

@section('contenido')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5 text-center">                    
                    <i class="fa-solid fa-envelope-circle-check fa-5x text-primary mb-4"></i>
                    <h2 class="h3 mb-3">¡Revisa tu correo!</h2>
                    
                    @if (session('mensaje'))
                        <div class="alert alert-success">
                            {{ session('mensaje') }}
                        </div>
                    @endif
                    @if (session('mensaje_registro'))
                        <div class="alert alert-success">
                            {{ session('mensaje_registro') }}
                        </div>
                    @endif

                    <p class="text-muted">
                        Introduce el código de 6 dígitos que hemos enviado a <br><strong>{{ session('email') }}</strong>.
                    </p>

                    <div x-data="verificationForm()">
                        <form method="POST" action="{{ route('verification.code.verify') }}" @submit.prevent="submitForm">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('email') }}">
                            <input type="hidden" name="verification_code" x-model="fullCode">
                            <div class="d-flex justify-content-center gap-2 my-4" @paste.window="handlePaste($event)">
                                <template x-for="(digit, index) in code" :key="index">
                                    <input type="text" 
                                           maxlength="1" 
                                           class="form-control verification-code-input"
                                           x-ref="codeInput" {{-- El x-ref ahora es el mismo para todos --}}
                                           x-model="code[index]"
                                           @input.prevent="handleInput(index, $event.target)"
                                           @keydown.backspace="handleBackspace(index, $event.target)"
                                           pattern="[0-9]"
                                           inputmode="numeric"
                                           autocomplete="one-time-code">
                                </template>
                            </div>
                            
                            @error('verification_code')
                                <div class="text-danger small mb-3">{{ $message }}</div>
                            @enderror

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa-solid fa-check-circle me-1"></i> Verificar y Continuar
                                </button>
                            </div>
                        </form>
                    </div>

                    <hr class="my-4">
                    <div x-data="resendTimer()"
                         x-init="init('{{ session('email') }}', '{{ route('verification.code.resend') }}')">
                        
                        <p class="small text-muted mb-0">
                            ¿No recibiste el código?
                            <button type="button" @click="resendCode" :disabled="countdown > 0 || loading" class="btn btn-link p-0 border-0 align-baseline text-decoration-none">
                                <span x-show="!loading">Reenviar código</span>
                                <span x-show="loading">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Enviando...
                                </span>
                            </button>
                            <span x-show="countdown > 0 && !loading" x-text="` (intenta de nuevo en ${countdown}s)`"></span>
                        </p>
                        <div x-show="message" :class="messageType === 'success' ? 'text-success' : 'text-danger'" class="small mt-2" x-text="message"></div>
                    </div>

                </div>
                <div class="card-footer text-center py-3">
                    <a href="{{ route('login') }}">Volver al inicio de sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function verificationForm() {
    return {
        code: Array(6).fill(''),
        get fullCode() { return this.code.join(''); },

        handleInput(currentIndex, currentInput) {
            let value = currentInput.value;
            if (!/^[0-9]$/.test(value)) {
                this.code[currentIndex] = '';
                return;
            }
            
            if (value && currentIndex < 5) {
                let nextSibling = currentInput.nextElementSibling;
                if (nextSibling) {
                    nextSibling.focus();
                }
            }
        },

        handleBackspace(currentIndex, currentInput) {
            if (currentInput.value === '' && currentIndex > 0) {
                let prevSibling = currentInput.previousElementSibling;
                if (prevSibling) {
                    prevSibling.focus();
                }
            }
        },

        handlePaste(e) {
            const pasteData = e.clipboardData.getData('text').trim().slice(0, 6);
            if (/^[0-9]{6}$/.test(pasteData)) {
                e.preventDefault();
                this.code = pasteData.split('');
                this.$nextTick(() => {
                    const allInputs = e.target.querySelectorAll('.verification-code-input');
                    if (allInputs.length === 6) {
                        allInputs[5].focus();
                    }
                });
            }
        },

        submitForm(e) {
            if (this.fullCode.length === 6) { e.target.submit(); } 
            else { alert('Por favor, introduce los 6 dígitos del código.'); }
        }
    };
}

function resendTimer() {
    return {
        countdown: 60,
        canResend: false,
        loading: false,
        message: '',
        messageType: 'success',
        email: '',
        resendUrl: '',
        init(email, resendUrl) {
            this.email = email;
            this.resendUrl = resendUrl;
            this.startCountdown();
        },
        startCountdown() {
            this.canResend = false;
            let timer = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) {
                    clearInterval(timer);
                    this.canResend = true;
                    this.countdown = 0;
                }
            }, 1000);
        },
        async resendCode() {
            if (!this.canResend || this.loading) return;
            this.loading = true;
            this.message = '';
            const url = this.resendUrl + '?email=' + encodeURIComponent(this.email);
            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                const data = await response.json();
                if (response.ok) {
                    this.message = data.mensaje;
                    this.messageType = 'success';
                } else {
                    this.message = data.message || 'Ocurrió un error al reenviar el código.';
                    this.messageType = 'error';
                }
            } catch (error) {
                console.error('Error en la petición de reenvío:', error);
                this.message = 'No se pudo conectar con el servidor. Intenta de nuevo.';
                this.messageType = 'error';
            } finally {
                this.loading = false;
                if (this.messageType === 'success') {
                    this.countdown = 60;
                    this.startCountdown();
                }
            }
        }
    };
}
</script>
@endpush