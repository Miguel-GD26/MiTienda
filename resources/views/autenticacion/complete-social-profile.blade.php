{{-- resources/views/autenticacion/complete-social-profile.blade.php --}}
@extends('autenticacion.app')
@section('titulo', 'Sistema - Completar Registro')

@section('contenido')
<div class="container my-4 mx-auto" style="max-width: 900px;">
  <div class="card shadow" x-data="{ tipoUsuario: '{{ old('tipo_usuario') }}' }">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0 text-center text-md-start">Casi listo, ¡completa tu perfil!</h4>
    </div>
    <div class="card-body p-4">
      <div class="alert alert-info">
          <p class="fw-bold mb-1">Bienvenido, {{ $socialiteData['name'] }}</p>
          <p class="mb-0 small">Tu correo <strong>{{ $socialiteData['email'] }}</strong> se ha verificado con Google. Solo necesitamos unos datos más para crear tu cuenta.</p>
      </div>

      <form action="{{ route('login.google.complete.store') }}" method="POST" class="mt-4">
        @csrf
        <div class="mb-4">
          <label class="form-label fw-bold">¿Qué deseas hacer?</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="tipo_usuario" id="tipo_cliente" value="cliente" x-model="tipoUsuario" {{ old('tipo_usuario') == 'cliente' ? 'checked' : '' }}>
            <label class="form-check-label" for="tipo_cliente">Quiero comprar (Ser Cliente)</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="tipo_usuario" id="tipo_empresa" value="empresa" x-model="tipoUsuario" {{ old('tipo_usuario') == 'empresa' ? 'checked' : '' }}>
            <label class="form-check-label" for="tipo_empresa">Quiero vender (Registrar mi Empresa)</label>
          </div>
          @error('tipo_usuario') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div x-show="tipoUsuario !== ''" x-transition>
          <hr>
          <p class="text-muted small">Los campos con <span class="text-danger">*</span> son obligatorios.</p>

          <div x-show="tipoUsuario === 'cliente'" x-transition>
            <div class="mb-3">
              <label for="cliente_telefono" class="form-label">Teléfono<span class="text-danger">*</span></label>
              <input id="cliente_telefono" type="text" name="cliente_telefono" value="{{ old('cliente_telefono') }}" class="form-control @error('cliente_telefono') is-invalid @enderror" placeholder="9 dígitos">
              @error('cliente_telefono') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
          </div>

          <div x-show="tipoUsuario === 'empresa'" x-transition>
            <h5 class="mb-3">Datos de tu Empresa</h5>
            <div class="mb-3">
              <label for="empresa_nombre" class="form-label">Nombre de la Empresa<span class="text-danger">*</span></label>
              <input id="empresa_nombre" type="text" name="empresa_nombre" value="{{ old('empresa_nombre') }}" class="form-control @error('empresa_nombre') is-invalid @enderror">
              @error('empresa_nombre') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="empresa_telefono_whatsapp" class="form-label">WhatsApp de la Empresa<span class="text-danger">*</span></label>
                <input id="empresa_telefono_whatsapp" type="text" name="empresa_telefono_whatsapp" value="{{ old('empresa_telefono_whatsapp') }}" class="form-control @error('empresa_telefono_whatsapp') is-invalid @enderror" placeholder="9 dígitos">
                @error('empresa_telefono_whatsapp') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="mb-4">
              <label for="empresa_rubro" class="form-label">Rubro<span class="text-danger">*</span></label>
              <input id="empresa_rubro" type="text" name="empresa_rubro" value="{{ old('empresa_rubro') }}" class="form-control @error('empresa_rubro') is-invalid @enderror" placeholder="Ej: Restaurante, Tienda de Ropa">
              @error('empresa_rubro') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>
          </div>
          
          <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="fas fa-check-circle me-1"></i> Finalizar Registro
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection