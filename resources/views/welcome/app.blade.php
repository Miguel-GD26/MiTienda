<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sistema</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free Website Template" name="keywords">
    <meta content="Free Website Template" name="description">

    
    <!-- Favicon -->
    <link href="{{asset('assets/img/ventas.png')}}" rel="icon">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;400&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <script src="//unpkg.com/alpinejs" defer></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Customized Bootstrap Stylesheet -->

    @stack('estilos')
    @vite(['resources/css/welcome.css'])
    @stack('styles')

    
    @push('scripts')
    <script>
        // Nos aseguramos de que el DOM esté completamente cargado
        document.addEventListener('DOMContentLoaded', function () {
            const copyButton = document.getElementById('copyLinkBtn');
            const linkInput = document.getElementById('storeLinkInput');

            // Solo añadimos el evento si el botón existe en la página
            if (copyButton) {
                copyButton.addEventListener('click', function () {
                    const linkToCopy = linkInput.value;

                    // Usamos la API moderna del Portapapeles (es asíncrona)
                    navigator.clipboard.writeText(linkToCopy).then(function() {
                        // Éxito: Damos feedback al usuario
                        const originalText = copyButton.innerHTML;
                        copyButton.innerHTML = '<i class="bi bi-check-lg me-1"></i> ¡Copiado!';
                        
                        // Volvemos al texto original después de 2 segundos
                        setTimeout(function() {
                            copyButton.innerHTML = originalText;
                        }, 2000);

                    }).catch(function(err) {
                        // Error: Por si algo falla (muy raro en navegadores modernos)
                        console.error('Error al intentar copiar el enlace: ', err);
                        alert('Error al copiar. Por favor, selecciona el texto manualmente.');
                    });
                });
            }
        });
    </script>
    @endpush

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
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Contact Javascript File -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>
    @stack('scripts')
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    
       
</body>

</html>