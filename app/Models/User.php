<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Traits\HasActive;
use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string|null $avatar
 * @property string $profile_photo_url
 * @property string $avatar_url
 * @property UserStatus|null $status
 * @property string $name
 * @property string|null $username
 * @property string $email
 * @property string|null $phone
 * @property int $failed_login_attempts
 * @property Carbon|null $locked_until
 */
class User extends Authenticatable
{
    use HasActive;
    use HasApiTokens;
    use HasFactory;
    use HasFilters;
    use HasProfilePhoto;
    use HasRoles;
    use HasTeams;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'status',
        'avatar',
        'last_login_at',
        'last_login_ip',
        'failed_login_attempts',
        'locked_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'two_factor_remember_token',
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
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }

    public function perfil(): HasOne
    {
        return $this->hasOne(Perfil::class);
    }

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }

        return $this->profile_photo_url;
    }
}
