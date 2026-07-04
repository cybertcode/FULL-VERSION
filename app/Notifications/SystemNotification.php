<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notificación genérica del sistema (canal database).
 *
 * Uso:
 *   $user->notify(new SystemNotification(
 *       title: 'Bienvenido al sistema',
 *       message: 'Tu cuenta fue creada correctamente.',
 *       icon: 'tabler-user-check',   // ícono tabler
 *       color: 'success',            // primary|success|info|warning|danger
 *       url: route('admin.profile.show'),
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
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
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
}
