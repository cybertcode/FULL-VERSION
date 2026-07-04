<?php

namespace Tests\Feature\Admin;

class SearchControllerTest extends AdminTestCase
{
    public function test_busca_usuarios_por_nombre(): void
    {
        $response = $this->actingAsAdmin()
            ->getJson(route('admin.search', ['q' => 'Plain User']));

        $response->assertOk();
        $this->assertTrue(
            collect($response->json('results'))->contains(fn ($r) => $r['name'] === 'Plain User')
        );
    }

    public function test_query_corta_devuelve_vacio(): void
    {
        $this->actingAsAdmin()
            ->getJson(route('admin.search', ['q' => 'a']))
            ->assertOk()
            ->assertExactJson(['results' => []]);
    }

    public function test_usuario_sin_permiso_no_recibe_usuarios(): void
    {
        $response = $this->actingAsUser()
            ->getJson(route('admin.search', ['q' => 'Admin User']));

        $response->assertOk();
        $this->assertFalse(
            collect($response->json('results'))->contains(fn ($r) => ($r['section'] ?? '') === 'Usuarios')
        );
    }
}
