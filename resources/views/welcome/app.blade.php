<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free Website Template" name="keywords">
    <meta content="Free Website Template" name="description">
    <title>@yield('titulo', 'Sistema')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link href="{{asset('assets/img/ventas.png')}}" rel="icon">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;400&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        .navbar-logo {
            height: 60px;
            transition: height 0.2s ease-in-out; /* Opcional: una suave transición */
        }

        @media (min-width: 992px) {
            .navbar-logo {
                height: 80px;
            }
        }
         @media (max-width: 991.98px) {

        /* 
         * Combina las dos reglas:
         * 1. #navbarCollapse .navbar-nav .nav-link -> Para enlaces DENTRO del menú hamburguesa.
         * 2. .navbar-dark .d-lg-none .nav-link    -> Para los iconos de Usuario y Carrito FUERA del menú.
        */
        #navbarCollapse,
        .navbar-dark .d-lg-none .nav-link {
            /* 
              Este es el color estándar de un enlace inactivo en navbar-dark.
              Usamos !important para asegurar que sobreescriba cualquier otro estilo.
            */
            color: rgba(255, 255, 255, 1) !important; 
            font-weight: normal;
        }
    }
    </style>

    @stack('estilos')
    @vite(['resources/css/welcome.css'])
    @stack('styles')
    @livewireStyles
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- Navbar Start -->
        <div class="container-fluid p-0 nav-bar">
            @include('welcome.navbar')
        </div>
        <!-- Navbar End -->
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid"></div>
            </div>
            @yield('contenido')

        </main>
    </div>
    
    @livewireScripts
    @include('plantilla.partials.sweetalert-listener')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <!-- 2. Carga el BUNDLE de Bootstrap 5. El .bundle incluye Popper.js, necesario para Dropdowns, Tooltips, etc. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- 3. Carga otras librerías INDEPENDIENTES como Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Alpine.js (si lo usas) -->
    <!-- <script src="//unpkg.com/alpinejs" defer></script> -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>  
    @stack('scripts') 
</body>

</html>