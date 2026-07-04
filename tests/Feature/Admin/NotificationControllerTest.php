<?php

namespace Tests\Feature\Admin;

use App\Notifications\SystemNotification;

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

    public function test_no_puede_leer_notificacion_ajena(): void
    {
        $id = $this->notifyUser($this->admin, 'Del admin');

        $this->actingAsUser()
            ->get(route('admin.notifications.read', $id))
            ->assertNotFound();
    }
}
