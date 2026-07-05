<?php

namespace Tests\Feature\Admin;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LfmImageOptimizationTest extends AdminTestCase
{
    public function test_uploaded_image_larger_than_max_dimensions_is_resized(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('foto-grande.jpg', 3000, 3000);

        $response = $this->actingAsSuperAdmin()
            ->post(route('unisharp.lfm.upload', ['type' => 'image']), [
                'upload' => $image,
                'type' => 'Images',
                'working_dir' => '/',
            ]);

        $response->assertOk();

        $files = Storage::disk('public')->allFiles('imagenes');
        $uploaded = collect($files)->first(fn ($f) => str_contains($f, 'foto-grande'));

        $this->assertNotNull($uploaded, 'El archivo no se encontró en el disco tras el upload.');

        [$width, $height] = getimagesize(Storage::disk('public')->path($uploaded));

        $this->assertLessThanOrEqual(2000, $width);
        $this->assertLessThanOrEqual(2000, $height);
    }
}
