<?php

namespace Tests\Feature\Admin;

class DashboardControllerTest extends AdminTestCase
{
    public function test_admin_ve_dashboard_con_stats_y_grafica(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Total usuarios')
            ->assertSee('Usuarios registrados por mes')
            ->assertSee('Actividad reciente');
    }

    public function test_usuario_simple_ve_dashboard_sin_stats(): void
    {
        $this->actingAsUser()
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Total usuarios')
            ->assertDontSee('Usuarios registrados por mes');
    }

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }
}
