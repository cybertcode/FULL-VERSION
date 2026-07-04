<?php

use App\Models\Setting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;

return new class extends Migration
{
    public function up(): void
    {
        foreach (Setting::ENCRYPTED_KEYS as $key) {
            $setting = Setting::query()->where('key', $key)->first();

            if (! $setting || $setting->getRawOriginal('value') === null) {
                continue;
            }

            $raw = $setting->getRawOriginal('value');

            try {
                Crypt::decryptString($raw);

                continue; // ya está encriptado
            } catch (DecryptException) {
                // texto plano, encriptar
            }

            $setting->value = $raw;
            $setting->save();
        }
    }

    public function down(): void
    {
        foreach (Setting::ENCRYPTED_KEYS as $key) {
            $setting = Setting::query()->where('key', $key)->first();

            if (! $setting) {
                continue;
            }

            $plain = $setting->value; // accessor desencripta
            $setting->setRawAttributes(['key' => $setting->key, 'value' => $plain, 'group' => $setting->group]);
            $setting->save();
        }
    }
};
