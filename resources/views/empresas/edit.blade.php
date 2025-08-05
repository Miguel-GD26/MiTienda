{{-- resources/views/empresas/edit.blade.php --}}
@extends('plantilla.app') {{-- O como se llame tu layout de admin --}}

@section('contenido')
<main class="app-main">
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0">Personalizando: {{ $empresa->nombre }}</h2>
            <a href="{{ route('empresas.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver
            </a>
        </div>

        @if(session('mensaje'))
            <div class="alert alert-success">{{ session('mensaje') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('empresas.update', $empresa) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- CARD 1: Información General (Tu código estaba perfecto) --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header"><h5 class="mb-0">Información General</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre de la Empresa</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $empresa->nombre) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="slug" class="form-label">URL (Slug)</label>
                            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $empresa->slug) }}" required>
                        </div>
                        <div class="col-12">
                            <label for="logo_url" class="form-label">Logo de la Empresa (para el Navbar)</label>
                            <input type="file" name="logo_url" id="logo_url" class="form-control">
                            @if($empresa->logo_url)
                                <div class="mt-2">
                                    <img src="{{ cloudinary()->getImage($empresa->logo_url)->toUrl() }}" height="50" class="rounded border p-1">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: Página de Inicio (Carrusel) --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header"><h5 class="mb-0">Página de Inicio (Carrusel)</h5></div>
                <div class="card-body">
                    {{-- Tu código para el carrusel era un buen inicio --}}
                    @for ($i = 0; $i < 2; $i++)
                        <div class="border p-3 mb-2 rounded bg-light">
                            <strong>Slide {{ $i + 1 }}</strong>
                            <div class="row g-2 mt-1">
                                <div class="col-md-6">
                                    <label class="form-label">Título</label>
                                    <input type="text" class="form-control" name="personalizacion[slides][{{ $i }}][titulo]" value="{{ old("personalizacion.slides.$i.titulo", $empresa->personalizacion['slides'][$i]['titulo'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Subtítulo</label>
                                    <input type="text" class="form-control" name="personalizacion[slides][{{ $i }}][subtitulo]" value="{{ old("personalizacion.slides.$i.subtitulo", $empresa->personalizacion['slides'][$i]['subtitulo'] ?? '') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Imagen</label>
                                    <input type="file" class="form-control" name="personalizacion[slides][{{ $i }}][imagen]">
                                    @if($empresa->personalizacion['slides'][$i]['public_id'] ?? null)
                                    <div class="mt-2">
                                        <img src="{{ cloudinary()->getImage($empresa->personalizacion['slides'][$i]['public_id'])->toUrl() }}" height="50" class="rounded">
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- CARD 3: Página "Nosotros" y "Contacto" --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header"><h5 class="mb-0">Páginas "Nosotros" y "Contacto"</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="mision" class="form-label">Misión de la empresa</label>
                        <textarea name="personalizacion[mision]" id="mision" class="form-control" rows="4">{{ old('personalizacion.mision', $empresa->personalizacion['mision'] ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="vision" class="form-label">Visión de la empresa</label>
                        <textarea name="personalizacion[vision]" id="vision" class="form-control" rows="4">{{ old('personalizacion.vision', $empresa->personalizacion['vision'] ?? '') }}</textarea>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label for="email_contacto" class="form-label">Email para recibir mensajes del formulario de contacto</label>
                        <input type="email" name="personalizacion[email_contacto]" id="email_contacto" class="form-control" value="{{ old('personalizacion.email_contacto', $empresa->personalizacion['email_contacto'] ?? '') }}" placeholder="ejemplo@tuempresa.com">
                        <div class="form-text">Este email recibirá las consultas de los clientes.</div>
                    </div>
                </div>
            </div>

            <div class="text-end mb-4">
                <button type="submit" class="btn btn-primary btn-lg px-5">Guardar Personalización</button>
            </div>
        </form>
    </div>
</main>
@endsection