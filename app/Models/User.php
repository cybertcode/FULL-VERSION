<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Traits\HasActive;
use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string|null      $avatar
 * @property string           $profile_photo_url
 * @property string           $avatar_url
 * @property UserStatus|null  $status
 * @property string           $name
 * @property string|null      $username
 * @property string           $email
 * @property string|null      $phone
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasRoles;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
    use HasActive;
    use HasFilters;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'status',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected array $searchable = ['name', 'username', 'email', 'phone'];

    protected string $defaultSort = 'created_at';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => UserStatus::class,
        ];
    }

    public function perfil(): HasOne
    {
        return $this->hasOne(Perfil::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }

        return $this->profile_photo_url;
    }
}
