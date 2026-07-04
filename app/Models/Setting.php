<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
