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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Poppins:wght@700&family=Raleway:wght@700&display=swap"
        rel="stylesheet">

    <style>
    .navbar-brand-name {
        font-family: 'Raleway', sans-serif;
        font-weight: 400;
        font-size: 1.2rem;
        letter-spacing: 0.5px;
        margin-left: 12px;
    }


    .navbar-logo {
        height: 60px;
        transition: height 0.2s ease-in-out;
        filter: drop-shadow(0 0 1px white);
    }


    .navbar-nav .nav-link {
        font-weight: 500;
        font-size: 0.95rem;
    }

    @media (min-width: 992px) {
        .navbar-logo {
            height: 80px;
        }
    }

    @media (max-width: 991.98px) {

        #navbarCollapse,
        .navbar-dark .d-lg-none .nav-link {
            color: rgba(255, 255, 255, 1) !important;
            font-weight: normal;
        }
    }

    .navbar {
        background: linear-gradient(175deg, #11998e, #38ef7d);
        transition: background 0.3s ease-in-out;
    }

    /* Links del navbar */
    .navbar .nav-link.active {
        color: #124557ff !important;
        transition: color 0.3s, transform 0.2s;
    }

    .navbar .nav-link:hover {
        color: #043d39ff !important;
        transition: color 0.3s, transform 0.2s;
        transform: scale(1.05);
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <!-- 3. Carga otras librerías INDEPENDIENTES como Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Alpine.js (si lo usas) -->
    <!-- <script src="//unpkg.com/alpinejs" defer></script> -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>