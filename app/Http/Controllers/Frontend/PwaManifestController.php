<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PwaManifestController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $name = setting('site_name', config('variables.templateName', 'App'));

        return response()->json([
            'name' => $name,
            'short_name' => $name,
            'description' => setting('site_description', config('variables.templateDescription', '')),
            'start_url' => '/',
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#1340A0',
            'icons' => [
                [
                    'src' => asset('assets/img/pwa/icon-192.png'),
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => asset('assets/img/pwa/icon-512.png'),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => asset('assets/img/pwa/icon-512-maskable.png'),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable',
                ],
            ],
        ], 200, ['Content-Type' => 'application/manifest+json']);
    }
}
