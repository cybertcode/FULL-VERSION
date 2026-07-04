<?php

namespace Tests\Feature\Admin;

class ProfileControllerTest extends AdminTestCase
{
    public function test_usuario_autenticado_ve_su_perfil(): void
    {
        $this->actingAsUser()
            ->get(route('admin.profile.show'))
            ->assertOk()
            ->assertSee('Plain User');
    }

    public function test_usuario_actualiza_su_username(): void
    {
        $this->actingAsUser()
            ->put(route('admin.profile.update'), ['username' => 'plain.user'])
            ->assertRedirect();

        $this->assertSame('plain.user', $this->plainUser->fresh()->username);
    }

    public function test_username_duplicado_es_rechazado(): void
    {
        $this->admin->update(['username' => 'ocupado']);

        $this->actingAsUser()
            ->from(route('admin.profile.show'))
            ->put(route('admin.profile.update'), ['username' => 'ocupado'])
            ->assertSessionHasErrors('username');
    }

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get(route('admin.profile.show'))->assertRedirect(route('login'));
    }
}
