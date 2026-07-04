<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $ip) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu contraseña ha sido cambiada — '.config('app.name'))
            ->line('Te informamos que la contraseña de tu cuenta acaba de ser cambiada.')
            ->line('Dirección IP: '.$this->ip)
            ->line('Si no reconoces esta acción, contacta de inmediato al administrador del sistema.');
    }
}
