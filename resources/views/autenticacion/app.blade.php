<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('titulo', 'Sistema')</title>
    
    <!-- Meta Tags -->
    <meta name="description" content="Sistema." />

    <!-- ================== ESTILOS ESENCIALES ================== -->
    <!-- Fuente personalizada (Opcional, pero bueno para la consistencia) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (para los iconos del formulario) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <!-- Estilos de Livewire -->
    @livewireStyles
    @vite(['resources/css/adminlte.css'])
    <!-- Para cualquier estilo específico de la página -->
    @stack('estilos')
    
  </head>

  <body class="login-page bg-body-secondary">
    

    <div class="login-box">
      @yield('contenido')
    </div>

    <!-- ================== SCRIPTS ESENCIALES ================== -->
    <!-- Scripts de Livewire -->
    @livewireScripts

    <!-- SweetAlert2 (Para las notificaciones) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Bootstrap 5 Bundle (incluye Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    
    <!-- Para cualquier script específico de la página (aquí se inyecta tu sweetalert-listener) -->
    @stack('scripts')
    
  </body>
</html>