
@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();

    // Valores por defecto (admin)
    $colorFondo = '#8B0000'; // rojo oscuro
    $colorTexto = 'text-white';

    // Si el rol es cliente
    if ($user && $user->hasRole('cliente')) {
        $colorFondo = 'rgb(88, 34, 4)'; // tono café
        $colorTexto = 'text-light';
    }
@endphp

<nav class="app-header navbar navbar-expand navbar navbar-custom {{ $colorTexto }}" style="background-color: {{ $colorFondo }};">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Home</a></li>
            <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Contact</a></li>
        </ul>
        <!--end::Start Navbar Links-->
        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto">
            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                </a>
            </li>
            <!--end::Fullscreen Toggle-->
            <!--begin::User Menu Dropdown-->
            @if(Auth::check())
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                        <img src="{{ asset('assets/img/user2-160x160.jpg') }}" class="rounded-circle shadow"
                            alt="User Image" width="32" height="32">
                        <span class="d-none d-md-inline ms-2">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('perfil.edit') }}">
                                <i class="bi bi-person-fill me-2"></i> Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                            </a>
                        </li>
                        <form action="{{ route('logout') }}" id="logout-form" method="POST" class="d-none">
                            @csrf
                        </form>
                    </ul>
                </li>
            @endif
            <!--end::User Menu Dropdown-->
        </ul>
        <!--end::End Navbar Links-->
    </div>
    <!--end::Container-->
</nav>
