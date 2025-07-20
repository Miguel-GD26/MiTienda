<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; background-color: #f9f9f9; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #0d6efd; }
        .content p { margin-bottom: 20px; }
        .button-container { text-align: center; margin: 30px 0; }
        .button { background-color: #0d6efd; color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .footer { text-align: center; font-size: 0.9em; color: #777; margin-top: 20px; }
        .link-small { font-size: 0.8em; word-break: break-all; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Restablecimiento de Contraseña</h1>
        </div>
        
        <div class="content">
            <p>¡Hola, <strong>{{ $userName }}</strong>!</p>
            
            <p>Recibiste este correo electrónico porque recibimos una solicitud de restablecimiento de contraseña para tu cuenta. Haz clic en el botón de abajo para elegir una nueva.</p>
            
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="button">Restablecer Contraseña</a>
            </div>
            
            <p>Este enlace de restablecimiento de contraseña expirará en 60 minutos.</p>
            
            <p>Si no solicitaste un restablecimiento de contraseña, no se requiere ninguna otra acción.</p>
        </div>
        
        <div class="footer">
            <p>Si tienes problemas para hacer clic en el botón "Restablecer Contraseña", copia y pega la siguiente URL en tu navegador web:</p>
            <p class="link-small"><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
            <hr>
            <p>Saludos,<br>El equipo de {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>