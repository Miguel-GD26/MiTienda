@extends('autenticacion.app')
@section('titulo', 'Sistema - Login')

@section('contenido')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  {{-- USAMOS TU ARCHIVO CSS (con los cambios que acabamos de hacer) --}}
  @vite(['resources/css/login.css'])

  <video autoplay loop muted playsinline controlslist="nodownload" class="video-background d-none d-lg-block">
    <source src="{{ asset('assets/video/fondo.mp4') }}" type="video/mp4">
  </video>

  <div class="login-page-container">
      <div class="left-video-spacer d-none d-lg-block"></div>

      <div class="login-form-column">
          <div class="login-content">
              
              <form action="{{ route('login.post') }}" method="POST">
                  @csrf
                  @if(request()->has('redirect'))
                      <input type="hidden" name="redirect" value="{{ request()->query('redirect') }}">
                  @endif
                  <div class="text-center mb-5">
                      <img src="{{ asset('assets/img/login.png') }}" alt="Logo" style="width: 70px;">
                      <h3 class="fw-bold mt-3 mb-1">Bienvenido</h3>
                      <p class="text-muted">Introduce tus credenciales para acceder.</p>
                  </div>

                  @if(session('error'))
                    <div class="alert alert-danger text-start small p-2 mb-3">{{ session('error') }}</div>
                  @endif

                  {{-- Tus input-div con más espaciado --}}
                  <div class="input-div one mb-4">
                      <div class="i"><i class="fas fa-user"></i></div>
                      <div class="div">
                          <input type="email" name="email" value="{{ old('email') }}" class="input" placeholder="Correo electrónico" required>
                      </div>
                  </div>

                  <div class="input-div pass mb-3">
                      <div class="i"><i class="fas fa-lock"></i></div>
                      <div class="div">
                          <input type="password" name="password" class="input" placeholder="Contraseña" required>
                      </div>
                  </div>

                  <div class="text-end mb-4">
                     <a href="{{ route('password.request') }}" class="small text-decoration-none">¿Olvidaste tu contraseña?</a>
                  </div>

                  <div class="d-grid mb-3">
                      <button type="submit" class="btn btn-primary btn-lg fw-bold">ACCEDER</button>
                  </div>

                  {{-- Integración del login social --}}
                  <div class="text-center my-3">
                      <small class="text-muted">o</small>
                  </div>
                  
                  <div class="d-grid">
                      <a href="#" class="btn btn-outline-secondary">
                          <i class="fab fa-google me-2"></i> Continuar con Google
                      </a>
                  </div>

                  <div class="text-center mt-5">
                      <p class="text-muted mb-0">¿No tienes una cuenta? 
                          <a href="{{ route('registro') }}" class="fw-bold text-decoration-none">Regístrate</a>
                      </p>
                  </div>
              </form>
          </div>
      </div>
  </div>
@endsection