<?php

namespace App\Enums;

/**
 * Plantillas Blade disponibles para páginas del frontend. Cada proyecto futuro
 * agrega sus propias plantillas aquí (un case + su vista en resources/views/frontend/templates/
 * + su definición de campos) — el admin solo elige de esta lista, nunca escribe
 * nombres de vista a mano.
 */
enum PageTemplate: string
{
    case Standard = 'standard';
    case Landing = 'landing';
    case Contact = 'contact';

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Estándar (título + contenido)',
            self::Landing => 'Landing (hero + secciones)',
            self::Contact => 'Contacto (formulario)',
        };
    }

    /**
     * Vista Blade que renderiza esta plantilla en el frontend.
     */
    public function view(): string
    {
        return match ($this) {
            self::Standard => 'frontend.templates.standard',
            self::Landing => 'frontend.templates.landing',
            self::Contact => 'frontend.templates.contact',
        };
    }

    /**
     * Campos de contenido que esta plantilla necesita — define el formulario
     * dinámico en el admin. Cada campo: [key, label, type: text|textarea|image|url].
     *
     * @return array<int, array{key: string, label: string, type: string}>
     */
    public function fields(): array
    {
        return match ($this) {
            self::Standard => [
                ['key' => 'body', 'label' => 'Contenido', 'type' => 'richtext'],
            ],
            self::Landing => [
                ['key' => 'hero_title', 'label' => 'Título del hero', 'type' => 'text'],
                ['key' => 'hero_subtitle', 'label' => 'Subtítulo del hero', 'type' => 'text'],
                ['key' => 'hero_image', 'label' => 'Imagen del hero', 'type' => 'image'],
                ['key' => 'body', 'label' => 'Contenido adicional', 'type' => 'richtext'],
            ],
            self::Contact => [
                ['key' => 'intro_text', 'label' => 'Texto introductorio', 'type' => 'richtext'],
                ['key' => 'contact_email', 'label' => 'Correo de destino', 'type' => 'text'],
            ],
        };
    }
}
