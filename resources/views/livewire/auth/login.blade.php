<form wire:submit.prevent="authenticate">
    
    <div class="text-center mb-5">
        <img src="{{ asset('assets/img/login.png') }}" alt="Logo" style="width: 70px;">
        <h3 class="fw-bold mt-3 mb-1">Bienvenido</h3>
        <p class="text-muted">Introduce tus credenciales para acceder.</p>
    </div>

    {{-- CAMPO: CORREO ELECTRÓNICO (NUEVO ESTILO) --}}
    <div class="material-form-group-with-icon mb-4">
        <i class="fas fa-user fa-fw form-icon"></i>
        <input 
            id="email" 
            type="email" 
            wire:model.live="email"
            class="material-form-control-with-icon @error('email') is-invalid @enderror"
            placeholder=" "
            autocomplete="email"
        >
        <label for="email" class="material-form-label">Correo electrónico</label>
        @error('email') 
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- CAMPO: CONTRASEÑA (NUEVO ESTILO CON VISIBILIDAD) --}}
    <div class="material-form-group-with-icon mb-4" x-data="{ show: false }">
        <i class="fas fa-lock fa-fw form-icon"></i>
        <input 
            id="password" 
            :type="show ? 'text' : 'password'"
            wire:model.live="password"
            class="material-form-control-with-icon @error('password') is-invalid @enderror"
            placeholder=" "
            autocomplete="current-password"
        >
        <label for="password" class="material-form-label">Contraseña</label>
        
        {{-- Botón para alternar visibilidad --}}
        <i 
            @click="show = !show" 
            :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'" 
            class="password-toggle-icon"
            aria-label="Mostrar u ocultar contraseña"
        ></i>

        @error('password') 
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- ENLACE: OLVIDASTE TU CONTRASEÑA --}}
    <div class="text-end mb-4">
        <a href="{{ route('password.request') }}" class="small text-decoration-none">¿Olvidaste tu contraseña?</a>
    </div>

    {{-- BOTÓN: ACCEDER CON ESTADO DE CARGA --}}
    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary btn-lg fw-bold" wire:loading.attr="disabled" wire:target="authenticate">
            <span wire:loading.remove wire:target="authenticate">ACCEDER</span>
            <span wire:loading wire:target="authenticate">
                <span class="spinner-border spinner-border-sm"></span> Accediendo...
            </span>
        </button>
    </div>

    {{-- SEPARADOR --}}
    <div class="text-center my-3">
        <small class="text-muted">o</small>
    </div>
    
    {{-- BOTÓN: LOGIN CON GOOGLE --}}
    <div class="d-grid">
        <a href="{{ route('login.google') }}" class="btn btn-light border d-flex align-items-center justify-content-center gap-2 shadow-sm" style="height: 45px;">
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