<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top py-3 shadow-sm">
    <div class="container-fluid">
        <!-- Logo a la izquierda -->
        <a href="{{ route('welcome') }}" class="navbar-brand px-lg-4 m-0">
            <img src="{{ asset('assets/img/MiTienda2.png') }}" alt="Logo" style="height: 80px;">
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
                {{-- Esto probablemente está en un parcial como `layouts/navbar.blade.php` --}}

                @auth
                    @if($misTiendas->isNotEmpty())
                        {{-- Si el cliente tiene tiendas, mostramos un menú desplegable --}}
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle px-3" data-toggle="dropdown">
                                <i class="fas fa-store me-1"></i> Mis Tiendas
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                @foreach($misTiendas as $tienda)
                                    <a href="{{ route('tienda.public.index', $tienda->slug) }}" class="dropdown-item">
                                        {{ $tienda->nombre }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @elseif($returnUrl)
                        {{-- Si no tiene tiendas pero hay una URL de retorno (ej. del carrito), mostramos un enlace simple --}}
                        <a href="{{ $returnUrl }}" class="nav-item nav-link px-3 {{ Request::url() == $returnUrl ? 'active' : '' }}">
                            Tienda
                        </a>

                    @endif
                @endauth
                <a href="{{ route('soporte') }}" class="nav-item nav-link px-3 {{ Route::is('soporte') ? 'active' : '' }}">Soporte</a>
                <a href="{{ route('acerca') }}" class="nav-item nav-link px-3 {{ Route::is('acerca') ? 'active' : '' }}">Acerca de</a>
                
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
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle px-3" data-toggle="dropdown">
                            <i class="fa-solid fa-circle-user"></i> {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right text-capitalize">
                            <a href="{{ route('perfil.edit') }}" class="dropdown-item">
                                <i class="fa-solid fa-user-cog me-2"></i> Mi Perfil</a>
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
                    @if(auth()->user()->hasRole('cliente'))
                        <a href="{{ route('cart.index') }}" class="nav-link position-relative">
                            <i class="fa-solid fa-shopping-cart"></i>
                            @if($cartItemCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $cartItemCount }}
                                    <span class="visually-hidden">items en el carrito</span>
                                </span>
                            @endif
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>

