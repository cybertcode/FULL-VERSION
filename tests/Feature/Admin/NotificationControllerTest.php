<?php

namespace Tests\Feature\Admin;

use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class NotificationControllerTest extends AdminTestCase
{
    protected function notifyUser($user, string $title = 'Aviso de prueba', ?string $url = null): string
    {
        $user->notify(new SystemNotification(title: $title, message: 'Contenido del aviso.', url: $url));

        return $user->notifications()->latest()->first()->id;
    }

    public function test_usuario_ve_sus_notificaciones(): void
    {
        $this->notifyUser($this->plainUser, 'Notificación propia');

        $this->actingAsUser()
            ->get(route('admin.notifications.index'))
            ->assertOk()
            ->assertSee('Notificación propia');
    }

    public function test_filtro_no_leidas(): void
    {
        $idLeida = $this->notifyUser($this->plainUser, 'Ya leída');
        $this->plainUser->notifications()->find($idLeida)->markAsRead();
        $this->notifyUser($this->plainUser, 'Pendiente');

        $response = $this->actingAsUser()
            ->get(route('admin.notifications.index', ['filtro' => 'no-leidas']))
            ->assertOk()
            ->assertSee('Pendiente');

        // La navbar muestra las últimas 8 (leídas o no) — la leída debe aparecer
        // solo ahí (1 vez) y no en el listado filtrado
        $this->assertSame(1, substr_count($response->getContent(), 'Ya leída'));
    }

    public function test_marcar_leida_redirige_a_url(): void
    {
        $id = $this->notifyUser($this->plainUser, 'Con enlace', '/admin/mi-perfil');

        $this->actingAsUser()
            ->get(route('admin.notifications.read', $id))
            ->assertRedirect('/admin/mi-perfil');

        $this->assertNotNull($this->plainUser->notifications()->find($id)->read_at);
    }

    public function test_marcar_todas_como_leidas(): void
    {
        $this->notifyUser($this->plainUser);
        $this->notifyUser($this->plainUser);

        $this->actingAsUser()
            ->from(route('admin.notifications.index'))
            ->post(route('admin.notifications.read-all'))
            ->assertRedirect(route('admin.notifications.index'));

        $this->assertSame(0, $this->plainUser->unreadNotifications()->count());
    }

    public function test_eliminar_notificacion(): void
    {
        $id = $this->notifyUser($this->plainUser);

        $this->actingAsUser()
            ->from(route('admin.notifications.index'))
            ->delete(route('admin.notifications.destroy', $id));

        $this->assertSame(0, $this->plainUser->notifications()->count());
    }

    public function test_super_admin_can_broadcast_to_all_users(): void
    {
        Notification::fake();

        $this->actingAsSuperAdmin()
            ->post(route('admin.notifications.broadcast'), [
                'title' => 'Aviso general',
                'message' => 'Mantenimiento programado.',
                'audience' => 'all',
            ])
            ->assertRedirect();

        Notification::assertSentTo([$this->superAdmin, $this->admin, $this->plainUser], SystemNotification::class);
    }

    public function test_can_broadcast_to_specific_role_only(): void
    {
        Notification::fake();

        $this->actingAsSuperAdmin()
            ->post(route('admin.notifications.broadcast'), [
                'title' => 'Solo admins',
                'message' => 'Aviso para administradores.',
                'audience' => 'role',
                'role' => 'admin',
            ])
            ->assertRedirect();

        Notification::assertSentTo($this->admin, SystemNotification::class);
        Notification::assertNotSentTo($this->plainUser, SystemNotification::class);
    }

    public function test_broadcast_with_send_email_marks_notification_for_email(): void
    {
        Notification::fake();

        $this->actingAsSuperAdmin()->post(route('admin.notifications.broadcast'), [
            'title' => 'Con email',
            'message' => 'Este va también por correo.',
            'audience' => 'all',
            'send_email' => '1',
        ]);

        Notification::assertSentTo($this->plainUser, SystemNotification::class, function ($notification) {
            return $notification->sendEmail === true && in_array('mail', $notification->via($this->plainUser), true);
        });
    }

    public function test_user_without_permission_cannot_broadcast(): void
    {
        $this->actingAsUser()
            ->post(route('admin.notifications.broadcast'), [
                'title' => 'No autorizado',
                'message' => 'No debería enviarse.',
                'audience' => 'all',
            ])
            ->assertForbidden();
    }

    public function test_no_puede_leer_notificacion_ajena(): void
    {
        $id = $this->notifyUser($this->admin, 'Del admin');

        $this->actingAsUser()
            ->get(route('admin.notifications.read', $id))
            ->assertNotFound();
    }
}
