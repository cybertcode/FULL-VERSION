<?php

namespace Tests\Feature\Admin;

class ActivityLogControllerTest extends AdminTestCase
{
    public function test_admin_puede_ver_auditoria(): void
    {
        activity('usuarios')->causedBy($this->admin)->event('created')->log('Registro de prueba.');

        $this->actingAsAdmin()
            ->get(route('admin.activity.index'))
            ->assertOk()
            ->assertSee('Auditoría del Sistema')
            ->assertSee('Registro de prueba.');
    }

    public function test_usuario_sin_permiso_no_puede_ver_auditoria(): void
    {
        $this->actingAsUser()
            ->get(route('admin.activity.index'))
            ->assertForbidden();
    }

    public function test_filtro_por_modulo(): void
    {
        activity('usuarios')->event('created')->log('Actividad de usuarios.');
        activity('roles')->event('updated')->log('Actividad de roles.');

        $this->actingAsAdmin()
            ->get(route('admin.activity.index', ['modulo' => 'roles']))
            ->assertOk()
            ->assertSee('Actividad de roles.')
            ->assertDontSee('Actividad de usuarios.');
    }

    public function test_filtro_por_evento(): void
    {
        activity('usuarios')->event('created')->log('Evento creado.');
        activity('usuarios')->event('deleted')->log('Evento eliminado.');

        $this->actingAsAdmin()
            ->get(route('admin.activity.index', ['evento' => 'deleted']))
            ->assertOk()
            ->assertSee('Evento eliminado.')
            ->assertDontSee('Evento creado.');
    }

    public function test_export_requiere_permiso_export(): void
    {
        // admin solo tiene viewAny, no export
        $this->actingAsAdmin()
            ->get(route('admin.activity.export.csv'))
            ->assertForbidden();
    }

    public function test_super_admin_puede_exportar_csv(): void
    {
        activity('usuarios')->event('created')->log('Exportable.');

        $response = $this->actingAsSuperAdmin()->get(route('admin.activity.export.csv'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get(route('admin.activity.index'))->assertRedirect(route('login'));
    }
}
