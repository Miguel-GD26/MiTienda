<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('titulo', 'Sistema')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
    <script>
      document.addEventListener('livewire:load', function () {

          Livewire.onPageExpired((response, message) => {
              // --- AQUÍ ESTÁ LA MAGIA ---

              // 1. Prevenir el alert() por defecto de Livewire
              // Al no hacer nada con 'message' o 'confirm()', evitamos el pop-up.

              // 2. Mostrar tu alerta personalizada.
              // Asumiendo que tu sistema de alertas (dispatch('alert', ...))
              // finalmente dispara un evento de navegador que puedes escuchar.
              // Por ejemplo, si usas Alpine.js con un listener:
              window.dispatchEvent(new CustomEvent('alert', {
                  detail: {
                      type: 'error', // O 'warning'
                      message: 'Tu sesión ha expirado. Serás redirigido al inicio de sesión.'
                  }
              }));
              
              // Si usas Toastr directamente, sería aún más fácil:
              // toastr.error('Tu sesión ha expirado. Serás redirigido al inicio de sesión.', 'Sesión Expirada');

              // 3. Redirigir al login después de un breve retraso.
              // Damos un par de segundos para que el usuario pueda leer la alerta.
              setTimeout(() => {
                  window.location.href = '{{ route("login") }}';
              }, 3000); // 3000 milisegundos = 3 segundos

              return false; // Buena práctica para detener cualquier otra acción.
          });

      });
    </script>
    @stack('scripts')
    
  </body>
</html>