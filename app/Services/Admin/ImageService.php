<?php

namespace App\Services\Admin;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Image;

class ImageService
{
    public function store(
        UploadedFile $file,
        string $folder = 'uploads',
        ?string $oldPath = null,
        int $quality = 85,
        ?int $maxWidth = 1200,
        int $maxSizeKb = 5120,
    ): string {
        if ($file->getSize() > $maxSizeKb * 1024) {
            throw new \App\Exceptions\BusinessException(
                "La imagen no debe superar los {$maxSizeKb}KB (" . round($maxSizeKb / 1024, 1) . "MB)."
            );
        }

        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $extension = strtolower($file->getClientOriginalExtension());

        if (\in_array($extension, ['svg', 'ico'], true)) {
            return $file->store($folder, 'public');
        }

        // Guardar temporal
        $tempPath     = $file->store('temp', 'public');
        $absoluteTemp = Storage::disk('public')->path($tempPath);

        // Sanitizar el archivo con GD para eliminar perfiles ICC inválidos
        // que hacen que Spatie/Image lance CouldNotLoadImage
        $this->sanitizeWithGd($absoluteTemp, $extension);

        // Ruta final WebP
        $filename      = pathinfo($tempPath, PATHINFO_FILENAME) . '.webp';
        $finalRelative = $folder . '/' . $filename;
        $absoluteFinal = Storage::disk('public')->path($finalRelative);

        // Asegurar que el directorio destino exista
        if (! is_dir(dirname($absoluteFinal))) {
            mkdir(dirname($absoluteFinal), 0755, true);
        }

        $image = Image::load($absoluteTemp)->quality($quality);

        if ($maxWidth && $image->getWidth() > $maxWidth) {
            $image->width($maxWidth);
        }

        $image->save($absoluteFinal);

        Storage::disk('public')->delete($tempPath);

        return $finalRelative;
    }

    /**
     * Re-escribe el archivo usando GD para eliminar metadatos/perfiles ICC
     * inválidos que corrompen el header y causan CouldNotLoadImage en Spatie.
     */
    private function sanitizeWithGd(string $absolutePath, string $extension): void
    {
        // Suprimir el warning de perfil iCCP — lo corremos igualmente
        $gdImage = @imagecreatefromstring((string) file_get_contents($absolutePath));

        if ($gdImage === false) {
            // Si GD tampoco puede leerlo, dejamos que Spatie falle con su error descriptivo
            return;
        }

        // Preservar transparencia para PNG
        if (\in_array($extension, ['png', 'webp'], true)) {
            imagesavealpha($gdImage, true);
        }

        // Re-guardar en el mismo path — esto elimina el perfil ICC inválido
        match ($extension) {
            'png'  => imagepng($gdImage, $absolutePath, 0),  // sin compresión extra — Spatie lo hará
            'webp' => imagewebp($gdImage, $absolutePath, 100),
            'gif'  => imagegif($gdImage, $absolutePath),
            default => imagejpeg($gdImage, $absolutePath, 100), // JPG/JPEG máxima calidad — Spatie recomprimirá
        };

    }
}
