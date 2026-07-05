<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Sanitiza HTML enviado por editores de contenido (campos "richtext" del CMS
 * de Páginas) antes de guardarlo, para que nunca se persista <script>,
 * atributos on*, o URLs con scheme javascript: — se renderiza luego con
 * {!! !!} en las plantillas del frontend público, así que la limpieza debe
 * ocurrir aquí, no confiar en el navegador ni en el editor.
 */
class HtmlSanitizer
{
    public static function clean(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', implode(',', [
            'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', 'ol', 'li',
            'blockquote', 'pre', 'code',
            'a[href|title|target|rel]',
            'img[src|alt|width|height]',
            'table', 'thead', 'tbody', 'tr', 'th', 'td',
            'span[style]', 'div',
        ]));
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
        $config->set('HTML.TargetBlank', false);
        $config->set('CSS.AllowedProperties', ['text-align', 'color', 'background-color']);
        $config->set('Cache.SerializerPath', storage_path('app/htmlpurifier'));

        return (new HTMLPurifier($config))->purify($html);
    }
}
