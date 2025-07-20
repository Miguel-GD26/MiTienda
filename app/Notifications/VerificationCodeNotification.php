<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationCodeNotification extends Notification
{
    use Queueable;

    protected string $code;
    protected string $userName; 

    public function __construct(string $code, string $userName)
    {
        $this->code = $code;
        $this->userName = $userName; 
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Tu Código de Verificación')
                    ->greeting('¡Hola, ' . $this->userName . '!') 
                    ->line('Tu código de verificación es:')
                    ->line(new \Illuminate\Support\HtmlString('<strong style="font-size: 24px; letter-spacing: 5px;">' . $this->code . '</strong>'))
                    ->line('Este código expirará en 10 minutos.')
                    ->line('Si no solicitaste este código, puedes ignorar este mensaje.')
                    ->salutation('Saludos, El equipo de ' . config('app.name'));
    }
}