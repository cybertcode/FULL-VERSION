<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notificación genérica del sistema (canal database, con email opcional).
 *
 * Uso:
 *   $user->notify(new SystemNotification(
 *       title: 'Bienvenido al sistema',
 *       message: 'Tu cuenta fue creada correctamente.',
 *       icon: 'tabler-user-check',   // ícono tabler
 *       color: 'success',            // primary|success|info|warning|danger
 *       url: route('admin.profile.show'),
 *       sendEmail: true,             // además del in-app, envía por correo
 *   ));
 */
class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $icon = 'tabler-bell',
        public string $color = 'primary',
        public ?string $url = null,
        public bool $sendEmail = false,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->sendEmail ? ['database', 'mail'] : ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'icon' => $this->icon,
            'color' => $this->color,
            'url' => $this->url,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting($this->title)
            ->line($this->message);

        if ($this->url) {
            $mail->action('Ver más', $this->url);
        }

        return $mail;
    }
}
