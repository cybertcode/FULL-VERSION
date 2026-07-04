<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Setting\UpdateSettingRequest;
use App\Models\Setting;
use App\Services\Admin\ImageService;
use App\Services\Admin\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SettingController extends BaseAdminController
{
    public function __construct(
        private readonly SettingService $settingService,
        private readonly ImageService $imageService,
    ) {
        parent::__construct();
    }

    public function index(): View
    {
        $this->authorize('settings.view');

        $systemInfo = $this->getSystemInfo();

        return view('admin.settings.index', compact('systemInfo'));
    }

    public function update(UpdateSettingRequest $request, string $group): RedirectResponse|JsonResponse
    {
        $this->authorize('settings.edit');

        $data = $request->except(['_token', '_method']);

        $fileFields = [
            'site_logo' => ['maxWidth' => 600,  'quality' => 90],
            'site_logo_dark' => ['maxWidth' => 600,  'quality' => 90],
            'site_favicon' => ['maxWidth' => 64,   'quality' => 90],
            'seo_og_image' => ['maxWidth' => 1200, 'quality' => 85],
        ];

        foreach ($fileFields as $field => $options) {
            if ($request->hasFile($field)) {
                $data[$field] = $this->imageService->store(
                    file: $request->file($field),
                    folder: 'settings',
                    oldPath: setting($field),
                    quality: $options['quality'],
                    maxWidth: $options['maxWidth'],
                );
            } else {
                unset($data[$field]);
            }
        }

        // Checkboxes — si no vienen en el request, guardar como 0
        $booleanFields = [
            'maintenance_mode', 'force_2fa', 'captcha_enabled', 'registration_enabled',
            'password_require_mixed', 'password_require_numbers', 'password_require_symbols', 'password_check_breach',
            'social_google_enabled', 'social_github_enabled', 'social_facebook_enabled',
        ];
        foreach ($booleanFields as $field) {
            if (\array_key_exists($field, $data)) {
                $data[$field] = $request->boolean($field) ? '1' : '0';
            } elseif ($group === $this->groupOf($field)) {
                $data[$field] = '0';
            }
        }

        if ($group === 'mail') {
            $this->applyMailConfig($data);
        }

        $changes = $this->settingService->save($data, $group);

        if ($changes !== []) {
            activity('configuracion')
                ->causedBy(auth()->user())
                ->withProperties(['group' => $group, 'changes' => $this->maskSensitiveChanges($changes)])
                ->event('updated')
                ->log("Configuración '{$group}' actualizada.");
        }

        if ($request->wantsJson()) {
            $response = response()->json([
                'success' => true,
                'message' => 'Configuración guardada correctamente.',
                'data' => $data,
            ]);

            if ($group === 'appearance' && isset($data['primary_color'])) {
                $response = $response->withCookie(cookie()->forget('admin-primaryColor'));
            }

            return $response;
        }

        $this->flashSuccess('Configuración guardada correctamente.');

        // '_tab' permite que un form comparta el grupo de datos de otro (ej. Login Social
        // reutiliza el grupo 'integrations') pero regrese a su propia pestaña visual.
        $tab = $request->string('_tab')->toString() ?: $group;
        $response = redirect()->route('admin.settings.index', ['tab' => $tab]);

        // Al guardar apariencia, borrar el cookie del customizer de Vuexy para que el
        // color de settings tenga efecto inmediato (el cookie tiene prioridad sobre config)
        if ($group === 'appearance' && isset($data['primary_color'])) {
            $response = $response->withCookie(cookie()->forget('admin-primaryColor'));
        }

        return $response;
    }

    public function testMail(Request $request): JsonResponse
    {
        $this->authorize('settings.testMail');

        $request->validate(['email' => ['required', 'email']]);

        $email = $request->string('email')->toString();

        try {
            Mail::raw(
                'Este es un correo de prueba enviado desde '.setting('site_name', config('app.name')).'. Si lo recibes, la configuración de correo funciona correctamente.',
                static function ($message) use ($email) {
                    $message->to($email)
                        ->subject('Correo de prueba — '.setting('site_name', config('app.name')));
                }
            );

            return response()->json(['success' => true, 'message' => 'Correo enviado correctamente a '.$email]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 422);
        }
    }

    public function runArtisan(Request $request): JsonResponse
    {
        $this->authorize('settings.runArtisan');

        $allowed = [
            'optimize:clear', 'config:cache', 'route:cache', 'view:cache',
            'config:clear', 'cache:clear', 'view:clear', 'route:clear',
        ];

        $command = $request->string('command')->toString();

        if (! \in_array($command, $allowed, true)) {
            return response()->json(['success' => false, 'message' => 'Comando no permitido.'], 422);
        }

        try {
            Artisan::call($command);

            return response()->json(['success' => true, 'message' => "Comando <code>{$command}</code> ejecutado correctamente."]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 422);
        }
    }

    private function getSystemInfo(): array
    {
        $storagePath = storage_path();
        $totalSpace = @disk_total_space($storagePath);
        $freeSpace = @disk_free_space($storagePath);

        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_version' => config('app.version', '1.0.0'),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug') ? 'Activado' : 'Desactivado',
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'db_driver' => config('database.default'),
            'disk_total' => $totalSpace ? $this->formatBytes((int) $totalSpace) : 'N/A',
            'disk_free' => $freeSpace ? $this->formatBytes((int) $freeSpace) : 'N/A',
            'disk_used_pct' => ($totalSpace && $freeSpace)
                ? round((($totalSpace - $freeSpace) / $totalSpace) * 100, 1)
                : 0,
            'server_os' => PHP_OS,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'timezone' => date_default_timezone_get(),
            'locale' => config('app.locale'),
        ];
    }

    private function applyMailConfig(array $data): void
    {
        $map = [
            'mail_driver' => 'mail.default',
            'mail_host' => 'mail.mailers.smtp.host',
            'mail_port' => 'mail.mailers.smtp.port',
            'mail_encryption' => 'mail.mailers.smtp.encryption',
            'mail_username' => 'mail.mailers.smtp.username',
            'mail_password' => 'mail.mailers.smtp.password',
            'mail_from_name' => 'mail.from.name',
            'mail_from_address' => 'mail.from.address',
        ];

        foreach ($map as $settingKey => $configKey) {
            if (isset($data[$settingKey])) {
                config([$configKey => $data[$settingKey]]);
            }
        }
    }

    public function export(): StreamedResponse
    {
        $this->authorize('settings.view');

        $filename = 'configuracion_'.now()->format('Ymd_His').'.json';
        $payload = json_encode($this->settingService->export(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        activity('configuracion')
            ->causedBy(auth()->user())
            ->event('exported')
            ->log('Configuración exportada a JSON.');

        return response()->streamDownload(
            function () use ($payload) {
                echo $payload;
            },
            $filename,
            ['Content-Type' => 'application/json']
        );
    }

    public function import(Request $request): JsonResponse
    {
        $this->authorize('settings.edit');

        $request->validate([
            'file' => ['required', 'file', 'mimes:json', 'max:512'],
        ]);

        $decoded = json_decode($request->file('file')->get(), true);

        if (! \is_array($decoded)) {
            return response()->json(['success' => false, 'message' => 'El archivo no contiene un JSON válido.'], 422);
        }

        $applied = $this->settingService->import($decoded);

        activity('configuracion')
            ->causedBy(auth()->user())
            ->withProperties(['applied' => $applied])
            ->event('imported')
            ->log("Configuración importada desde JSON ({$applied} claves aplicadas).");

        return response()->json([
            'success' => true,
            'message' => "Se aplicaron {$applied} configuraciones correctamente. Recarga la página para verlas.",
        ]);
    }

    /** Enmascara valores de claves sensibles antes de guardarlos en el log de auditoría. */
    private function maskSensitiveChanges(array $changes): array
    {
        foreach (Setting::ENCRYPTED_KEYS as $key) {
            if (isset($changes[$key])) {
                $changes[$key] = ['before' => '••••••', 'after' => '••••••'];
            }
        }

        return $changes;
    }

    private function groupOf(string $field): string
    {
        return match (true) {
            \in_array($field, ['maintenance_mode', 'maintenance_message', 'maintenance_ips'], true) => 'maintenance',
            \in_array($field, [
                'force_2fa', 'captcha_enabled', 'registration_enabled',
                'password_require_mixed', 'password_require_numbers', 'password_require_symbols', 'password_check_breach',
            ], true) => 'security',
            \in_array($field, ['social_google_enabled', 'social_github_enabled', 'social_facebook_enabled'], true) => 'integrations',
            default => '',
        };
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        $count = \count($units) - 1;
        while ($bytes >= 1024 && $i < $count) {
            $bytes = (int) ($bytes / 1024);
            $i++;
        }

        return $bytes.' '.$units[$i];
    }
}
