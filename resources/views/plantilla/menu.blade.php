@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();

    // 1. Inicializar valores por defecto
    $logoPath = asset('assets/img/mitienda.png'); // Un logo genérico por si nada más aplica
    $colorFondo = '#00406d'; 
    $colorTexto = 'text-white';

    // 2. Comprobar si el usuario tiene una empresa y esa empresa tiene un logo
    if ($user && $user->empresa && $user->empresa->logo_url) {
        // Si hay un logo de empresa, se usa ese.
        $logoPath = cloudinary()->image($user->empresa->logo_url)->toUrl();
    }
    
    
    // 4. Lógica para mantener el menú activo (sin cambios)
    $gestionActiva = request()->routeIs([
        'productos.index', 'productos.create', 'productos.edit',
        'categorias.index', 'categorias.create', 'categorias.edit',
        'pedidos.index',
    ]);
    
    $seguridadActiva = request()->routeIs([
        'usuarios.index', 'usuarios.create', 'usuarios.edit',
        'roles.index', 'roles.create', 'roles.edit',
        'permisos.index',
    ]);

@endphp

<aside class="app-sidebar shadow d-flex flex-column {{ $colorTexto }}" style="min-height: 100vh; background-color: {{ $colorFondo }};">
    <div class="d-flex justify-content-center py-4" style="background-color: {{ $colorFondo }};">
        <img src="{{ $logoPath }}" alt="Logo de la Empresa" style="height: 150px; max-width: 200px; object-fit: contain;">
    </div>

    <div class="sidebar-wrapper flex-grow-1 px-2 pt-2">
        <nav class="nav flex-column">
            <!-- Dashboard -->
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ $colorTexto }}">
                    <i class="fa-solid fa-gauge-high me-2"></i> Dashboard
                </a>
            </div>

             <!-- Menú de Administración (antes 'Seguridad') -->
            @canany(['user-list', 'rol-list', 'permission-list'])
                <div class="nav-item mt-3">
                    <a class="nav-link {{ $colorTexto }} d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" href="#adminMenu" role="button"
                        aria-expanded="{{ $seguridadActiva ? 'true' : 'false' }}" aria-controls="adminMenu">
                        <span><i class="fa-solid fa-shield-halved me-2"></i> Administración</span>
                        <i class="fa-solid {{ $seguridadActiva ? 'fa-chevron-up' : 'fa-chevron-down' }}"></i>
                    </a>
                    <div class="collapse ps-4 {{ $seguridadActiva ? 'show' : '' }}" id="adminMenu">
                        @can('user-list')
                            <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs(['usuarios.index','usuarios.create','usuarios.edit']) ? 'active-item' : $colorTexto }}"><i class="fa-solid fa-users me-2"></i> Usuarios</a>
                        @endcan
                        @can('rol-list')
                            <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs(['roles.index','roles.create','roles.edit']) ? 'active-item' : $colorTexto }}"><i class="fa-solid fa-user-tag me-2"></i> Roles</a>
                        @endcan
                        @can('permission-list')
                            <a href="{{ route('permisos.index') }}" class="nav-link {{ request()->routeIs('permisos.index') ? 'active-item' : $colorTexto }}"><i class="fa-solid fa-key me-2"></i> Permisos</a>
                        @endcan
                    </div>
                </div>
            @endcanany
            
            <!-- Menú de Gestión (antes 'Mi Tienda') -->
            @canany(['producto-list', 'categoria-list', 'pedido-list'])
                <div class="nav-item mt-3">
                    <a class="nav-link {{ $colorTexto }} d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" href="#gestionMenu" role="button"
                        aria-expanded="{{ $gestionActiva ? 'true' : 'false' }}" aria-controls="gestionMenu">
                        <span><i class="fa-solid fa-store me-2"></i> Gestión</span>
                        <i class="fa-solid {{ $gestionActiva ? 'fa-chevron-up' : 'fa-chevron-down' }}"></i>
                    </a>
                    <div class="collapse ps-4 {{ $gestionActiva ? 'show' : '' }}" id="gestionMenu">
                        @can('categoria-list')
                             <a href="{{ route('categorias.index') }}" class="nav-link {{ request()->routeIs(['categorias.index','categorias.create','categorias.edit']) ? 'active-item' : $colorTexto }}"><i class="fa-solid fa-tags me-2"></i> Categorías</a>
                        @endcan
                        
                        @can('producto-list')
                            <a href="{{ route('productos.index') }}" class="nav-link {{ request()->routeIs(['productos.index','productos.create','productos.edit']) ? 'active-item' : $colorTexto }}"><i class="fa-solid fa-box-open me-2"></i> Productos</a>
                        @endcan

                        @can('pedido-list')
                            <a href="{{ route('pedidos.index') }}" class="nav-link {{ request()->routeIs('pedidos.index') ? 'active-item' : $colorTexto }}"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Pedidos</a>
                        @endcan
                        
                        @if(auth()->user()->hasRole('super_admin'))
                            <a href="#" class="nav-link {{ $colorTexto }}" data-bs-toggle="modal" data-bs-target="#selectEmpresaModal">
                                <i class="fa-solid fa-share-nodes me-2"></i> Compartir / Ver Tienda
                            </a>
                        @else
                            <a href="#" class="nav-link {{ $colorTexto }}" data-bs-toggle="modal" data-bs-target="#shareLinkModal">
                                <i class="fa-solid fa-share-nodes me-2"></i> Compartir Tienda
                            </a>

                            <a href="{{ route('tienda.public.index', auth()->user()->empresa) }}" target="_blank" class="nav-link {{ $colorTexto }}">
                                <i class="fa-solid fa-eye me-2"></i> Ver Tienda
                            </a>
                        @endif
                    </div>
                </div>
            @endcanany

        </nav>
    </div>
</aside>