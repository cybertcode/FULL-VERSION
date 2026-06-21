<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Setting\UpdateSettingRequest;
use App\Models\Setting;
use App\Services\Admin\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends BaseAdminController
{
    public function __construct(private readonly SettingService $settingService)
    {
        parent::__construct();
    }

    public function index(): View
    {
        $settings = $this->settingService->grouped();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(UpdateSettingRequest $request, string $group): RedirectResponse
    {
        $data = $request->except(['_token', '_method']);

        // Procesar uploads de archivos
        $fileFields = ['site_logo', 'site_logo_dark', 'site_favicon', 'seo_og_image'];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                // Eliminar archivo anterior si existe
                $current = setting($field);
                if ($current && Storage::disk('public')->exists($current)) {
                    Storage::disk('public')->delete($current);
                }

                $data[$field] = $request->file($field)->store('settings', 'public');
            } else {
                // No envió archivo — mantener el valor actual
                unset($data[$field]);
            }
        }

        // Asociar el group a cada key antes de guardar
        $toSave = [];
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );
        }

        $this->settingService->clearCache();

        $this->flashSuccess('Configuración guardada correctamente.');

        return redirect()->route('admin.settings.index');
    }
}
