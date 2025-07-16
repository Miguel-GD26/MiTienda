@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();

    // Colores y logo por defecto (admin)
    $colorFondo = '#800000';
    $colorTexto = 'text-white';
    $logoPath = asset('assets/img/bg1.png');

    // Si el rol es cliente
    if ($user && $user->hasRole('cliente')) {
        $colorFondo = 'rgb(88, 34, 4)'; // azul oscuro
        $colorTexto = 'text-light';
        $logoPath = asset('assets/img/coffe.png'); // Logo alternativo para cliente
    }

    // Verifica si estás en una de las rutas del submenú Seguridad
    $seguridadActiva = request()->routeIs('usuarios.index') ||
        request()->routeIs('roles.index') ||
        request()->routeIs('categorias.index');
@endphp


<aside class="app-sidebar shadow d-flex flex-column {{ $colorTexto }}" style="min-height: 100vh; background-color: {{ $colorFondo }};">
    <div class="d-flex justify-content-center py-4" style="background-color: {{ $colorFondo }};">
        <img src="{{ $logoPath }}" alt="Logo" style="height: 150px;">
    </div>
    <!-- Sidebar Wrapper -->
    <div class="sidebar-wrapper flex-grow-1 px-2 pt-2">
        <nav class="nav flex-column">
            <!-- Dashboard -->
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ $colorTexto }}">
                    <i class="bi bi-speedometer me-2"></i> Dashboard
                </a>
            </div>

            <!-- Seguridad -->
            @canany(['user-list', 'rol-list'])
                <div class="nav-item mt-3">
                    <a class="nav-link {{ $colorTexto }} d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" href="#seguridadMenu" role="button"
                        aria-expanded="{{ $seguridadActiva ? 'true' : 'false' }}" aria-controls="seguridadMenu">
                        <span><i class="bi bi-shield-lock me-2"></i> Seguridad</span>
                        <i class="bi {{ $seguridadActiva ? 'bi-chevron-up' : 'bi-chevron-down' }}"></i>
                    </a>
                    <div class="collapse ps-4 {{ $seguridadActiva ? 'show' : '' }}" id="seguridadMenu">
                        @can('user-list')
                            <a href="{{ route('usuarios.index') }}"
                                class="nav-link {{ request()->routeIs('usuarios.index') ? 'active-item' : $colorTexto }}">
                                <i class="bi bi-people me-2"></i> Usuarios
                            </a>
                        @endcan

                        @can('rol-list')
                            <a href="{{ route('roles.index') }}"
                                class="nav-link {{ request()->routeIs('roles.index') ? 'active-item' : $colorTexto }}">
                                <i class="bi bi-person-badge me-2"></i> Roles
                            </a>
                        @endcan

                        <a href="{{ route('categorias.index') }}"
                            class="nav-link {{ request()->routeIs('categorias.index') ? 'active-item' : $colorTexto }}">
                            <i class="bi bi-tags me-2"></i> Categorías
                        </a>
                    </div>
                </div>
            @endcanany
        </nav>
    </div>
</aside>