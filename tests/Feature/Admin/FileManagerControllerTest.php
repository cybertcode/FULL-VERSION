<?php

namespace Tests\Feature\Admin;

class FileManagerControllerTest extends AdminTestCase
{
    public function test_admin_puede_ver_gestor_de_archivos(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.files.index'))
            ->assertOk()
            ->assertSee('Archivos del sistema');
    }

    public function test_usuario_sin_permiso_no_puede_ver_gestor(): void
    {
        $this->actingAsUser()
            ->get(route('admin.files.index'))
            ->assertForbidden();
    }

    public function test_lfm_interno_bloqueado_sin_permiso(): void
    {
        $this->actingAsUser()
            ->get(route('unisharp.lfm.show'))
            ->assertForbidden();
    }

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get(route('admin.files.index'))->assertRedirect(route('login'));
    }
}
