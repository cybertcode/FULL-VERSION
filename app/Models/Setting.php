<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value', 'group'];

    // Grupos disponibles de configuración
    const GROUP_BRANDING = 'branding';

    const GROUP_SEO = 'seo';

    const GROUP_COMPANY = 'company';

    const GROUP_MAIL = 'mail';

    const GROUP_REGIONAL = 'regional';

    // Claves sensibles que se guardan encriptadas en BD
    const ENCRYPTED_KEYS = [
        'mail_password', 'recaptcha_secret_key',
        'social_google_client_secret', 'social_github_client_secret', 'social_facebook_client_secret',
    ];

    protected function isEncryptedKey(): bool
    {
        return \in_array($this->getAttribute('key'), self::ENCRYPTED_KEYS, true);
    }

    public function getValueAttribute(?string $value): ?string
    {
        if ($value === null || ! $this->isEncryptedKey()) {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return $value;
        }
    }

    public function setValueAttribute(?string $value): void
    {
        $this->attributes['value'] = ($value !== null && $this->isEncryptedKey())
            ? Crypt::encryptString($value)
            : $value;
    }
}
