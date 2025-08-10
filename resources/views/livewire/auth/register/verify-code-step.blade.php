<div class="card-body p-4 p-md-5 text-center">
    <i class="fa-solid fa-envelope-circle-check fa-5x text-primary mb-4"></i>
    <h2 class="h3 mb-3">¡Revisa tu correo!</h2>
    @if(!empty($registrationData['email']))
    <p class="text-muted">Introduce el código de 6 dígitos que enviamos a
        <br><strong>{{ $registrationData['email'] }}</strong>.</p>
    @endif

    <div x-data="verificationForm()">
        <form wire:submit.prevent="verifyCode">
            @csrf
            <div class="d-flex justify-content-center gap-2 my-4" @paste.window="handlePaste($event)">
                @for ($i = 0; $i < 6; $i++) <input type="text" maxlength="1"
                    class="form-control verification-code-input @error('verification_code.' . $i) is-invalid @enderror"
                    wire:model.defer="verification_code.{{$i}}" x-ref="codeInput{{$i}}" @input="handleInput({{ $i }})"
                    @keydown.backspace="handleBackspace({{ $i }})" pattern="[0-9]*" inputmode="numeric"
                    autocomplete="one-time-code" aria-label="Dígito {{ $i + 1 }}">
                    @endfor
            </div>

            @error('verification_code.0') <div class="text-danger small mb-3">{{ $message }}</div> @enderror

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled"
                    wire:target="verifyCode">
                    <span wire:loading.remove wire:target="verifyCode">Verificar y Continuar</span>
                    <span wire:loading wire:target="verifyCode">Procesando...</span>
                </button>
            </div>
        </form>
    </div>

    <hr class="my-4">
    <div wire:poll.1s="decrementCountdown">
        <p class="small text-muted mb-0">
            ¿No recibiste el código?
            <button type="button" wire:click="resendCode" @if(!$canResend) disabled @endif
                class="btn btn-link p-0 border-0 align-baseline text-decoration-none">
                <span wire:loading.remove wire:target="resendCode">Reenviar código</span>
                <span wire:loading wire:target="resendCode">Enviando...</span>
            </button>
            @if(!$canResend && $countdown > 0)
            <span class="text-muted">(intenta de nuevo en {{ $countdown }}s)</span>
            @endif
        </p>
    </div>
    <a href="#" wire:click.prevent="backToForm" class="small mt-3 d-inline-block">Volver y corregir mis datos</a>
</div>