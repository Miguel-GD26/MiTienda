<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top py-3 shadow-sm">
    <div class="container-fluid">
        <!-- Logo a la izquierda -->
        <a href="{{ route('welcome') }}" class="navbar-brand px-lg-4 m-0">
            <img src="{{ asset('assets/img/MiTienda.png') }}" alt="Logo" style="height: 60px;">
        </a>

        <!-- Botón para móviles -->
        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenedor de enlaces que se empuja a la derecha -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarCollapse">
            <div class="navbar-nav align-items-center">
                <!-- He cambiado 'active-item' por 'active' de Bootstrap -->
                <a href="{{ route('welcome') }}" class="nav-item nav-link px-3 {{ Route::is('welcome') ? 'active' : '' }}">Inicio</a>
                @auth
                @php
                    // --- Lógica Centralizada para Determinar la URL de Retorno para el botón superior ---
                    $firstItem = !empty($cartItems) ? reset($cartItems) : null;
                    // La URL de retorno prioriza la info del carrito, y si no, usa la sesión.
                    $returnUrl = $firstItem ? route('tienda.public.index', $firstItem['tienda_slug']) : session('url.store_before_login');
                @endphp

                <!-- <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle px-3" data-toggle="dropdown">Categorías</a>
                    <div class="dropdown-menu dropdown-menu-right text-capitalize">
                        <a href="{{ route('categorias.list') }}" class="dropdown-item">Todas las Categorías</a>
                        <a href="#" class="dropdown-item">Testimonial</a>
                    </div>
                </div> -->
                    @if($returnUrl)
                        <a href="{{ $returnUrl }}" class="nav-item nav-link px-3 {{ Request::url() == $returnUrl ? 'active' : '' }}">
                            Tienda
                        </a>    
                    @endif
                @endauth
                <a href="{{ route('soporte') }}" class="nav-item nav-link px-3 {{ Route::is('soporte') ? 'active' : '' }}">Soporte</a>
                <a href="#" class="nav-item nav-link px-3">Acerca de</a>
                
                <!-- Separador opcional para dar espacio antes del botón -->
                <div class="mx-lg-2"></div>

                <!-- Bloque de Login / Usuario -->
                
                @guest
                    {{-- Forzamos el color azul y el color del borde con un estilo en línea --}}
                    <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}" 
                    class="btn rounded-pill my-2 my-lg-0 px-3 text-white" 
                    style="background-color:rgb(11, 50, 108); border-color:rgb(7, 27, 57);">
                        <i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión
                    </a>
                @endguest

                @auth
                    {{-- Si el usuario SÍ ha iniciado sesión, se muestra el menú --}}
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle px-3" data-toggle="dropdown">
                            <i class="fa-solid fa-circle-user"></i> {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right text-capitalize">
                            <a href="{{ route('perfil.edit') }}" class="dropdown-item">Mi Perfil</a>
                            @if(auth()->user()->hasRole('cliente'))
                            <li>
                                <a class="dropdown-item" href="{{ route('pedidos.index') }}">
                                    <i class="fa-solid fa-receipt me-2"></i> Mis Pedidos
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                        @endif
                            <div class="dropdown-divider"></div>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
                <!-- @auth
                    @if(auth()->user()->hasRole('cliente'))
                    <a href="{{ route('cart.index') }}" class="nav-link position-relative">
                        <i class="fa-solid fa-shopping-cart"></i>
                        @if(count(session('cart', [])) > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ count(session('cart', [])) }}
                        </span>
                        @endif
                    </a>
                    @endif
                @endauth -->
                @auth
                    @if(auth()->user()->hasRole('cliente'))
                        {{-- 
                        Lógica condicional: si estamos en una página de tienda, el enlace al carrito
                        incluirá el slug de esa tienda. La variable $tienda viene del controlador
                        de la página de la tienda.
                        --}}
                        @if(isset($tienda))
                            <a href="{{ route('cart.index', ['empresa' => $tienda]) }}" class="nav-link position-relative">
                                <i class="fa-solid fa-shopping-cart"></i>
                                @if(count(session('cart', [])) > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ count(session('cart', [])) }}
                                </span>
                                @endif
                            </a>
                        @else
                            {{-- Si no estamos en una página de tienda, el enlace al carrito no pasa parámetros --}}
                            <a href="{{ route('cart.index') }}" class="nav-link position-relative">
                                <i class="fa-solid fa-shopping-cart"></i>
                                @if(count(session('cart', [])) > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ count(session('cart', [])) }}</span>
                                @endif
                            </a>
                        @endif
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>