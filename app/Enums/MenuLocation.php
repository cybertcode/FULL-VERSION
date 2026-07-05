<?php

namespace App\Enums;

/**
 * Zonas de navegación del frontend donde se puede asignar un menú.
 * Se declaran aquí (no en el panel) porque cada zona corresponde a un lugar
 * fijo en el layout Blade del proyecto (ej. renderMenu(MenuLocation::Header->value)).
 * Agregar una zona nueva = agregar un case aquí + usarla en la vista del layout.
 */
enum MenuLocation: string
{
    case Header = 'header';
    case Footer = 'footer';
    case Sidebar = 'sidebar';

    public function label(): string
    {
        return match ($this) {
            self::Header => 'Encabezado (Header)',
            self::Footer => 'Pie de página (Footer)',
            self::Sidebar => 'Barra lateral (Sidebar)',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Header => 'Navegación principal en la parte superior del sitio.',
            self::Footer => 'Enlaces secundarios y legales en el pie de página.',
            self::Sidebar => 'Menú lateral, usado en layouts con barra lateral.',
        };
    }
}
