@extends('autenticacion.app')
@section('titulo', 'Sistema - Registro')

@section('contenido')


<div class="container my-4 mx-auto" style="max-width: 900px;">
  
  @if(old('tipo_usuario'))
    <div class="card shadow" x-data="{ tipoUsuario: '{{ old('tipo_usuario') }}' }">
  @else
    <div class="card shadow" x-data="{ tipoUsuario: '' }">
  @endif

    <div class="card-header bg-primary text-white">
      <h4 class="mb-0 text-center text-md-start">Nuevo Registro</h4>
    </div>
    <div class="card-body">
      <div class="row g-4 align-items-center mt-0">

        <!-- Columna de la imagen -->
        <div class="col-12 col-md-4 text-center d-none d-md-block">
          <img src="{{ asset('assets/img/usuario.gif') }}" alt="Icono de registro" class="img-fluid">
        </div>

        <!-- Columna del formulario -->
        <div class="col-12 col-md-8">
          <form action="{{ route('registro.store') }}" method="POST">
            @csrf
            @if(request()->has('redirect'))
              <input type="hidden" name="redirect" value="{{ request()->query('redirect') }}">
            @endif
            @if(session('error'))
              <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Paso 1: Pregunta Inicial (Siempre visible) -->
            <div class="mb-3">
              <label class="form-label fw-bold">¿Qué deseas hacer?</label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_usuario" id="tipo_cliente" value="cliente" x-model="tipoUsuario">
                <label class="form-check-label" for="tipo_cliente">
                  Quiero comprar (Registrarme como Cliente)
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="tipo_usuario" id="tipo_empresa" value="empresa" x-model="tipoUsuario">
                <label class="form-check-label" for="tipo_empresa">
                  Quiero vender (Registrar mi Empresa)
                </label>
              </div>
              @error('tipo_usuario')
                  <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>
            

            <div x-show="tipoUsuario !== ''" x-transition>
              <hr>
              
              {{-- Para ser explícito, aquí está el resto: --}}
              <h5 class="mb-3">Datos de tu cuenta</h5>
              
              <div class="mb-3">
                <label for="name" class="form-label">Tu nombre completo</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-user"></i></span>
                  <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Ej. Juan Pérez" required>
                </div>
                @error('name')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Tu correo electrónico (para iniciar sesión)</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                  <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="ejemplo@correo.com" required>
                </div>
                @error('email')
                  <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="password" class="form-label">Contraseña</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres" required>
                  </div>
                  @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Repita la contraseña" required>
                  </div>
                </div>
              </div>

              <div x-show="tipoUsuario === 'cliente'" x-transition>
                <div class="mb-3">
                  <label for="cliente_telefono" class="form-label">Tu teléfono (Opcional)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input id="cliente_telefono" type="text" name="cliente_telefono" value="{{ old('cliente_telefono') }}" class="form-control @error('cliente_telefono') is-invalid @enderror" placeholder="Número de contacto">
                  </div>
                  @error('cliente_telefono')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <div x-show="tipoUsuario === 'empresa'" x-transition>
                <hr>
                <h5 class="mb-3">Datos de tu Empresa</h5>

                <div class="mb-3">
                  <label for="empresa_nombre" class="form-label">Nombre de la Empresa</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                    <input id="empresa_nombre" type="text" name="empresa_nombre" value="{{ old('empresa_nombre') }}" class="form-control @error('empresa_nombre') is-invalid @enderror" placeholder="Ej. Mi Tienda Fantástica">
                  </div>
                  @error('empresa_nombre')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
                </div>
                
                <div class="mb-3">
                    <label for="empresa_telefono_whatsapp" class="form-label">Teléfono / WhatsApp de la Empresa</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                        <input id="empresa_telefono_whatsapp" type="text" name="empresa_telefono_whatsapp" value="{{ old('empresa_telefono_whatsapp') }}" class="form-control @error('empresa_telefono_whatsapp') is-invalid @enderror" placeholder="Número de contacto de la empresa">
                    </div>
                    @error('empresa_telefono_whatsapp')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                  <label for="empresa_rubro" class="form-label">Rubro de la Empresa</label>
                  <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-tag"></i></span>
                      
                      {{-- CAMBIO: Reemplazamos <select> por <input type="text"> --}}
                      <input id="empresa_rubro" 
                            type="text" 
                            name="empresa_rubro" 
                            value="{{ old('empresa_rubro') }}" 
                            class="form-control @error('empresa_rubro') is-invalid @enderror" 
                            placeholder="Ej: Restaurante, Tienda de Ropa, Consultoría">
                            
                  </div>
                  @error('empresa_rubro')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                  @enderror
              </div>
              </div>
              
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                  <i class="fas fa-user-plus me-1"></i> Completar Registro
                </button>
              </div>
              
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="card-footer text-center py-3">
        <a href="{{ route('login') }}">¿Ya tienes una cuenta? Inicia Sesión</a>
    </div>
  </div>
</div>
@endsection