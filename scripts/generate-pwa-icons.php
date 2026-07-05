<?php

/**
 * Genera íconos PWA placeholder (letra inicial sobre fondo de color) vía GD.
 * Reemplazar por el logo real del proyecto cuando esté disponible:
 * ver public/assets/img/pwa/README.md
 */

function makeIcon(int $size, string $letter, string $bgHex, bool $maskable = false): \GdImage
{
    $img = imagecreatetruecolor($size, $size);

    [$r, $g, $b] = sscanf($bgHex, '#%02x%02x%02x');
    $bg = imagecolorallocate($img, $r, $g, $b);
    imagefill($img, 0, 0, $bg);

    $white = imagecolorallocate($img, 255, 255, 255);

    // Maskable icons need safe-zone padding (~20%) since the OS may crop to a circle/squircle.
    $fontSize = $maskable ? (int) ($size * 0.28) : (int) ($size * 0.4);

    $ttfCandidates = [
        'C:\\Windows\\Fonts\\arialbd.ttf',
        'C:\\Windows\\Fonts\\arial.ttf',
    ];
    $ttf = null;
    foreach ($ttfCandidates as $candidate) {
        if (is_file($candidate)) {
            $ttf = $candidate;
            break;
        }
    }

    if ($ttf) {
        $bbox = imagettfbbox($fontSize, 0, $ttf, $letter);
        $textWidth = abs($bbox[4] - $bbox[0]);
        $textHeight = abs($bbox[5] - $bbox[1]);
        $x = (int) (($size - $textWidth) / 2);
        $y = (int) (($size + $textHeight) / 2);
        imagettftext($img, $fontSize, 0, $x, $y, $white, $ttf, $letter);
    } else {
        // No system TTF available: draw a centered square as a generic placeholder mark.
        $pad = (int) ($size * 0.3);
        imagefilledrectangle($img, $pad, $pad, $size - $pad, $size - $pad, $white);
    }

    return $img;
}

$outDir = __DIR__.'/../public/assets/img/pwa';
if (! is_dir($outDir)) {
    mkdir($outDir, 0755, true);
}

$brandColor = '#1340A0';
$letter = 'V';

foreach ([192, 512] as $size) {
    $icon = makeIcon($size, $letter, $brandColor);
    imagepng($icon, "{$outDir}/icon-{$size}.png");
    imagedestroy($icon);
}

// Maskable version at 512 with extra safe-zone padding baked into the letter size.
$maskable = makeIcon(512, $letter, $brandColor, true);
imagepng($maskable, "{$outDir}/icon-512-maskable.png");
imagedestroy($maskable);

echo "Íconos PWA generados en {$outDir}\n";
