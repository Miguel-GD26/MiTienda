<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conectando...</title>
    <meta http-equiv="refresh" content="8; url={{ request()->fullUrl() }}">
    <style>
        body { background-color: #111827; color: #9ca3af; font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; text-align: center; }
        .container { max-width: 400px; }
        .spinner { width: 50px; height: 50px; border: 5px solid rgba(255, 255, 255, 0.2); border-top-color: #4f46e5; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px; }
        p { margin-bottom: 5px; font-size: 1.1rem; }
        small { font-size: 0.9rem; color: #6b7280; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <h1>Iniciando Servicios</h1>
        <p>Nuestros servidores se están despertando.</p>
        <small>Serás redirigido automáticamente en unos segundos...</small>
    </div>
</body>
</html>