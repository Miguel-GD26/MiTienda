<form wire:submit.prevent="authenticate">
    
    <div class="text-center mb-5">
        <img src="{{ asset('assets/img/login.png') }}" alt="Logo" style="width: 70px;">
        <h3 class="fw-bold mt-3 mb-1">Bienvenido</h3>
        <p class="text-muted">Introduce tus credenciales para acceder.</p>
    </div>

    {{-- CAMPO: CORREO ELECTRÓNICO --}}
    <div class="input-div one mb-2">
        <div class="i"><i class="fas fa-user"></i></div>
        <div class="div">
            {{-- wire:model.blur valida el campo cuando el usuario sale de él --}}
            <input type="email" wire:model.blur="email" class="input @error('email') is-invalid @enderror"
                placeholder="Correo electrónico" required autocomplete="email">
        </div>
    </div>
    {{-- Mensaje de error específico para el campo email --}}
    @error('email') <span class="text-danger small d-block mb-3">{{ $message }}</span> @enderror

    {{-- CAMPO: CONTRASEÑA --}}
    <div class="input-div pass mb-2">
        <div class="i"><i class="fas fa-lock"></i></div>
        <div class="div">
            <input type="password" wire:model="password" class="input @error('password') is-invalid @enderror" 
                   placeholder="Contraseña" required autocomplete="current-password">
        </div>
    </div>
    {{-- Mensaje de error específico para el campo contraseña --}}
    @error('password') <span class="text-danger small d-block mb-3">{{ $message }}</span> @enderror

    {{-- ENLACE: OLVIDASTE TU CONTRASEÑA --}}
    <div class="text-end mb-4">
        <a href="{{ route('password.request') }}" class="small text-decoration-none">¿Olvidaste tu contraseña?</a>
    </div>

    {{-- BOTÓN: ACCEDER CON ESTADO DE CARGA --}}
    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary btn-lg fw-bold" wire:loading.attr="disabled" wire:target="authenticate">
            {{-- Texto por defecto --}}
            <span wire:loading.remove wire:target="authenticate">
                ACCEDER
            </span>
            {{-- Texto y spinner mientras se procesa el login --}}
            <span wire:loading wire:target="authenticate">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Accediendo...
            </span>
        </button>
    </div>

    {{-- SEPARADOR --}}
    <div class="text-center my-3">
        <small class="text-muted">o</small>
    </div>
    
    {{-- BOTÓN: LOGIN CON GOOGLE --}}
    <div class="d-grid">
        <a href="{{ route('login.google.redirect') }}" class="btn btn-light border d-flex align-items-center justify-content-center gap-2 shadow-sm" style="height: 45px;">
            <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google logo" style="width: 20px; height: 20px;">
            <span>Continuar con Google</span>
        </a>
    </div>

    {{-- ENLACE: REGISTRO --}}
    <div class="text-center mt-5">
        <p class="text-muted mb-0">¿No tienes una cuenta?
            <a href="{{ route('registro') }}" class="fw-bold text-decoration-none">Regístrate</a>
        </p>
    </div>
</form>