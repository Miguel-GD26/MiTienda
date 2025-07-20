<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    /**
     * El constructor ahora acepta el token de restablecimiento.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Construye el mensaje de correo usando una vista de Blade.
     */
    public function toMail($notifiable): MailMessage
    {
        // Generamos la URL que el usuario usará para restablecer su contraseña.
        $resetUrl = route('password.reset', $this->token);

        return (new MailMessage)
                    ->subject('Restablecer tu Contraseña')
                    ->markdown('emails.password_reset_link', [
                        'resetUrl' => $resetUrl,
                        'userName' => $notifiable->name,
                    ]);
    }
}