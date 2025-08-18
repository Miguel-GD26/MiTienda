<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top py-3 shadow-sm">
    <div class="container-fluid">

        <!-- Logo a la izquierda -->
        <a href="{{ $returnUrl ?? route('welcome') }}" class="navbar-brand px-lg-4 m-0">
            <img src="{{ asset('assets/img/MiTienda2.png') }}" alt="Logo" class="navbar-logo">
        </a>

        <!-- VERSIÓN MÓVIL (VISIBLE SÓLO EN CELULAR, FUERA DEL HAMBURGUESA) -->
        <div class="d-flex align-items-center d-lg-none">
            @guest
                <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}" class="nav-link text-white" >
                    <i class="fa-solid fa-right-to-bracket fa-lg"></i> Iniciar Sesión
                </a>
            @endguest

            @auth
                <!-- Menú desplegable del usuario (versión ícono) -->
                <div class="nav-item dropdown ">
                    <a href="#" class="nav-link dropdown-toggle px-3" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-circle-user fa-lg"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end text-capitalize">
                        <div class="px-3 py-2">
                            <span class="d-block">Hola, {{ Auth::user()->name }}</span>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('perfil.edit') }}" class="dropdown-item" ><i class="fa-solid fa-user-cog me-2"></i> Mi Perfil</a>
                        @if(auth()->user()->hasRole('cliente'))
                            <a class="dropdown-item" href="{{ route('cliente.pedidos') }}"><i class="fa-solid fa-receipt me-2"></i> Mis Pedidos</a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión</button>
                        </form>
                    </div>
                </div>

                <!-- Ícono del Carrito de Compras (Livewire) -->
                @if(auth()->user()->hasRole('cliente'))
                    @livewire('cart-counter')
                @endif
            @endauth
        </div>

        <!-- Botón para móviles -->
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>


        <!-- VERSIÓN ESCRITORIO (Y CONTENIDO DEL HAMBURGUESA EN MÓVIL) -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarCollapse">
            <div class="navbar-nav align-items-center">
                <!-- Enlaces de navegación -->
                <a href="{{ route('welcome') }}" class="nav-item nav-link px-3 {{ Route::is('welcome') ? 'active' : '' }}">Inicio</a>
                
                @auth
                    @if($misTiendas->isNotEmpty())
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle px-3" data-bs-toggle="dropdown"><i class="fas fa-store me-1"></i> Mis Tiendas</a>
                            <div class="dropdown-menu dropdown-menu-end">
                                @foreach($misTiendas as $tienda)
                                    <a href="{{ route('tienda.public.index', $tienda->slug) }}" class="dropdown-item">{{ $tienda->nombre }}</a>
                                @endforeach
                            </div>
                        </div>
                    @elseif(isset($returnUrl) && $returnUrl)
                        <a href="{{ $returnUrl }}" class="nav-item nav-link px-3 {{ Request::url() == $returnUrl ? 'active' : '' }}">Tienda</a>
                    @endif
                @endauth
                
                <a href="{{ route('soporte') }}" class="nav-item nav-link px-3 {{ Route::is('soporte') ? 'active' : '' }}">Soporte</a>
                <a href="{{ route('acerca') }}" class="nav-item nav-link px-3 {{ Route::is('acerca') ? 'active' : '' }}">Acerca de</a>
                
                <!-- Separador -->
                <div class="mx-lg-2"></div>

                <div class="d-none d-lg-flex align-items-center">
                    @guest
                        <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}" 
                           class="btn rounded-pill my-2 my-lg-0 px-3 text-white" 
                           style="background-color:rgb(11, 50, 108); border-color:rgb(7, 27, 57);">
                            <i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión
                        </a>
                    @endguest

                    @auth
                        <!-- Menú Usuario (versión con texto) -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle px-3" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-circle-user"></i> {{ Auth::user()->name }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end text-capitalize">
                                 <a href="{{ route('perfil.edit') }}" class="dropdown-item"><i class="fa-solid fa-user-cog me-2"></i> Mi Perfil</a>
                                @if(auth()->user()->hasRole('cliente'))
                                    <a class="dropdown-item" href="{{ route('cliente.pedidos') }}"><i class="fa-solid fa-receipt me-2"></i> Mis Pedidos</a>
                                    <hr class="dropdown-divider">
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Carrito (Livewire) -->
                        @if(auth()->user()->hasRole('cliente'))
                            @livewire('cart-counter')
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>