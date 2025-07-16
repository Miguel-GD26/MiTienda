<nav class="app-header navbar navbar-expand navbar-light bg-white border-bottom">
    <!-- Contenedor del Navbar -->
    <div class="container-fluid">
        <!-- Enlaces de la izquierda -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="fa-solid fa-bars"></i>
                </a>
            </li>
        </ul>
        
        <!-- Enlaces de la derecha -->
        <ul class="navbar-nav ms-auto">
            <!-- Botón de Pantalla Completa -->
            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                    <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                </a>
            </li>
            
            <!-- Menú de Usuario -->
            @auth
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                        <img src="{{ asset('assets/img/login.png') }}" class="brand-image-xl rounded-circle shadow-sm"
                            alt="User Image" width="28" height="28">
                        <span class="d-none d-md-inline ms-2">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                        <li>
                            <a class="dropdown-item" href="{{ route('perfil.edit') }}">
                                <i class="fa-solid fa-user-cog me-2"></i> Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión
                            </a>
                        </li>
                        <form action="{{ route('logout') }}" id="logout-form" method="POST" class="d-none">
                            @csrf
                        </form>
                    </ul>
                </li>
            @endauth
        </ul>
    </div>
</nav>