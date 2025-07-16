@extends('autenticacion.app')
@section('titulo', 'Sistema - Recuperar Contraseña')

@section('contenido')
<div class="container my-4 mx-auto" style="max-width: 900px;">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0 text-center text-md-start">Recuperar Contraseña</h4>
    </div>
    <div class="card-body">
      {{-- COMIENZA LA ESTRUCTURA DE 2 COLUMNAS --}}
      <div class="row g-4 align-items-center mt-3">

        <!-- Columna de la imagen -->
        <div class="col-12 col-md-4 text-center">
          {{-- Puedes cambiar esta imagen por una más adecuada para "recuperar contraseña", como una llave o un candado --}}
          <img src="{{ asset('assets/img/usuario.gif') }}" alt="Icono de recuperación" class="img-fluid" style="max-height: 200px;">
        </div>

        <!-- Columna del formulario -->
        <div class="col-12 col-md-8">
          {{-- TU FORMULARIO DE RECUPERACIÓN VA AQUÍ DENTRO --}}
          <p class="text-muted">
            Ingresa tu correo electrónico y te enviaremos un enlace para que puedas restablecer tu contraseña.
          </p>
          
          <form action="{{ route('password.send-link') }}" method="POST">
            @csrf

            {{-- Mensaje de error de sesión (si existe) --}}
            @if(session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Mensaje de éxito/informativo de sesión --}}
            @if(Session::has('mensaje'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ Session::get('mensaje') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif
             @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif


            {{-- Campo de Email --}}
            <div class="mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror" placeholder="ejemplo@correo.com" required>
              </div>
              @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            {{-- Botón de Enviar --}}
            <div class="d-grid mt-4">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-1"></i> Enviar enlace de recuperación
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