<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Spatie\Activitylog\Models\Activity;

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
                'timezone' => 'America/Lima',
                'default_language' => 'es',
                'date_format' => 'd/m/Y',
                'currency_symbol' => 'S/',
                'currency_decimals' => 2,
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

    public function test_actualizar_via_ajax_responde_json_en_exito(): void
    {
        $this->actingAsSuperAdmin()
            ->putJson(route('admin.settings.update', 'regional'), [
                'timezone' => 'America/Lima',
                'default_language' => 'es',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame('America/Lima', setting('timezone'));
    }

    public function test_actualizar_via_ajax_responde_422_con_errores_por_campo(): void
    {
        $this->actingAsSuperAdmin()
            ->putJson(route('admin.settings.update', 'regional'), [
                'timezone' => 'Zona/Invalida',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['timezone']);
    }

    public function test_actualizar_registra_actividad_con_diff(): void
    {
        $this->actingAsSuperAdmin()
            ->putJson(route('admin.settings.update', 'regional'), ['timezone' => 'America/Lima']);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'configuracion',
            'event' => 'updated',
        ]);
    }

    public function test_activity_log_properties_have_before_after_structure(): void
    {
        $this->actingAsSuperAdmin()
            ->putJson(route('admin.settings.update', 'regional'), ['timezone' => 'America/Bogota']);

        $activity = Activity::where('log_name', 'configuracion')->latest('id')->first();

        $this->assertNotNull($activity);
        $this->assertArrayHasKey('changes', $activity->properties->toArray());
        $this->assertArrayHasKey('timezone', $activity->properties['changes']);
        $this->assertArrayHasKey('before', $activity->properties['changes']['timezone']);
        $this->assertArrayHasKey('after', $activity->properties['changes']['timezone']);
        $this->assertEquals('America/Bogota', $activity->properties['changes']['timezone']['after']);
    }

    public function test_no_registra_actividad_si_no_hubo_cambios(): void
    {
        $this->actingAsSuperAdmin()
            ->putJson(route('admin.settings.update', 'regional'), ['timezone' => 'America/Lima']);

        $countAfterFirst = Activity::where('log_name', 'configuracion')->count();

        // Mismo valor, no debería generar un segundo registro
        $this->actingAsSuperAdmin()
            ->putJson(route('admin.settings.update', 'regional'), ['timezone' => 'America/Lima']);

        $this->assertSame($countAfterFirst, Activity::where('log_name', 'configuracion')->count());
    }

    public function test_exportar_configuracion_descarga_json(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('admin.settings.export'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_importar_configuracion_aplica_valores(): void
    {
        $json = json_encode(['site_name' => 'Sistema Importado', 'currency_symbol' => '$']);
        $file = UploadedFile::fake()->createWithContent('config.json', $json);

        $this->actingAsSuperAdmin()
            ->post(route('admin.settings.import'), ['file' => $file])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame('Sistema Importado', setting('site_name'));
        $this->assertSame('$', setting('currency_symbol'));
    }

    public function test_test_recaptcha_fails_without_secret_key(): void
    {
        $this->actingAsSuperAdmin()
            ->postJson(route('admin.settings.test-recaptcha'))
            ->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_test_recaptcha_reports_invalid_secret_key(): void
    {
        Setting::updateOrCreate(['key' => 'recaptcha_secret_key'], ['value' => 'clave-invalida', 'group' => 'integrations']);

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-secret'],
            ]),
        ]);

        $this->actingAsSuperAdmin()
            ->postJson(route('admin.settings.test-recaptcha'))
            ->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_test_recaptcha_succeeds_when_secret_key_is_accepted(): void
    {
        Setting::updateOrCreate(['key' => 'recaptcha_secret_key'], ['value' => 'clave-valida', 'group' => 'integrations']);

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ]),
        ]);

        $this->actingAsSuperAdmin()
            ->postJson(route('admin.settings.test-recaptcha'))
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_user_without_permission_cannot_test_recaptcha(): void
    {
        $this->actingAsUser()
            ->postJson(route('admin.settings.test-recaptcha'))
            ->assertForbidden();
    }

    public function test_importar_ignora_claves_encriptadas_y_archivos(): void
    {
        $json = json_encode(['mail_password' => 'hackeado', 'site_logo' => 'ruta/falsa.png']);
        $file = UploadedFile::fake()->createWithContent('config.json', $json);

        $this->actingAsSuperAdmin()
            ->post(route('admin.settings.import'), ['file' => $file])
            ->assertOk();

        $this->assertNull(setting('mail_password'));
        $this->assertNull(setting('site_logo'));
    }
}
