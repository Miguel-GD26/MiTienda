@extends('welcome.app')
@section('titulo', 'Mi Perfil')

@section('contenido')
<main class="app-main">
    <div class="container-contenido">        
        <h2 class="h3 mb-4">
            <i class="fa-solid fa-id-card-clip me-2"></i>
            Editar Mi Perfil
        </h2>
        <div class="row"> 
            <div class="col-md-12">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        @if (session('mensaje'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa-solid fa-circle-check me-2"></i>
                                {{ session('mensaje') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- SECCIÓN DATOS DE CUENTA --}}
                            <h5 class="mb-3 text-primary"><i class="fa-solid fa-user-lock me-2"></i>Datos de tu Cuenta de Acceso</h5>
                            <p class="text-muted small">Usa estos datos para iniciar sesión. Tu nombre se usará en todo el sistema.</p>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nombre Completo</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                     id="name" name="name" value="{{ old('name', $registro->name ?? '') }}" required>
                                     @error('name') <small class="text-danger">{{$message}}</small> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                     id="email" name="email"  value="{{ old('email', $registro->email ?? '') }}" readonly>
                                     @error('email') <small class="text-danger">{{$message}}</small> @enderror
                                </div>
                            </div>
                            @if(!$isSocialUser)
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Nueva Contraseña</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Dejar en blanco para no cambiar">
                                        @error('password') <small class="text-danger">{{$message}}</small> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                        <input type="password" class="form-control"
                                        id="password_confirmation" name="password_confirmation">
                                    </div>
                                </div>
                            @endif
                            {{-- SECCIÓN DATOS DEL CLIENTE --}}
                            @if(isset($cliente))
                                <hr class="my-4">
                                <h5 class="mb-3 text-primary"><i class="fa-solid fa-address-book me-2"></i>Tus Datos de Contacto</h5>
                                <p class="text-muted small">Esta información se usará para tus pedidos y facturación.</p>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="cliente_telefono" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control @error('cliente_telefono') is-invalid @enderror"
                                            id="cliente_telefono" name="cliente_telefono" value="{{ old('cliente_telefono', $cliente->telefono) }}"
                                            placeholder="Tu número de teléfono de contacto">
                                        @error('cliente_telefono') <small class="text-danger">{{$message}}</small> @enderror
                                    </div>
                                    {{-- Aquí puedes añadir más campos de CLIENTE si los necesitas --}}
                                </div>
                            @endif
                                                        
                            <div class="card-footer bg-white text-end border-0 pt-3 px-0">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Actualizar Perfil
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    const itemPerfil = document.getElementById('itemPerfil');
    if (itemPerfil) {
        itemPerfil.classList.add('active');
    }

  const camposTelefono = ['cliente_telefono'];

  camposTelefono.forEach(id => {
    const input = document.getElementById(id);

    if (input) {
      // Al escribir: solo números, máx. 9
      input.addEventListener('input', () => {
        input.value = input.value.replace(/\D/g, '').slice(0, 9);
      });

      // Al pegar: limpiar texto no numérico y limitar a 9
      input.addEventListener('paste', e => {
        e.preventDefault();
        const texto = (e.clipboardData || window.clipboardData).getData('text');
        const limpio = texto.replace(/\D/g, '').slice(0, 9);
        document.execCommand('insertText', false, limpio);
      });
    }
  });
</script>
@endpush