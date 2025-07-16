@extends('autenticacion.app')
@section('titulo', 'Sistema - Restablecer Contraseña')

@section('contenido')
<div class="container my-4 mx-auto" style="max-width: 900px;">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0 text-center text-md-start">Restablecer la Contraseña</h4>
    </div>
    <div class="card-body">
      {{-- COMIENZA LA ESTRUCTURA DE 2 COLUMNAS --}}
      <div class="row g-4 align-items-center mt-3">

        <!-- Columna de la imagen -->
        <div class="col-12 col-md-4 text-center">
          <img src="{{ asset('assets/img/usuario.gif') }}" alt="Icono de seguridad" class="img-fluid" style="max-height: 200px;">
        </div>

        <!-- Columna del formulario -->
        <div class="col-12 col-md-8">
          <p class="text-muted">
            Por favor, confirma tu correo electrónico y elige una nueva contraseña.
          </p>

          <form action="{{ route('password.update') }}" method="POST">
            @csrf
            {{-- Este campo oculto es esencial para que el proceso funcione --}}
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Mensaje de error de sesión (si existe) --}}
            @if(session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Campo de Email (AHORA EDITABLE) --}}
            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                {{-- CAMBIO PRINCIPAL: Se ha quitado 'readonly' para que el campo sea editable --}}
                <input id="email" type="email" name="email" value="{{ request()->email ?? old('email') }}"
                       class="form-control @error('email') is-invalid @enderror" placeholder="Escribe tu correo electrónico" required>
              </div>
              @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            {{-- Campo de Nueva Contraseña --}}
            <div class="mb-3">
              <label for="password" class="form-label">Nueva Contraseña</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres" required>
              </div>
              @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            {{-- Campo de Confirmar Contraseña --}}
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Confirmar nueva contraseña</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="form-control" placeholder="Repita la nueva contraseña" required>
              </div>
            </div>

            {{-- Botón de Actualizar --}}
            <div class="d-grid mt-4">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Actualizar Contraseña
              </button>
            </div>
          </form>
          {{-- FIN DEL FORMULARIO --}}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection