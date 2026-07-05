<?php

namespace Tests\Feature\Admin;

use App\Enums\PageStatus;
use App\Enums\PageTemplate;
use App\Services\Admin\PageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageServiceHtmlSanitizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regresión: PageService::create()/update() deben sanitizar los campos
     * "richtext" antes de guardar — el contenido se renderiza sin escapar
     * ({!! !!}) en las plantillas del frontend público, así que un <script>
     * guardado tal cual sería XSS almacenado para cualquier visitante.
     */
    public function test_create_strips_script_tags_from_richtext_fields(): void
    {
        $service = app(PageService::class);

        $page = $service->create([
            'title' => 'Página de prueba',
            'template' => PageTemplate::Standard->value,
            'status' => PageStatus::Draft->value,
            'content' => [
                'body' => '<p>Hola <strong>mundo</strong></p><script>alert(document.cookie)</script>',
            ],
        ]);

        $this->assertStringNotContainsString('<script', $page->content['body']);
        $this->assertStringContainsString('<strong>mundo</strong>', $page->content['body']);
    }

    public function test_update_strips_javascript_uri_and_event_handlers(): void
    {
        $service = app(PageService::class);

        $page = $service->create([
            'title' => 'Página de prueba',
            'template' => PageTemplate::Standard->value,
            'status' => PageStatus::Draft->value,
            'content' => ['body' => '<p>Original</p>'],
        ]);

        $updated = $service->update($page, [
            'template' => PageTemplate::Standard->value,
            'content' => [
                'body' => '<img src="x" onerror="alert(1)"><a href="javascript:alert(1)">click</a>',
            ],
        ]);

        $this->assertStringNotContainsString('onerror', $updated->content['body']);
        $this->assertStringNotContainsString('javascript:', $updated->content['body']);
    }

    public function test_non_richtext_fields_are_left_untouched(): void
    {
        $service = app(PageService::class);

        $page = $service->create([
            'title' => 'Contacto',
            'template' => PageTemplate::Contact->value,
            'status' => PageStatus::Draft->value,
            'content' => [
                'intro_text' => '<p>Escríbenos</p>',
                'contact_email' => 'hola@example.com',
            ],
        ]);

        $this->assertSame('hola@example.com', $page->content['contact_email']);
    }
}
