<div class="container my-4 mx-auto" style="max-width: 900px;">
    <div class="card shadow position-relative">
        <div wire:loading.class.remove="d-none" wire:target="sendResetLink" class="d-none position-absolute w-100 h-100 top-0 start-0 bg-white bg-opacity-75 d-flex align-items-center justify-content-center rounded" style="z-index: 10;">
            <div class="text-center">
                <img src="{{ asset('assets/img/codigo.gif') }}" alt="Enviando correo..." style="width: 150px; height: auto;">
                <p class="mt-2 mb-0 fw-bold text-primary">Enviando correo...</p>
            </div>
        </div>
        
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0 text-center text-md-start">Recuperar Contraseña</h4>
        </div>
        <div class="card-body">
            <div class="row g-4 align-items-center mt-3">
                <div class="col-12 col-md-4 text-center">
                    <img src="{{ asset('assets/img/usuario.gif') }}" alt="Icono" class="img-fluid" style="max-height: 200px;">
                </div>
                <div class="col-12 col-md-8 position-relative"> {{-- Añadido position-relative --}}
                    @if ($sent)
                        <div class="alert alert-success">
                            <h5 class="alert-heading">¡Correo enviado!</h5>
                            <p>Hemos enviado un enlace de recuperación a <strong>{{ $email }}</strong>. Por favor, revisa tu bandeja de entrada y la carpeta de spam.</p>
                            <hr>
                            <p class="mb-0">Si no lo recibes en unos minutos, puedes intentarlo de nuevo.</p>
                        </div>
                    @else
                        <p class="text-muted">Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.</p>
                        <form wire:submit.prevent="sendResetLink">
                            @csrf
                            <div class="material-form-group-with-icon mb-4">
                                <i class="fas fa-envelope fa-fw form-icon"></i>
                                <input id="email" type="email" wire:model.live="email" class="material-form-control-with-icon @error('email') is-invalid @enderror" placeholder=" ">
                                <label for="email" class="material-form-label">Correo electrónico</label>
                                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:loading.class="opacity-75">
                                    <span wire:loading.remove wire:target="sendResetLink">
                                        <i class="fas fa-paper-plane me-1"></i> Enviar enlace
                                    </span>
                                    <span wire:loading wire:target="sendResetLink">
                                        <span class="spinner-border spinner-border-sm"></span> Enviando...
                                    </span>
                                </button>
                            </div>
                        </form>
                    @endif
                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-decoration-none">Volver al inicio de sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>