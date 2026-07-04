<?php

namespace Tests\Feature\Admin;

class SettingControllerTest extends AdminTestCase
{
    public function test_admin_puede_ver_configuracion(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Configuración del Sistema');
    }

    public function test_usuario_sin_permiso_no_puede_ver_configuracion(): void
    {
        $this->actingAsUser()
            ->get(route('admin.settings.index'))
            ->assertForbidden();
    }

    public function test_super_admin_puede_actualizar_grupo_regional(): void
    {
        $this->actingAsSuperAdmin()
            ->put(route('admin.settings.update', 'regional'), [
                'timezone'            => 'America/Lima',
                'default_language'    => 'es',
                'date_format'         => 'd/m/Y',
                'currency_symbol'     => 'S/',
                'currency_decimals'   => 2,
                'pagination_per_page' => 15,
            ])
            ->assertRedirect();

        $this->assertSame('America/Lima', setting('timezone'));
    }

    public function test_admin_sin_settings_edit_no_puede_actualizar(): void
    {
        // admin solo tiene settings.view
        $this->actingAsAdmin()
            ->put(route('admin.settings.update', 'regional'), ['timezone' => 'UTC'])
            ->assertForbidden();
    }
}
